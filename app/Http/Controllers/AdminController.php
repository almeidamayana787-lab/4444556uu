<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GlobalSetting;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class AdminController extends Controller
{
    // Get all settings
    public function getSettings()
    {
        $settings = GlobalSetting::all()->pluck('value', 'key');
        return response()->json($settings);
    }

    // Update settings (Admin only)
    public function updateSettings(Request $request)
    {
        try {
            $settings = $request->all();
            \Log::info('Updating settings:', $settings);

            foreach ($settings as $key => $value) {
                // Store arrays as JSON strings
                if (is_array($value)) {
                    $value = json_encode($value);
                }

                GlobalSetting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );
            }

            return response()->json(['message' => 'Configurações atualizadas com sucesso']);
        } catch (\Exception $e) {
            \Log::error('Error updating settings: ' . $e->getMessage());
            return response()->json(['message' => 'Erro interno: ' . $e->getMessage()], 500);
        }
    }

    // Upload an image (Backgrounds, Banners, Icons)
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,avif|max:10240', // 10MB max
            'folder' => 'required|string'
        ]);

        $folder = $request->folder; // e.g., 'founde', 'banner', 'casino_icons'

        if ($request->hasFile('image')) {
            try {
                $file = $request->file('image');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = public_path($folder);

                // Ensure directory exists
                if (!File::isDirectory($path)) {
                    File::makeDirectory($path, 0755, true, true);
                }

                // Move directly to public folder
                $file->move($path, $filename);

                return response()->json([
                    'url' => '/' . $folder . '/' . $filename
                ]);
            } catch (\Exception $e) {
                \Log::error('Upload error into folder ' . $folder . ': ' . $e->getMessage());
                return response()->json([
                    'error' => 'Failed to upload image',
                    'message' => $e->getMessage()
                ], 500);
            }
        }

        return response()->json(['error' => 'No image uploaded'], 400);
    }

    // Get current balance from GGPIX
    public function getGGPIXBalance()
    {
        $apiKey = GlobalSetting::where('key', 'ggpix_api_key')->value('value');
        if (!$apiKey)
            return response()->json(['error' => 'GGPIX API Key not set'], 400);

        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders(['X-API-Key' => $apiKey])->get('https://ggpixapi.com/api/v1/balance');
            if ($response->successful()) {
                return response()->json($response->json());
            }
            return response()->json(['error' => 'API Error'], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Request withdrawal of casino profit via GGPIX
    public function withdrawProfit(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'pix_key' => 'required|string',
            'pix_key_type' => 'required|string',
            'recipient_document' => 'required|string'
        ]);

        $apiKey = GlobalSetting::where('key', 'ggpix_api_key')->value('value');
        if (!$apiKey)
            return response()->json(['error' => 'GGPIX API Key not set'], 400);

        $amountCents = (int) ($request->amount * 100);
        $externalId = 'ADMIN_WITH_' . uniqid();

        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'X-API-Key' => $apiKey,
                'Content-Type' => 'application/json'
            ])->post('https://ggpixapi.com/api/v1/pix/out', [
                        'amountCents' => $amountCents,
                        'pixKey' => $request->pix_key,
                        'pixKeyType' => $request->pix_key_type,
                        'recipientDocument' => $request->recipient_document,
                        'externalId' => $externalId,
                        'description' => 'Saque de Lucro Cassino'
                    ]);

            if ($response->successful()) {
                return response()->json(['status' => 'success', 'message' => 'Saque enviado com sucesso!']);
            }
            return response()->json(['error' => 'Erro GGPIX: ' . $response->body()], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Sync RTP: Save arrecadação/distribuição, compute RTP,
     * push to MAX API (control_rtp) and adjust retro game difficulty.
     */
    public function syncRtp(Request $request)
    {
        try {
            $arrecadacao = floatval($request->input('system_arrecadacao', 30));
            $distribuicao = floatval($request->input('system_distribuicao', 70));

            // Persist to global_settings
            GlobalSetting::updateOrCreate(['key' => 'system_arrecadacao'], ['value' => $arrecadacao]);
            GlobalSetting::updateOrCreate(['key' => 'system_distribuicao'], ['value' => $distribuicao]);

            // Calculate RTP percentage
            $total = $arrecadacao + $distribuicao;
            $rtp = $total > 0 ? ($distribuicao / $total) * 100 : 0;

            \Log::info("[RTP Sync] Arrecadação: {$arrecadacao}, Distribuição: {$distribuicao}, RTP: {$rtp}%");

            $results = ['rtp' => round($rtp, 2), 'max_api' => null, 'retro' => []];

            // ── 1. Sync MAX API Games (control_rtp) ──
            $agentCode = GlobalSetting::where('key', 'api_agent_code')->value('value');
            $agentToken = GlobalSetting::where('key', 'api_agent_token')->value('value');

            if ($agentCode && $agentToken) {
                try {
                    $response = \Illuminate\Support\Facades\Http::timeout(15)->post('https://api.maxapi.games/api/v2', [
                        'method' => 'control_rtp',
                        'agent_code' => $agentCode,
                        'agent_token' => $agentToken,
                        'rtp' => round($rtp, 2),
                    ]);

                    $results['max_api'] = [
                        'status' => $response->status(),
                        'success' => $response->successful(),
                        'body' => $response->json() ?? $response->body(),
                    ];
                    \Log::info("[RTP Sync] MAX API response: " . json_encode($results['max_api']));
                } catch (\Exception $e) {
                    $results['max_api'] = ['error' => $e->getMessage()];
                    \Log::warning("[RTP Sync] MAX API error: " . $e->getMessage());
                }
            } else {
                $results['max_api'] = ['skipped' => 'Agent code or token not configured'];
            }

            // ── 2. Adjust Retro Games Difficulty ──
            // Strategy: Scale META_MULTIPLIER inversely to RTP.
            // Base assumption: default RTP ~ 70%, default META_MULTIPLIER values as defined.
            // If admin sets RTP higher (more distribution), make games easier (lower multiplier).
            // If admin sets RTP lower (more collection), make games harder (higher multiplier).
            $baseRtp = 70; // The default baseline RTP
            $rtpRatio = $baseRtp > 0 ? $rtp / $baseRtp : 1;
            // Clamp ratio between 0.3 and 3.0 to avoid extremes
            $rtpRatio = max(0.3, min(3.0, $rtpRatio));
            // For META_MULTIPLIER: inverse relationship (higher RTP -> lower meta -> easier to win)
            $metaScale = 1 / $rtpRatio;

            $retroController = new \App\Http\Controllers\RetroGameController();
            $gameDefinitions = $retroController->getPublicGameDefinitions();

            foreach ($gameDefinitions as $game) {
                if (!isset($game['difficulty_keys']))
                    continue;

                foreach ($game['difficulty_keys'] as $dk) {
                    $key = $dk['key'];
                    $default = $dk['default'];

                    // Only scale META_MULTIPLIER and DIFFICULTY keys
                    if (strpos($key, 'META_MULTIPLIER') !== false || strpos($key, 'DIFFICULTY') !== false) {
                        $newValue = round($default * $metaScale, 2);
                        // Ensure minimum of 1
                        $newValue = max(1, $newValue);

                        GlobalSetting::updateOrCreate(['key' => $key], ['value' => $newValue]);
                        $results['retro'][] = ['key' => $key, 'original' => $default, 'new' => $newValue];
                    }
                }
            }

            \Log::info("[RTP Sync] Retro adjustments: " . json_encode($results['retro']));

            return response()->json([
                'success' => true,
                'message' => "RTP sincronizado: {$results['rtp']}%",
                'details' => $results
            ]);

        } catch (\Exception $e) {
            \Log::error("[RTP Sync] Fatal error: " . $e->getMessage());
            return response()->json(['message' => 'Erro: ' . $e->getMessage()], 500);
        }
    }
}
