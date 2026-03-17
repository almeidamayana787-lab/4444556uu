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
            'webhook_secret' => GlobalSetting::where('key', 'api_webhook_secret')->value('value') ?? '',
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

    // Set games as retro
    public function setRetro(Request $request)
    {
        $gameIds = $request->game_ids ?? [];

        // Reset all retro
        Game::where('is_retro', true)->update(['is_retro' => false]);

        // Set selected as retro
        Game::whereIn('id', $gameIds)->update(['is_retro' => true]);

        return response()->json(['success' => true, 'message' => count($gameIds) . ' jogos marcados como retrô.']);
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

    // Upload provider logo for sidebar
    public function setProviderLogo(Request $request)
    {
        $providerCode = $request->provider_code;
        $provider = Provider::where('code', $providerCode)->first();

        if (!$provider) {
            return response()->json(['success' => false, 'message' => 'Provedor não encontrado'], 404);
        }

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = 'logo_' . strtolower($providerCode) . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('provider_logos'), $filename);
            $provider->logo = '/provider_logos/' . $filename;
            $provider->save();
        }

        return response()->json(['success' => true, 'logo' => $provider->logo]);
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

        $game = Game::where('game_code', $gameCode)->first();
        $providerCode = $game ? $game->provider_code : ($request->provider_code ?? 'PGSOFT');

        $user = auth('sanctum')->user();

        // Demo users don't need balance check for the platform, 
        // they use the demo balance from MAX API
        if (!$user->is_demo && ($user->balance + $user->bonus_balance) <= 0) {
            return response()->json([
                'status' => 0,
                'msg' => 'Saldo insuficiente para iniciar o jogo. Por favor, realize um depósito para desfrutar da experiência completa e concorrer a prêmios reais.'
            ], 200);
        }

        $userId = $user->id;
        $userCode = 'player_' . $userId;

        // Callback URL for game results
        $callbackUrl = config('app.url') . '/api/webhook/game-callback';

        try {
            if (empty($creds['agent_code']) || empty($creds['agent_token'])) {
                return response()->json(['status' => 0, 'msg' => 'Configuração da API incompleta. Contate o suporte.'], 400);
            }

            $payload = [
                'method' => 'game_launch',
                'agent_code' => $creds['agent_code'],
                'agent_token' => $creds['agent_token'],
                'user_code' => $userCode,
                'game_code' => $gameCode,
                'provider_code' => $providerCode,
                'callback_url' => $callbackUrl,
                'is_demo' => $user->is_demo ? 1 : 0,
                'lang' => 'pt'
            ];

            $response = Http::post($this->apiUrl, $payload);
            $data = $response->json();

            // Log launch for debugging if it fails
            if (!isset($data['status']) || $data['status'] == 0) {
                \Log::error('MAX API Launch Error Detail', [
                    'payload' => array_merge($payload, ['agent_token' => '***']),
                    'response' => $data,
                    'status_code' => $response->status(),
                    'body' => $response->body()
                ]);

                if (isset($data['msg']) && $data['msg'] === 'INTERNAL_ERROR') {
                    return response()->json([
                        'status' => 0,
                        'msg' => 'Estamos enfrentando uma instabilidade técnica momentânea com este provedor. Por favor, tente novamente em alguns instantes ou escolha outro jogo.'
                    ]);
                }
            }
            return response()->json($data);
        } catch (\Exception $e) {
            \Log::error('Game Launch Exception', ['message' => $e->getMessage()]);
            return response()->json(['status' => 0, 'msg' => 'Erro de sistema: ' . $e->getMessage()], 500);
        }
    }

    // Webhook for game callbacks (results, balance checks, etc.)
    public function handleCallback(Request $request)
    {
        $data = $request->all();
        \Log::info('MAX API Callback Received', $data);

        $method = $data['method'] ?? '';

        // Security check: verify agent_secret
        $creds = $this->getCredentials();
        $receivedSecret = $data['agent_secret'] ?? '';

        if ($receivedSecret !== ($creds['webhook_secret'] ?? '')) {
            \Log::warning('MAX API Callback: Invalid secret received', [
                'received' => $receivedSecret,
                'expected' => $creds['webhook_secret'] ?? 'NOT_SET'
            ]);
            // Still respond with status 1 to avoid retry loops, but don't process
            return response()->json(['status' => 0, 'msg' => 'Invalid secret']);
        }

        if ($method === 'user_balance') {
            $userCode = $data['user_code'] ?? '';
            $userId = str_replace('player_', '', $userCode);
            $user = \App\Models\User::find($userId);

            if (!$user) {
                \Log::warning('MAX API Callback: User not found', ['user_code' => $userCode]);
                return response()->json([
                    'status' => 1,
                    'user_balance' => 0.00
                ]);
            }

            $response = [
                'status' => 1,
                'user_balance' => (float) ($user->balance + $user->bonus_balance)
            ];
            \Log::info('MAX API Callback: Responding to user_balance', $response);
            return response()->json($response);
        }

        if ($method === 'transaction') {
            $userCode = $data['user_code'] ?? '';
            $userId = str_replace('player_', '', $userCode);
            $user = \App\Models\User::find($userId);

            if (!$user) {
                \Log::error('MAX API Transaction: User not found', ['user_code' => $userCode]);
                return response()->json(['status' => 0, 'msg' => 'User not found']);
            }

            $slot = $data['slot'] ?? [];
            $txnType = $slot['txn_type'] ?? '';
            $bet = (float) ($slot['bet_money'] ?? 0);
            $win = (float) ($slot['win_money'] ?? 0);

            if ($txnType === 'debit' || $txnType === 'debit_credit') {
                if ($bet > 0) {
                    if ($user->balance >= $bet) {
                        $user->balance -= $bet;
                        $user->rollover_deposit_current += $bet;
                    } else {
                        $realBet = $user->balance;
                        $bonusBet = $bet - $realBet;
                        $user->balance = 0;
                        $user->bonus_balance -= $bonusBet;
                        if ($user->bonus_balance < 0) {
                            $user->bonus_balance = 0; // Previne ficar saldo negativo por erros de arredondamento
                        }
                        $user->rollover_deposit_current += $realBet;
                        $user->rollover_bonus_current += $bonusBet;
                    }
                }
            }

            if ($txnType === 'credit' || $txnType === 'debit_credit') {
                if ($win > 0) {
                    $user->balance += $win;
                }
            }

            $user->save();

            $response = [
                'status' => 1,
                'user_balance' => (float) ($user->balance + $user->bonus_balance)
            ];
            \Log::info('MAX API Transaction: Success', $response);
            return response()->json($response);
        }

        return response()->json(['status' => 1]);
    }
}
