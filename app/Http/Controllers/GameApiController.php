<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Provider;
use App\Models\GlobalSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class GameApiController extends Controller
{
    private $apiUrl = 'https://maxapigames.com/api/v2';

    private function getCredentials()
    {
        return [
            'agent_code' => GlobalSetting::where('key', 'api_agent_code')->value('value') ?? '',
            'agent_token' => GlobalSetting::where('key', 'api_agent_token')->value('value') ?? '',
            'agent_secret' => GlobalSetting::where('key', 'api_webhook_secret')->value('value') ?? '',
        ];
    }

    // Test API connection
    public function testConnection(Request $request)
    {
        $agentCode = $request->agent_code;
        $agentToken = $request->agent_token;
        $webhookSecret = $request->webhook_secret;

        // Save credentials
        GlobalSetting::updateOrCreate(['key' => 'api_agent_code'], ['value' => $agentCode]);
        GlobalSetting::updateOrCreate(['key' => 'api_agent_token'], ['value' => $agentToken]);
        GlobalSetting::updateOrCreate(['key' => 'api_webhook_secret'], ['value' => $webhookSecret]);

        try {
            $response = Http::post($this->apiUrl, [
                'method' => 'agent_info',
                'agent_code' => $agentCode,
                'agent_token' => $agentToken,
            ]);

            $data = $response->json();

            if (isset($data['status']) && $data['status'] == 1) {
                return response()->json([
                    'success' => true,
                    'message' => 'Conexão bem-sucedida! Webhook Secret salvo.',
                    'agent' => $data['agent'] ?? null
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $data['msg'] ?? 'Erro desconhecido'
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro de conexão: ' . $e->getMessage()
            ], 500);
        }
    }

    // Fetch games from MAX API and store locally
    public function fetchGames()
    {
        $creds = $this->getCredentials();

        if (!$creds['agent_code'] || !$creds['agent_token']) {
            return response()->json(['success' => false, 'message' => 'Credenciais não configuradas'], 400);
        }

        try {
            // 1. Fetch providers
            $providerResponse = Http::post($this->apiUrl, [
                'method' => 'provider_list',
                'agent_code' => $creds['agent_code'],
                'agent_token' => $creds['agent_token'],
            ]);

            $providerData = $providerResponse->json();

            if (!isset($providerData['status']) || $providerData['status'] != 1) {
                return response()->json(['success' => false, 'message' => $providerData['msg'] ?? 'Erro ao buscar provedores'], 400);
            }

            // Save providers
            foreach ($providerData['providers'] ?? [] as $p) {
                Provider::updateOrCreate(
                    ['code' => $p['code']],
                    ['name' => $p['name'], 'status' => $p['status'] ?? 1]
                );
            }

            // 2. Fetch all games
            $gameResponse = Http::post($this->apiUrl, [
                'method' => 'game_list',
                'agent_code' => $creds['agent_code'],
                'agent_token' => $creds['agent_token'],
            ]);

            $gameData = $gameResponse->json();

            if (!isset($gameData['status']) || $gameData['status'] != 1) {
                return response()->json(['success' => false, 'message' => $gameData['msg'] ?? 'Erro ao buscar jogos'], 400);
            }

            $gamesImported = 0;

            foreach ($gameData['games'] ?? [] as $g) {
                // Download thumbnail if available
                $localBanner = null;
                if (!empty($g['banner'])) {
                    try {
                        $bannerUrl = 'https://maxapigames.com' . $g['banner'];
                        $imgContent = Http::withHeaders(['Referer' => 'https://maxapigames.com'])->get($bannerUrl)->body();

                        $localPath = 'game_banners/' . $g['game_code'] . '.png';
                        $fullDir = public_path('game_banners');
                        if (!is_dir($fullDir)) {
                            mkdir($fullDir, 0755, true);
                        }
                        file_put_contents(public_path($localPath), $imgContent);
                        $localBanner = '/' . $localPath;
                    } catch (\Exception $e) {
                        // Skip banner download errors
                        $localBanner = null;
                    }
                }

                Game::updateOrCreate(
                    ['game_code' => $g['game_code']],
                    [
                        'game_name' => $g['game_name'],
                        'provider_code' => $g['provider_code'] ?? 'Unknown',
                        'banner_url' => $g['banner'] ?? null,
                        'banner_local' => $localBanner,
                        'status' => $g['status'] ?? 1,
                    ]
                );
                $gamesImported++;
            }

            return response()->json([
                'success' => true,
                'message' => "{$gamesImported} jogos importados com sucesso!",
                'total_games' => $gamesImported,
                'providers' => Provider::withCount('games')->get(),
                'games' => Game::all()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro: ' . $e->getMessage()
            ], 500);
        }
    }

    // Delete all imported games and providers
    public function deleteGames()
    {
        Game::truncate();
        Provider::truncate();

        // Clean banner files
        $bannerDir = public_path('game_banners');
        if (is_dir($bannerDir)) {
            array_map('unlink', glob("$bannerDir/*"));
        }

        return response()->json(['success' => true, 'message' => 'Todos os jogos e provedores foram excluídos.']);
    }

    // Set games as popular
    public function setPopular(Request $request)
    {
        $gameIds = $request->game_ids ?? [];

        // Reset all popular 
        Game::where('is_popular', true)->update(['is_popular' => false]);

        // Set selected as popular (max 9)
        Game::whereIn('id', array_slice($gameIds, 0, 9))->update(['is_popular' => true]);

        return response()->json(['success' => true, 'message' => count($gameIds) . ' jogos marcados como populares.']);
    }

    // Set provider as slot with cover image
    public function setSlotProvider(Request $request)
    {
        $providerCode = $request->provider_code;
        $provider = Provider::where('code', $providerCode)->first();

        if (!$provider) {
            return response()->json(['success' => false, 'message' => 'Provedor não encontrado'], 404);
        }

        $provider->is_slot = true;

        if ($request->hasFile('cover_image')) {
            $file = $request->file('cover_image');
            $filename = 'provider_' . strtolower($providerCode) . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('provider_covers'), $filename);
            $provider->cover_image = '/provider_covers/' . $filename;
        }

        $provider->save();

        return response()->json(['success' => true, 'provider' => $provider]);
    }

    // Remove provider from slots
    public function removeSlotProvider(Request $request)
    {
        $providerCode = $request->provider_code;
        Provider::where('code', $providerCode)->update(['is_slot' => false, 'cover_image' => null]);
        return response()->json(['success' => true]);
    }

    // PUBLIC: Get popular games for homepage
    public function getPopularGames()
    {
        $games = Game::where('is_popular', true)->limit(9)->get();
        return response()->json($games);
    }

    // PUBLIC: Get slot providers for homepage
    public function getSlotProviders()
    {
        $providers = Provider::where('is_slot', true)->get();
        return response()->json($providers);
    }

    // PUBLIC: Get all games for a specific provider
    public function getProviderGames($code)
    {
        $provider = Provider::where('code', $code)->first();
        $games = Game::where('provider_code', $code)->get();
        return response()->json([
            'provider' => $provider,
            'games' => $games
        ]);
    }

    // PUBLIC: Get all providers with game count
    public function getAllProviders()
    {
        $providers = Provider::withCount('games')->get();
        return response()->json($providers);
    }

    // PUBLIC: Get all games grouped by provider (for admin listing)
    public function getAllGamesGrouped()
    {
        $providers = Provider::with('games')->withCount('games')->get();
        return response()->json($providers);
    }

    // Proxy game launch to MAX API
    public function launchGame(Request $request)
    {
        $creds = $this->getCredentials();
        $gameCode = $request->game_code;
        $userCode = 'player_' . ($request->user_id ?? auth()->id() ?? rand(1000, 9999));

        try {
            $response = Http::post($this->apiUrl, [
                'method' => 'game_launch',
                'agent_code' => $creds['agent_code'],
                'agent_token' => $creds['agent_token'],
                'user_code' => $userCode,
                'game_code' => $gameCode,
            ]);

            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json(['status' => 0, 'msg' => $e->getMessage()], 500);
        }
    }
}
