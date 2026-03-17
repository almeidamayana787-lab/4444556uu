<?php

namespace App\Http\Controllers;

use App\Models\GlobalSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RetroGameController extends Controller
{
    /**
     * All retro games with their metadata.
     */
    private static function getGameDefinitions(): array
    {
        return [
            [
                'id' => 'subway',
                'name' => 'Subway Money',
                'icon' => '/assets/games/subway/icon.png',
                'banner' => '/assets/games/subway/banner.png',
                'play_url' => '/play/subway/',
                'difficulty_keys' => [
                    ['key' => 'GAME_SUBWAY_REAL_COIN_RATE', 'label' => 'Taxa de moedas (R$)', 'default' => 0.01],
                    ['key' => 'GAME_SUBWAY_REAL_META_MULTIPLIER', 'label' => 'Multiplicador de meta', 'default' => 4],
                    ['key' => 'GAME_SUBWAY_REAL_PLAYER_SPEED', 'label' => 'Velocidade do jogador', 'default' => 60],
                ],
            ],
            [
                'id' => 'angry',
                'name' => 'Angry Cash',
                'icon' => '/assets/games/angry/icon.png',
                'banner' => '/assets/games/angry/banner.png',
                'play_url' => '/play/angry/',
                'difficulty_keys' => [
                    ['key' => 'GAME_ANGRY_REAL_COIN_MULTIPLIER', 'label' => 'Multiplicador de moedas', 'default' => 4],
                    ['key' => 'GAME_ANGRY_REAL_META_MULTIPLIER', 'label' => 'Multiplicador de meta', 'default' => 4],
                    ['key' => 'GAME_ANGRY_REAL_DIFFICULTY', 'label' => 'Dificuldade', 'default' => 1],
                ],
            ],
            [
                'id' => 'jetpack',
                'name' => 'Jetpack Cash',
                'icon' => '/assets/games/jetpack/icon.png',
                'banner' => '/assets/games/jetpack/banner.png',
                'play_url' => '/play/jetpack/',
                'difficulty_keys' => [
                    ['key' => 'GAME_JETPACK_REAL_COIN_RATE', 'label' => 'Taxa de moedas (R$)', 'default' => 0.01],
                    ['key' => 'GAME_JETPACK_REAL_META_MULTIPLIER', 'label' => 'Multiplicador de meta', 'default' => 4],
                    ['key' => 'GAME_JETPACK_REAL_PLAYER_SPEED', 'label' => 'Velocidade do jogador', 'default' => 1000],
                    ['key' => 'GAME_JETPACK_REAL_MISSILE_SPEED', 'label' => 'Velocidade do míssil', 'default' => 2000],
                ],
            ],
            [
                'id' => 'dino',
                'name' => 'Dino Cash',
                'icon' => '/assets/games/dino/icon.png',
                'banner' => '/assets/games/dino/banner.png',
                'play_url' => '/play/dino/',
                'difficulty_keys' => [
                    ['key' => 'GAME_DINO_REAL_COIN_RATE', 'label' => 'Dinheiro/distância (R$)', 'default' => 0.01],
                    ['key' => 'GAME_DINO_REAL_META_MULTIPLIER', 'label' => 'Multiplicador de meta', 'default' => 100],
                    ['key' => 'GAME_DINO_REAL_PLAYER_SPEED', 'label' => 'Velocidade do jogador', 'default' => 15],
                ],
            ],
            [
                'id' => 'candy',
                'name' => 'Candy Cash',
                'icon' => '/assets/games/candy/icon.webp',
                'banner' => '/assets/games/candy/banner.webp',
                'play_url' => '/play/candy/',
                'difficulty_keys' => [
                    ['key' => 'GAME_CANDY_REAL_COIN_RATE', 'label' => 'Taxa de moedas (R$)', 'default' => 0.01],
                    ['key' => 'GAME_CANDY_REAL_META_MULTIPLIER', 'label' => 'Multiplicador de meta', 'default' => 20],
                    ['key' => 'GAME_CANDY_REAL_TIMER', 'label' => 'Tempo de jogo (seg)', 'default' => 30],
                ],
            ],
            [
                'id' => 'pacman',
                'name' => 'Pac-Man Cash',
                'icon' => '/assets/games/pacman/icon.webp',
                'banner' => '/assets/games/pacman/banner.jpg',
                'play_url' => '/play/pacman/',
                'difficulty_keys' => [
                    ['key' => 'GAME_PACMAN_REAL_LIVES', 'label' => 'Vidas', 'default' => 0],
                    ['key' => 'GAME_PACMAN_REAL_COIN_RATE', 'label' => 'Taxa de moedas (R$)', 'default' => 0.01],
                    ['key' => 'GAME_PACMAN_REAL_META_MULTIPLIER', 'label' => 'Multiplicador de meta', 'default' => 10],
                    ['key' => 'GAME_PACMAN_REAL_GHOST_POINTS', 'label' => 'Pontos fantasma (R$)', 'default' => 0.1],
                ],
            ],
            [
                'id' => 'fruit',
                'name' => 'Fruit Cash',
                'icon' => '/assets/games/fruit/icon.webp',
                'banner' => '/assets/games/fruit/banner.webp',
                'play_url' => '/play/fruit/',
                'difficulty_keys' => [
                    ['key' => 'GAME_FRUIT_REAL_META_MULTIPLIER', 'label' => 'Multiplicador de meta', 'default' => 10],
                    ['key' => 'GAME_FRUIT_REAL_DROP_DURATION', 'label' => 'Duração da queda (ms)', 'default' => 600],
                    ['key' => 'GAME_FRUIT_REAL_FRUIT_RATE', 'label' => 'Taxa de frutas', 'default' => 1],
                ],
            ],
        ];
    }

    /**
     * Public accessor for game definitions (used by AdminController for RTP sync).
     */
    public function getPublicGameDefinitions(): array
    {
        return self::getGameDefinitions();
    }

    /**
     * Return the list of all retro games with their current active status.
     */
    public function index()
    {
        $games = self::getGameDefinitions();

        // Check which games are marked as active (shown in Retro section)
        foreach ($games as &$game) {
            $game['is_active'] = GlobalSetting::where('key', 'retro_active_' . $game['id'])->value('value') === '1';
            // Allow custom name/banner overrides
            $customName = GlobalSetting::where('key', 'retro_name_' . $game['id'])->value('value');
            $customBanner = GlobalSetting::where('key', 'retro_banner_' . $game['id'])->value('value');
            if ($customName)
                $game['name'] = $customName;
            if ($customBanner)
                $game['banner'] = $customBanner;

            $game['game_code'] = null; // Hardcoded retro games have no game_code
            $game['is_database_game'] = false;
        }

        // Fetch games from database that are marked as retro
        $dbGames = \App\Models\Game::where('is_retro', true)->get();
        foreach ($dbGames as $dbGame) {
            $games[] = [
                'id' => 'db_' . $dbGame->id,
                'name' => $dbGame->game_name,
                'icon' => $dbGame->banner_local ?? $dbGame->banner_url,
                'banner' => $dbGame->banner_local ?? $dbGame->banner_url,
                'play_url' => null,
                'game_code' => $dbGame->game_code,
                'is_active' => true, // If it's in the DB with is_retro=true, it's active
                'is_database_game' => true,
                'difficulty_keys' => []
            ];
        }

        // Final filter to ensure requested removals are gone (using broad match)
        $games = array_filter($games, function ($g) {
            $name = strtolower($g['name']);
            $id = strtolower($g['id']);
            if (strpos($id, 'mario') !== false || strpos($name, 'mario') !== false)
                return false;
            if (strpos($id, 'flappy') !== false || strpos($name, 'flappy') !== false)
                return false;
            return true;
        });

        return response()->json(array_values($games));
    }

    /**
     * Get all difficulty settings for the admin panel.
     */
    public function getDifficultySettings()
    {
        $games = self::getGameDefinitions();
        $result = [];

        foreach ($games as $game) {
            $settings = [];
            foreach ($game['difficulty_keys'] as $dk) {
                $value = GlobalSetting::where('key', $dk['key'])->value('value');
                $settings[] = [
                    'key' => $dk['key'],
                    'label' => $dk['label'],
                    'value' => $value !== null ? floatval($value) : $dk['default'],
                    'default' => $dk['default'],
                ];
            }
            $result[] = [
                'id' => $game['id'],
                'name' => $game['name'],
                'icon' => $game['icon'],
                'settings' => $settings,
            ];
        }

        return response()->json($result);
    }

    /**
     * Save difficulty settings for all retro games.
     */
    public function saveDifficultySettings(Request $request)
    {
        $settings = $request->input('settings', []);

        foreach ($settings as $key => $value) {
            GlobalSetting::updateOrCreate(['key' => $key], ['value' => strval($value)]);
        }

        return response()->json(['message' => 'Configurações de dificuldade salvas!']);
    }

    /**
     * Toggle a game's active/inactive status for the Retro section.
     */
    public function toggleActive(Request $request)
    {
        $gameId = $request->input('game_id');
        $active = $request->input('active') ? '1' : '0';

        \App\Models\GlobalSetting::updateOrCreate(
            ['key' => 'retro_active_' . $gameId],
            ['value' => $active]
        );

        return response()->json(['message' => 'Status atualizado!', 'game_id' => $gameId, 'active' => $active]);
    }

    /**
     * Update a retro game's name and/or banner.
     */
    public function updateGame(Request $request)
    {
        $gameId = $request->input('game_id');

        if ($request->has('name')) {
            GlobalSetting::updateOrCreate(
                ['key' => 'retro_name_' . $gameId],
                ['value' => $request->input('name')]
            );
        }

        if ($request->hasFile('banner')) {
            $file = $request->file('banner');
            $filename = 'retro_' . $gameId . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('assets/games/' . $gameId), $filename);

            GlobalSetting::updateOrCreate(
                ['key' => 'retro_banner_' . $gameId],
                ['value' => '/assets/games/' . $gameId . '/' . $filename]
            );
        }

        return response()->json(['message' => 'Jogo atualizado!']);
    }

    /**
     * Get game settings for a specific game (used by the game iframe).
     */
    public function getGameSettings($gameId)
    {
        $games = self::getGameDefinitions();
        $game = collect($games)->firstWhere('id', $gameId);

        if (!$game) {
            return response()->json(['error' => 'Jogo não encontrado'], 404);
        }

        $settings = [];
        foreach ($game['difficulty_keys'] as $dk) {
            $value = GlobalSetting::where('key', $dk['key'])->value('value');
            $keyParts = explode('_REAL_', $dk['key']);
            $shortKey = strtolower(end($keyParts));
            $settings[$shortKey] = $value !== null ? floatval($value) : $dk['default'];
        }

        $user = Auth::user();

        // The game expects the 'last_balance' which is the bet amount
        $lastBet = \App\Models\Transaction::where('user_id', $user->id)
            ->where('type', 'bet')
            ->where('description', 'like', '%' . $gameId . '%')
            ->orderBy('id', 'desc')
            ->first();

        return response()->json([
            'settings' => $settings,
            'balance' => $user ? ($user->balance + $user->bonus_balance) : 0,
            'last_balance' => $lastBet ? ['amount' => $lastBet->amount] : null,
            'fake' => false
        ]);
    }

    /**
     * Start a game session by placing a bet.
     */
    public function startGame(Request $request)
    {
        $gameId = $request->input('jogo');
        $amount = floatval($request->input('aposta'));
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Usuário não autenticado'], 401);
        }

        if ($amount <= 0) {
            return response()->json(['error' => 'Valor de aposta inválido'], 400);
        }

        if (($user->balance + $user->bonus_balance) < $amount) {
            return response()->json(['error' => 'Saldo insuficiente'], 400);
        }

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($user, $amount, $gameId) {
                if ($user->balance >= $amount) {
                    $user->decrement('balance', $amount);
                    $user->increment('rollover_deposit_current', $amount);
                } else {
                    $realBet = $user->balance;
                    $bonusBet = $amount - $realBet;
                    $user->balance = 0;
                    $user->decrement('bonus_balance', $bonusBet);
                    if ($user->bonus_balance < 0) {
                        $user->bonus_balance = 0;
                    }
                    $user->save();

                    $user->increment('rollover_deposit_current', $realBet);
                    $user->increment('rollover_bonus_current', $bonusBet);
                }

                // Create transaction record
                \App\Models\Transaction::create([
                    'user_id' => $user->id,
                    'type' => 'bet',
                    'amount' => $amount,
                    'description' => 'Aposta em Jogo Retrô: ' . $gameId,
                    'external_id' => $gameId
                ]);
            });
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao processar aposta: ' . $e->getMessage()], 500);
        }

        // CRITICAL: Login the user into the session so the iframe can stay authenticated
        // via cookies, as some games have hardcoded API paths and can't send headers.
        Auth::guard('web')->login($user);

        return response()->json(['status' => 'success', 'url' => '/play/' . $gameId . '/']);
    }

    /**
     * Handle a win result from the game.
     */
    public function win(Request $request, $gameId)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Acesso negado'], 401);
        }

        $gain = floatval($request->input('ganho'));

        if ($gain <= 0) {
            return response()->json(['error' => 'Ganho inválido'], 400);
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($user, $gain, $gameId) {
            // Increase balance
            $user->increment('balance', $gain);

            // Create win transaction
            \App\Models\Transaction::create([
                'user_id' => $user->id,
                'type' => 'win',
                'amount' => $gain,
                'description' => 'Ganho em Jogo Retrô: ' . $gameId,
                'external_id' => $gameId
            ]);
        });

        return response()->json(['status' => 'success']);
    }

    /**
     * Handle a loss result from the game.
     */
    public function lost(Request $request, $gameId)
    {
        return response()->json(['status' => 'success']);
    }

    /**
     * Handle redirects from games after win/loss.
     */
    public function handleRedirect($gameId)
    {
        $winAmount = request()->query('win_amount');

        $html = "
        <!DOCTYPE html>
        <html>
        <head>
            <title>Fim de Jogo</title>
            <script>
                // Notify parent if the fetch override missed it
                window.parent.postMessage({ 
                    type: '" . ($winAmount > 0 ? 'game_win' : 'game_loss') . "',
                    win_amount: '" . $winAmount . "'
                }, '*');
                
                function goBack() {
                    window.parent.postMessage({ type: 'close' }, '*');
                }
            </script>
            <style>
                body { background: #000; color: #fff; display: flex; align-items: center; justify-content: center; height: 100vh; font-family: sans-serif; }
                .card { text-align: center; padding: 20px; border: 1px solid #fca000; border-radius: 10px; }
                button { background: #fca000; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-weight: bold; margin-top: 10px; }
            </style>
        </head>
        <body>
            <div class='card'>
                <h2>Jogo Finalizado</h2>
                <p>" . ($winAmount > 0 ? "Você ganhou R$ $winAmount!" : "Poxa, você perdeu :(") . "</p>
                <button onclick='goBack()'>Voltar para o Início</button>
            </div>
        </body>
        </html>";

        return response($html);
    }

    /**
     * Serve the game's index file and inject the auth token and parent messaging via global fetch override.
     */
    public function play($gameId)
    {
        $token = request()->query('token');
        if (!$token) {
            return abort(401, 'Token não fornecido');
        }

        $filePath = public_path('play/' . $gameId . '/index.php');
        if (!file_exists($filePath)) {
            $filePath = public_path('play/' . $gameId . '/index.html');
        }

        if (!file_exists($filePath)) {
            return abort(404, 'Jogo não encontrado');
        }

        $content = file_get_contents($filePath);

        // REMOVE PROTECTION SCRIPTS FOR DEBUGGING
        $content = preg_replace('/<script[^>]*disable-devtool[^>]*><\/script>/i', '<!-- disabled devtool -->', $content);
        $content = str_ireplace('console.clear()', '// console.clear()', $content);

        // ROBUST INJECTION: Override window.fetch to handle headers and postMessage
        $fetchOverride = "
        <script>
        (function() {
            // Prevent clearing the console
            const originalClear = console.clear;
            console.clear = function() { console.log('[RetroDebug] Game tried to clear console. Blocked.'); };

            console.log('[RetroDebug] Starting injection for: {$gameId}');
            const originalFetch = window.fetch;
            window.fetch = async function(...args) {
                let [resource, config] = args;
                
                const resourceStr = (typeof resource === 'string') ? resource : (resource instanceof URL ? resource.href : resource.toString());
                console.log('[RetroDebug] Intercepted fetch:', resourceStr);

                if (resourceStr.indexOf('/games/') !== -1 || resourceStr.indexOf('../../games/') !== -1) {
                    console.log('[RetroDebug] -> Game API call:', resourceStr);
                    if (!config) config = {};
                    if (!config.headers) config.headers = {};
                    
                    config.headers['Authorization'] = 'Bearer ' + '{$token}';
                    config.headers['Accept'] = 'application/json';
                    
                    try {
                        const response = await originalFetch(resource, config);
                        console.log('[RetroDebug] <- API Response status:', response.status);
                        
                        if (resourceStr.indexOf('/win') !== -1 || resourceStr.indexOf('/lost') !== -1) {
                            const clone = response.clone();
                            clone.json().then(function(data) {
                                console.log('[RetroDebug] signaling parent outcome from API');
                                window.parent.postMessage({ 
                                    type: resourceStr.indexOf('/win') !== -1 ? 'game_win' : 'game_loss',
                                    data: data
                                }, '*');
                            }).catch(function(e) { console.error('[RetroDebug] JSON parse error:', e); });
                        }
                        return response;
                    } catch (e) {
                        console.error('[RetroDebug] Fetch failed:', e);
                        throw e;
                    }
                }
                return originalFetch(...args);
            };
            window.tokenFromLaravel = '{$token}';
            console.log('[RetroDebug] Global fetch override installed.');
        })();
        </script>
        <base href='/play/{$gameId}/'>
        ";

        $content = str_replace('<head>', '<head>' . $fetchOverride, $content);

        return response($content)->header('Content-Type', 'text/html');
    }
}
