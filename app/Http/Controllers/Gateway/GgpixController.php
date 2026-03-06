<?php

namespace App\Http\Controllers\Gateway;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Traits\Gateways\GgpixTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GgpixController extends Controller
{
    use GgpixTrait;

    /**
     * Callback para confirmação de depósito PIX (Pix In).
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function callbackMethod(Request $request)
    {
        try {
            $data = $request->all();
            Log::info('[GgpixController] Webhook recebido', $data);
            DB::table('debug')->insert(['text' => json_encode($data)]);

            // Exemplo de estrutura de payload esperado:
            // { "external_id": "TXID123", "status": "paid", "amount": 10.50 }
            $externalId = $data['external_id'] ?? null;
            $status = $data['status'] ?? null;

            if ($externalId && $status === 'paid') {
                // Localiza a transação pelo txid
                $transaction = Transaction::where('payment_external_reference', $externalId)
                    ->orWhere('txid', $externalId)
                    ->first();

                if ($transaction && $transaction->status !== 'paid') {
                    $transaction->update(['status' => 'paid']);

                    // Creditar saldo ao usuário
                    $user = \App\Models\User::find($transaction->user_id);
                    if ($user) {
                        $user->increment('balance', $transaction->amount);
                        Log::info('[GgpixController] Depósito aprovado para usuário ' . $user->id);
                    }
                }
            }

            return response()->json(['message' => 'OK'], 200);
        } catch (\Exception $e) {
            Log::error('[GgpixController] Erro no webhook: ' . $e->getMessage());
            return response()->json(['message' => 'Erro interno'], 500);
        }
    }

    /**
     * Gera o QR Code PIX para um depósito.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getQRCodePix(Request $request)
    {
        $amount = $request->input('amount');
        $txid = uniqid('GGPIX_', true);
        $user = auth()->user();

        $payer = [
            'name' => $user->name ?? 'Usuário',
            'document' => $user->document ?? '',
        ];

        $result = $this->ggpixCreatePixIn((float) $amount, $txid, $payer);

        return response()->json($result);
    }
}
