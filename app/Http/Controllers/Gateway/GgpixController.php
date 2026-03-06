<?php

namespace App\Http\Controllers\Gateway;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Withdrawal;
use App\Models\AffiliateWithdraw;
use App\Traits\Gateways\GgpixTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GgpixController extends Controller
{
    use GgpixTrait;

    /**
     * Callback para confirmação de transações GGPIX (Pix In, Pix Out).
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function callbackMethod(Request $request)
    {
        try {
            $data = $request->all();
            Log::info('[GgpixController] Webhook recebido', $data);

            $status = $data['status'] ?? null;
            $type = $data['type'] ?? null;
            $transactionId = $data['transactionId'] ?? null;
            $externalId = $data['externalId'] ?? null;

            if ($status === 'COMPLETE' || $status === 'PAID') {
                if ($type === 'PIX_IN') {
                    // Finaliza depósito, garantindo CPA, bônus, e rollover corretamente
                    if (self::finalizePaymentGgpix($transactionId, $externalId)) {
                        return response()->json(['message' => 'Notificação de depósito processada com sucesso'], 200);
                    }
                } elseif ($type === 'PIX_OUT') {
                    // Trata notificação de saque pago. O \App\Traits\Gateways\GgpixTrait::pixCashOutGgpix marcou como status = 1 (processado) previamente.
                    // Aqui você poderia achar a Withdrawal correspondente (pelo externalId) e garantir status = 1 se houver algum tipo de checagem extra.
                    Log::info('[GgpixController] Saque PIX_OUT concluído na GGPIX. TXID: ' . $externalId);
                    return response()->json(['message' => 'Notificação de saque processada com sucesso'], 200);
                }
            }

            return response()->json(['message' => 'Notificação recebida, mas não requer ação imediata.'], 200);

        } catch (\Exception $e) {
            Log::error('[GgpixController] Erro no webhook: ' . $e->getMessage());
            return response()->json(['message' => 'Erro interno'], 500);
        }
    }
}
