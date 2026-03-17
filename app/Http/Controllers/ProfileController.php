<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Get transaction history for "Meus registros"
     */
    public function getHistory(Request $request)
    {
        $user = $request->user();
        $transactions = Transaction::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $transactions
        ]);
    }

    /**
     * Get affiliate/referral stats for "Convide amigos"
     */
    public function getAffiliate(Request $request)
    {
        $user = $request->user();

        // Basic stats for now
        return response()->json([
            'status' => 'success',
            'data' => [
                'referral_code' => $user->id,
                'total_referrals' => 0,
                'total_commissions' => 0.00,
                'referral_url' => url('/register?ref=' . $user->id)
            ]
        ]);
    }

    /**
     * Update account security (password)
     */
    public function updateSecurity(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Senha atual incorreta.'
            ], 400);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Senha alterada com sucesso!'
        ]);
    }

    /**
     * Get Help & FAQ data
     */
    public function getFaq()
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                ['q' => 'Como faço um depósito?', 'a' => 'Você pode fazer um depósito via PIX na seção "Depósito".'],
                ['q' => 'Quanto tempo demora o saque?', 'a' => 'Os saques são processados em até 24 horas úteis.'],
                ['q' => 'Os jogos são justos?', 'a' => 'Sim, todos os nossos jogos utilizam provedores certificados com RNG (Gerador de Números Aleatórios).'],
                ['q' => 'Como convidar amigos?', 'a' => 'Acesse "Convide amigos" e compartilhe seu link exclusivo.'],
            ]
        ]);
    }

    /**
     * Get Support contact info
     */
    public function getSupport()
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'telegram' => 'https://t.me/suporte_exemplo',
                'whatsapp' => 'https://wa.me/5511999999999',
                'email' => 'suporte@exemplo.com'
            ]
        ]);
    }
}
