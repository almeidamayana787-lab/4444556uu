<?php

namespace App\Http\Controllers;

use App\Models\GlobalSetting;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    private $baseUrl = 'https://ggpixapi.com/api/v1';

    private function getApiKey()
    {
        return GlobalSetting::where('key', 'ggpix_api_key')->value('value');
    }

    /**
     * Create a PIX In (Deposit) charge
     */
    public function createDeposit(Request $request)
    {
        $minDeposit = GlobalSetting::where('key', 'min_deposit')->value('value') ?? 1;

        $request->validate([
            'amount' => 'required|numeric|min:' . $minDeposit,
        ]);

        $user = auth('sanctum')->user();
        if (!$user)
            return response()->json(['error' => 'Unauthenticated'], 401);

        $apiKey = $this->getApiKey();
        if (!$apiKey)
            return response()->json(['error' => 'Payment gateway not configured'], 500);

        // Convert to cents
        $amountCents = (int) ($request->amount * 100);
        $externalId = 'DEP_' . uniqid() . '_' . $user->id;

        try {
            $response = Http::withHeaders([
                'X-API-Key' => $apiKey,
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl . '/pix/in', [
                        'amountCents' => $amountCents,
                        'description' => 'Deposito Cassino - ' . $user->name,
                        'payerName' => $user->name,
                        'payerDocument' => $user->document ?? '00000000000', // Falls back if document not stored
                        'externalId' => $externalId,
                        'webhookUrl' => config('app.url') . '/api/webhook/ggpix'
                    ]);

            if ($response->successful()) {
                $data = $response->json();

                // Track transaction in DB
                // Assuming a Transactions table exists or we just return the data
                return response()->json([
                    'status' => 'success',
                    'pixCode' => $data['pixCode'],
                    'pixCopyPaste' => $data['pixCopyPaste'],
                    'transactionId' => $data['id']
                ]);
            }

            return response()->json(['error' => 'API Error: ' . $response->body()], 400);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Request a PIX Out (Withdrawal)
     */
    public function requestWithdrawal(Request $request)
    {
        $minWithdrawal = GlobalSetting::where('key', 'min_withdrawal')->value('value') ?? 10;

        $request->validate([
            'amount' => 'required|numeric|min:' . $minWithdrawal,
            'pix_key' => 'required|string',
            'pix_key_type' => 'required|string|in:CPF,CNPJ,EMAIL,PHONE,EVP',
        ]);

        $user = auth('sanctum')->user();

        if ($user->rollover_deposit_current < $user->rollover_deposit_target) {
            $diff = $user->rollover_deposit_target - $user->rollover_deposit_current;
            return response()->json(['error' => 'Rollover de depósito pendente. Falta movimentar R$ ' . number_format($diff, 2, ',', '.')], 400);
        }

        if ($user->rollover_bonus_current < $user->rollover_bonus_target) {
            $diff = $user->rollover_bonus_target - $user->rollover_bonus_current;
            return response()->json(['error' => 'Rollover de bônus pendente. Falta movimentar R$ ' . number_format($diff, 2, ',', '.')], 400);
        }

        if ($user->balance < $request->amount) {
            return response()->json(['error' => 'Saldo insuficiente'], 400);
        }

        $apiKey = $this->getApiKey();
        $externalId = 'WITH_' . uniqid() . '_' . $user->id;
        $amountCents = (int) ($request->amount * 100);

        try {
            // Deduct balance immediately (lock funds)
            $user->decrement('balance', $request->amount);

            $payload = [
                'amountCents' => $amountCents,
                'pixKey' => $request->pix_key,
                'pixKeyType' => $request->pix_key_type,
                'externalId' => $externalId,
                'description' => 'Saque Cassino'
            ];

            // If key type requires document, we use user's document
            if ($request->pix_key_type != 'CPF' && $request->pix_key_type != 'CNPJ') {
                $payload['recipientDocument'] = $user->document ?? '00000000000';
            }

            $response = Http::withHeaders([
                'X-API-Key' => $apiKey,
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl . '/pix/out', $payload);

            if ($response->successful()) {
                return response()->json(['status' => 'success', 'message' => 'Saque processando']);
            }

            // Refund if failed
            $user->increment('balance', $request->amount);
            return response()->json(['error' => 'Erro GGPIX: ' . $response->body()], 400);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Handle GGPIX Webhook
     */
    public function handleWebhook(Request $request)
    {
        $signature = $request->header('X-Webhook-Signature');
        $secret = GlobalSetting::where('key', 'ggpix_webhook_secret')->value('value');

        // Basic HMCA validation could be added here as per docs

        $payload = $request->all();
        Log::info('GGPIX Webhook Received', $payload);

        if ($payload['status'] === 'COMPLETE') {
            $externalId = $payload['externalId'] ?? '';

            if (str_starts_with($externalId, 'DEP_')) {
                // Handle Deposit
                $parts = explode('_', $externalId);
                $userId = end($parts);
                $user = User::find($userId);
                if ($user) {
                    $amount = $payload['amount'] / 100;
                    $user->increment('balance', $amount);

                    // Calc Deposit Rollover
                    $depositMul = GlobalSetting::where('key', 'rollover_deposit_multiplier')->value('value') ?? 1;
                    $user->increment('rollover_deposit_target', $amount * $depositMul);

                    // Check for Bonus Rule
                    $rule = \App\Models\DepositBonusRule::where('min_amount', '<=', $amount)
                        ->where('max_amount', '>=', $amount)
                        ->orderBy('bonus_amount', 'desc')
                        ->first();

                    if ($rule && $rule->bonus_amount > 0) {
                        $user->increment('bonus_balance', $rule->bonus_amount);
                        $bonusMul = GlobalSetting::where('key', 'rollover_bonus_multiplier')->value('value') ?? 20;
                        $user->increment('rollover_bonus_target', $rule->bonus_amount * $bonusMul);
                        Log::info("Bônus aplicado para usuario $userId: +{$rule->bonus_amount}");
                    }

                    // Check for invite system completion
                    $invite = \App\Models\Invite::where('referred_id', $user->id)
                        ->where('status', 'pending')
                        ->first();

                    if ($invite) {
                        $invite->increment('referred_deposit_total', $amount);

                        if ($invite->referred_deposit_total >= 20 && $invite->referred_bet_total >= 300) {
                            $invite->status = 'completed';
                            $invite->save();

                            // Optional: Give reward to referrer here if decided later
                            // $referrer = User::find($invite->referrer_id);
                            // $referrer->increment('bonus_balance', REWARD);
                        }
                    }

                    Log::info("Saldo atualizado para usuario $userId: +$amount | Rollover Deposit: +" . ($amount * $depositMul));
                }
            }
        }

        return response()->json(['status' => 'ok']);
    }
}
