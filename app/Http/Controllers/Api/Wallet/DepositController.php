<?php

namespace App\Http\Controllers\Api\Wallet;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\Transaction;
use App\Traits\Gateways\DigitoPayTrait;
use App\Traits\Gateways\EzzepayTrait;
use App\Traits\Gateways\BsPayTrait;
use App\Traits\Gateways\GgpixTrait;
use App\Traits\Gateways\SuitpayTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DepositController extends Controller
{
    use SuitpayTrait, DigitoPayTrait, EzzepayTrait, BsPayTrait, GgpixTrait;

    /**
     * @param Request $request
     * @return array|false[]
     */
    public function submitPayment(Request $request)
    {
        Log::info('[DepositController] submitPayment chamado', ['gateway' => $request->gateway, 'all' => $request->all()]);

        $result = null;
        switch ($request->gateway) {
            case 'suitpay':
                // Redirecionamento forcado de Suitpay para GGPIX
                $result = self::requestQrcodeGgpix($request);
                break;
            case 'ezzepay':
                $result = self::requestQrcodeEzze($request);
                break;
            case 'digitopay':
                $result = self::requestQrcodeDigito($request);
                break;
            case 'ggpix':
                $result = self::requestQrcodeGgpix($request);
                break;
            case 'bspay':
                $result = self::requestQrcodeBsPay($request);
                break;
            default:
                Log::warning('[DepositController] Nenhum gateway correspondente', ['gateway' => $request->gateway]);
                // Se o gateway for vazio, vamos tentar usar GGPIX como padrão para não quebrar
                $result = self::requestQrcodeGgpix($request);
                break;
        }

        return response()->json($result);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function consultStatusTransactionPix(Request $request)
    {
        return self::consultStatusTransaction($request);
    }
    /**
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public static function consultStatusTransaction($request)
    {
        $idTransaction = $request->input("idTransaction") ?? $request->input("id") ?? $request->input("transaction_id");

        if (empty($idTransaction)) {
            Log::warning("[DepositController] Polling requested without idTransaction. Content: " . $request->getContent() . " | Headers: " . json_encode($request->header()));
            // Modificado para evitar Bad Request no frontend (isso estava matando a execution e impedindo a UI de aparecer)
            return response()->json(['status' => 'PENDING'], 200);
        }

        Log::info("[DepositController] Polling status for ID: " . $idTransaction);

        // Busca por payment_id (da API do gateway) ou idUnico (gerado internamente)
        $transaction = Transaction::where('payment_id', $idTransaction)
            ->orWhere('idUnico', $idTransaction)
            ->first();

        if ($transaction != null) {
            if ($transaction->status == 1) {
                Log::info("[DepositController] Transaction found and PAID: " . $idTransaction);
                return response()->json(['status' => 'PAID']);
            } else {
                Log::info("[DepositController] Transaction found and PENDING: " . $idTransaction);
                return response()->json(['status' => 'PENDING']);
            }
        }

        Log::info("[DepositController] Transaction NOT FOUND in database for ID: " . $idTransaction);
        return response()->json(['status' => 'NOT_FOUND'], 404);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $deposits = Deposit::whereUserId(auth('api')->id())->paginate();
        return response()->json(['deposits' => $deposits], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
