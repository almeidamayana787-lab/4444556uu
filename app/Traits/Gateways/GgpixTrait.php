<?php

namespace App\Traits\Gateways;

use App\Helpers\Core;
use App\Models\Deposit;
use App\Models\Gateway;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Withdrawal;
use App\Models\AffiliateWithdraw;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

trait GgpixTrait
{
    protected static string $baseUrlGgpix = '';
    protected static string $apiKeyGgpix = '';

    /**
     * Inicializa as credenciais da GGPIX a partir da tabela gateways.
     */
    protected static function generateCredentialsGgpix(): void
    {
        $gateway = Gateway::first();
        if ($gateway) {
            $attrs = $gateway->getAttributes();
            self::$baseUrlGgpix = rtrim($attrs['ggpix_uri'] ?? 'https://ggpixapi.com', '/');
            self::$apiKeyGgpix = $attrs['ggpix_key'] ?? '';
        }
    }

    /**
     * Gera um QR Code PIX para depósito (Pix In).
     *
     * @param $request
     * @return array|\Illuminate\Http\JsonResponse
     */
    public static function requestQrcodeGgpix($request)
    {
        try {
            $setting = Core::getSetting();
            $rules = [
                'amount' => ['required', 'numeric', 'min:' . $setting->min_deposit, 'max:' . $setting->max_deposit],
                'cpf' => ['required', 'string', 'max:255'],
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            self::generateCredentialsGgpix();

            $idUnico = uniqid('GGPIX_', true);
            $amountCents = (int) round(\App\Helpers\Core::amountPrepare($request->amount) * 100);

            $payload = [
                'amountCents' => $amountCents,
                'description' => 'Depósito na plataforma',
                'payerName' => auth('api')->user()->name ?? 'Usuário',
                'payerDocument' => \App\Helpers\Core::soNumero($request->cpf),
                'externalId' => $idUnico,
                'webhookUrl' => url('/ggpix/callback', [], true),
            ];

            // Rastreamento (UTMify), capturando o fallback do header caso disponível.
            $clientIp = $request->ip();
            $userAgent = $request->header('User-Agent');
            if ($clientIp || $userAgent) {
                $payload['tracking'] = [
                    'client_ip' => $clientIp,
                    'client_user_agent' => $userAgent,
                ];
            }

            $response = Http::withHeaders([
                'X-API-Key' => self::$apiKeyGgpix,
                'Content-Type' => 'application/json',
            ])->post(self::$baseUrlGgpix . '/api/v1/pix/in', $payload);

            if ($response->successful()) {
                $responseData = $response->json();

                // Grava as transações locais
                self::generateTransactionGgpix($responseData['id'], \App\Helpers\Core::amountPrepare($request->amount), $idUnico);
                self::generateDepositGgpix($responseData['id'], \App\Helpers\Core::amountPrepare($request->amount));

                return [
                    'status' => true,
                    'idTransaction' => (string) $responseData['id'],
                    'qrcode' => $responseData['pixCopyPaste']
                ];
            }

            Log::error('[GgpixTrait] Erro ao criar Pix In', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'status' => false,
                'error' => $response->json('message') ?? 'Erro ao gerar o QR Code PIX.',
            ];
        } catch (\Exception $e) {
            Log::error('[GgpixTrait] Exceção ao criar Pix In: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    /**
     * Realiza um saque (Pix Out)
     */
    public static function pixCashOutGgpix(array $array): bool
    {
        try {
            self::generateCredentialsGgpix();

            $id = $array['id'] ?? null;
            $tipo = $array['tipo'] ?? null;

            $withdrawal = null;
            if ($tipo === "afiliado") {
                $withdrawal = AffiliateWithdraw::find($id);
            } else {
                $withdrawal = Withdrawal::find($id);
            }

            if (!$withdrawal) {
                return false;
            }

            $amountCents = (int) round($withdrawal->amount * 100);
            $externalId = 'SAQUE_' . uniqid($withdrawal->id . '_', true);

            $digits = function (?string $v) {
                return $v ? preg_replace('/\D+/', '', $v) : null;
            };

            $pixTipo = null;
            $pixKey = null;
            $recipientDocument = null;

            switch ($withdrawal->pix_type) {
                case 'document':
                    $raw = $digits($withdrawal->pix_key);
                    if (strlen($raw) > 11) {
                        $pixTipo = 'CNPJ';
                        $pixKey = $raw;
                        $recipientDocument = $raw;
                    } else {
                        $pixTipo = 'CPF';
                        $pixKey = $raw;
                        $recipientDocument = $raw;
                    }
                    break;
                case 'phoneNumber':
                    $pixTipo = 'PHONE';
                    $pixKey = '+55' . $digits($withdrawal->pix_key);
                    break;
                case 'email':
                    $pixTipo = 'EMAIL';
                    $pixKey = $withdrawal->pix_key;
                    break;
                case 'randomKey':
                    $pixTipo = 'EVP';
                    $pixKey = $withdrawal->pix_key;
                    break;
            }

            $payload = [
                'amountCents' => $amountCents,
                'pixKey' => $pixKey,
                'pixKeyType' => $pixTipo,
                'externalId' => $externalId,
                'description' => 'Saque da Plataforma',
                'webhookUrl' => url('/ggpix/callback', [], true),
            ];

            if ($pixTipo === 'PHONE' || $pixTipo === 'EMAIL' || $pixTipo === 'EVP') {
                // Usuário da QGBet possui 'document' que é o CPF. Se não houver documento na tabela Withdrawal, tentamos localizar no User.
                $user = User::find($withdrawal->user_id);
                $doc = $digits($user->document ?? '');

                if (!empty($doc)) {
                    $payload['recipientDocument'] = $doc;
                } else {
                    Log::error('[GgpixTrait] recipientDocument ausente para o saque PIX Out.');
                    return false;
                }
            }

            $response = Http::withHeaders([
                'X-API-Key' => self::$apiKeyGgpix,
                'Content-Type' => 'application/json',
            ])
                ->timeout(30)
                ->retry(2, 500, null, false)
                ->withOptions(['http_errors' => false])
                ->post(self::$baseUrlGgpix . '/api/v1/pix/out', $payload);

            if ($response->successful()) {
                $responseData = $response->json();

                $withdrawal->update(['status' => 1]); // Status 1 = Processado (Aguardando PIX ou Concluído)
                return true;
            }

            Log::error('[GgpixTrait] Erro ao fazer Pix Out', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return false;

        } catch (\Throwable $e) {
            Log::error('[GgpixTrait] Exceção ao fazer Pix Out: ' . $e->getMessage());
            return false;
        }
    }

    public static function finalizePaymentGgpix($idTransaction, $idUnico): bool
    {
        $transaction = Transaction::where('idUnico', $idUnico)->where('status', 0)->first();
        if (!$transaction) {
            $transaction = Transaction::where('payment_id', $idTransaction)->where('status', 0)->first();
        }

        $setting = \App\Helpers\Core::getSetting();

        if (!empty($transaction)) {
            $user = User::find($transaction->user_id);
            $wallet = \App\Models\Wallet::where('user_id', $transaction->user_id)->first();

            if (!empty($wallet)) {
                $checkTransactions = Transaction::where('user_id', $transaction->user_id)
                    ->where('status', 1)
                    ->count();

                if ($checkTransactions == 0 || empty($checkTransactions)) {
                    $bonus = \App\Helpers\Core::porcentagem_xn($setting->initial_bonus, $transaction->price);
                    $wallet->increment('balance_bonus', $bonus);
                    $wallet->update(['balance_bonus_rollover' => $bonus * $setting->rollover]);
                }

                $configRounds = \App\Models\ConfigRoundsFree::orderBy('value', 'asc')->get();
                foreach ($configRounds as $value) {
                    if ($transaction->price >= $value->value) {
                        $dados = [
                            "username" => $user->email,
                            "game_code" => $value->game_code,
                            "rounds" => $value->spins
                        ];
                        \App\Services\PlayFiverService::RoundsFree($dados);
                        break;
                    }
                }

                $wallet->update(['balance_deposit_rollover' => $transaction->price * intval($setting->rollover_deposit)]);

                if ($wallet->increment('balance', $transaction->price)) {
                    if ($transaction->update(['status' => 1])) {
                        $deposit = Deposit::where('payment_id', $idTransaction)->orWhere('payment_id', $idUnico)->where('status', 0)->first();
                        if (!empty($deposit)) {
                            $affHistoryCPA = \App\Models\AffiliateHistory::where('user_id', $user->id)
                                ->where('commission_type', 'cpa')
                                ->where('status', 0)
                                ->first();

                            if (!empty($affHistoryCPA)) {
                                $sponsorCpa = User::find($user->inviter);
                                if (!empty($sponsorCpa)) {
                                    $deposited_amount = $transaction->price;
                                    if ($affHistoryCPA->deposited_amount >= $sponsorCpa->affiliate_baseline || $deposit->amount >= $sponsorCpa->affiliate_baseline) {
                                        $walletCpa = \App\Models\Wallet::where('user_id', $affHistoryCPA->inviter)->first();
                                        if (!empty($walletCpa)) {
                                            $walletCpa->increment('refer_rewards', $sponsorCpa->affiliate_cpa);
                                            $affHistoryCPA->update([
                                                'status' => 1,
                                                'deposited' => $deposited_amount,
                                                'commission_paid' => $sponsorCpa->affiliate_cpa
                                            ]);
                                            \App\Models\AffiliateLogs::create([
                                                'user_id' => $sponsorCpa->id,
                                                'commission' => $sponsorCpa->affiliate_cpa,
                                                'commission_type' => 'cpa',
                                                'type' => 'increment'
                                            ]);
                                        }
                                    } else {
                                        $affHistoryCPA->update(['deposited_amount' => $transaction->price]);
                                    }
                                }
                            }

                            if ($deposit->update(['status' => 1])) {
                                $admins = User::where('role_id', 0)->get();
                                foreach ($admins as $admin) {
                                    $admin->notify(new \App\Notifications\NewDepositNotification($user->name, $transaction->price));
                                }
                                return true;
                            }
                            return false;
                        }
                        return true; // Se não achar depósito, a transação pelo menos foi confirmada
                    }
                }
                return false;
            }
            return false;
        }
        return false;
    }

    private static function generateDepositGgpix($idTransaction, $amount)
    {
        $userId = auth('api')->user()->id;
        $wallet = \App\Models\Wallet::where('user_id', $userId)->first();

        Deposit::create([
            'payment_id' => $idTransaction,
            'user_id' => $userId,
            'amount' => $amount,
            'type' => 'pix',
            'currency' => $wallet->currency,
            'symbol' => $wallet->symbol,
            'status' => 0
        ]);
    }

    private static function generateTransactionGgpix($idTransaction, $amount, $idUnico)
    {
        $setting = Core::getSetting();

        Transaction::create([
            'payment_id' => $idTransaction,
            'user_id' => auth('api')->user()->id,
            'payment_method' => 'pix',
            'price' => $amount,
            'currency' => $setting->currency_code,
            'status' => 0,
            "idUnico" => $idUnico
        ]);
    }
}
