<?php

namespace App\Traits\Gateways;

use App\Models\Gateway;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

trait GgpixTrait
{
    protected static string $baseUrlGgpix = '';
    protected static string $apiKeyGgpix = '';

    /**
     * Inicializa as credenciais da GGPIX a partir da tabela gateways.
     */
    protected static function initGgpix(): void
    {
        $gateway = Gateway::first();
        if ($gateway) {
            $attrs = $gateway->getAttributes();
            self::$baseUrlGgpix = rtrim($attrs['ggpix_uri'] ?? 'https://api.ggpix.com.br', '/');
            self::$apiKeyGgpix = $attrs['ggpix_key'] ?? '';
        }
    }

    /**
     * Gera um QR Code PIX para depósito (Pix In).
     *
     * @param  float   $amount     Valor em reais (ex: 10.50)
     * @param  string  $txid       Identificador único da transação
     * @param  array   $payer      ['name' => '...', 'document' => '...']
     * @return array               Resposta da GGPIX
     */
    public function ggpixCreatePixIn(float $amount, string $txid, array $payer): array
    {
        self::initGgpix();

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . self::$apiKeyGgpix,
                'Content-Type' => 'application/json',
            ])->post(self::$baseUrlGgpix . '/v1/pix/qrcode', [
                        'amount' => $amount,
                        'external_id' => $txid,
                        'payer' => $payer,
                        'webhook_url' => url('/ggpix/callback'),
                        'description' => 'Depósito na plataforma',
                    ]);

            if ($response->successful()) {
                return [
                    'status' => 'success',
                    'data' => $response->json(),
                ];
            }

            Log::error('[GgpixTrait] Erro ao criar Pix In', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'status' => 'error',
                'message' => $response->json('message') ?? 'Erro ao gerar o QR Code PIX.',
            ];
        } catch (\Exception $e) {
            Log::error('[GgpixTrait] Exceção ao criar Pix In: ' . $e->getMessage());
            return ['status' => 'error', 'message' => 'Erro interno ao processar pagamento.'];
        }
    }

    /**
     * Realiza um Pix Out (saque/transferência).
     *
     * @param  float   $amount     Valor em reais
     * @param  string  $pixKey     Chave PIX do beneficiário
     * @param  string  $keyType    Tipo da chave: 'CPF','CNPJ','EMAIL','TELEFONE','ALEATORIA'
     * @param  string  $txid       Identificador único
     * @return array
     */
    public function ggpixPixOut(float $amount, string $pixKey, string $keyType, string $txid): array
    {
        self::initGgpix();

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . self::$apiKeyGgpix,
                'Content-Type' => 'application/json',
            ])->post(self::$baseUrlGgpix . '/v1/pix/transfer', [
                        'amount' => $amount,
                        'pix_key' => $pixKey,
                        'key_type' => $keyType,
                        'external_id' => $txid,
                    ]);

            if ($response->successful()) {
                return [
                    'status' => 'success',
                    'data' => $response->json(),
                ];
            }

            Log::error('[GgpixTrait] Erro ao fazer Pix Out', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'status' => 'error',
                'message' => $response->json('message') ?? 'Erro ao processar saque PIX.',
            ];
        } catch (\Exception $e) {
            Log::error('[GgpixTrait] Exceção ao fazer Pix Out: ' . $e->getMessage());
            return ['status' => 'error', 'message' => 'Erro interno ao processar saque.'];
        }
    }

    /**
     * Consulta o saldo disponível na GGPIX.
     *
     * @return array
     */
    public function ggpixGetBalance(): array
    {
        self::initGgpix();

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . self::$apiKeyGgpix,
            ])->get(self::$baseUrlGgpix . '/v1/account/balance');

            if ($response->successful()) {
                return ['status' => 'success', 'data' => $response->json()];
            }

            return ['status' => 'error', 'message' => $response->json('message') ?? 'Erro ao consultar saldo.'];
        } catch (\Exception $e) {
            Log::error('[GgpixTrait] Exceção ao consultar saldo: ' . $e->getMessage());
            return ['status' => 'error', 'message' => 'Erro interno ao consultar saldo.'];
        }
    }

    /**
     * Consulta status de uma transação na GGPIX.
     *
     * @param  string  $txid  O ID externo enviado na criação
     * @return array
     */
    public function ggpixGetTransactionStatus(string $txid): array
    {
        self::initGgpix();

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . self::$apiKeyGgpix,
            ])->get(self::$baseUrlGgpix . '/v1/pix/status/' . $txid);

            if ($response->successful()) {
                return ['status' => 'success', 'data' => $response->json()];
            }

            return ['status' => 'error', 'message' => $response->json('message') ?? 'Erro ao consultar status.'];
        } catch (\Exception $e) {
            Log::error('[GgpixTrait] Exceção ao consultar status: ' . $e->getMessage());
            return ['status' => 'error', 'message' => 'Erro interno.'];
        }
    }
}
