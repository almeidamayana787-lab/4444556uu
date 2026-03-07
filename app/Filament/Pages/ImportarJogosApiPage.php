<?php

namespace App\Filament\Pages;

use App\Models\GamesKey;
use App\Models\Game;
use App\Models\Provider;
use App\Models\Category;
use Exception;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\View;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Grid;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImportarJogosApiPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-tray';

    protected static string $view = 'filament.pages.importar-jogos-api-page';

    protected static ?string $title = 'IMPORTAR JOGOS API';

    protected static ?string $slug = 'importar-jogos-api';

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public ?array $data = [];
    public ?GamesKey $gamesKey;

    public function mount(): void
    {
        $this->gamesKey = GamesKey::first() ?? new GamesKey();
        $this->form->fill($this->gamesKey->toArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Configurações das APIs')
                    ->description('Configure as credenciais das APIs para importação de jogos')
                    ->schema([
                        Placeholder::make('status_playfiver')
                            ->label('Status PlayFiver')
                            ->content(function () {
                                return $this->verificarStatusPlayFiver() 
                                    ? '✅ Configurado e Ativo'
                                    : '❌ Não configurado ou com erro';
                            }),
                        
                        Placeholder::make('status_max_api')
                            ->label('Status Max API Games')
                            ->content(function () {
                                return $this->verificarStatusMaxApi() 
                                    ? '✅ Configurado e Ativo'
                                    : '❌ Não configurado ou com erro';
                            }),
                    ])
                    ->columns(2),

                Section::make('Importação de Jogos')
                    ->description('Importe jogos das APIs configuradas')
                    ->schema([
                        $this->getPlayFiverSection(),
                        $this->getMaxApiSection(),
                        $this->getProvidersSection(),
                    ])
                    ->columns(1),
            ])
            ->statePath('data');
    }

    private function verificarStatusPlayFiver(): bool
    {
        $gamesKey = GamesKey::first();
        if (!$gamesKey || empty($gamesKey->playfiver_token) || empty($gamesKey->playfiver_code)) {
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $gamesKey->playfiver_token,
            ])->get('https://api.playfivers.com/api/v2/games');

            return $response->successful();
        } catch (Exception $e) {
            Log::error('Erro ao verificar status PlayFiver: ' . $e->getMessage());
            return false;
        }
    }

    private function verificarStatusMaxApi(): bool
    {
        $gamesKey = GamesKey::first();
        if (!$gamesKey || empty($gamesKey->max_api_agent_code) || empty($gamesKey->max_api_agent_token)) {
            return false;
        }

        try {
            $response = Http::post('https://maxapigames.com/api/v2', [
                'method' => 'game_list',
                'agent_code' => $gamesKey->max_api_agent_code,
                'agent_token' => $gamesKey->max_api_agent_token,
            ]);

            return $response->successful() && isset($response->json()['data']);
        } catch (Exception $e) {
            Log::error('Erro ao verificar status Max API: ' . $e->getMessage());
            return false;
        }
    }

    private function getPlayFiverSection(): Section
    {
        return Section::make('PlayFiver')
            ->schema([
                Placeholder::make('jogos_disponiveis_playfiver')
                    ->label('Jogos Disponíveis')
                    ->content(function () {
                        $count = $this->getJogosDisponiveisPlayFiver();
                        return $count > 0 ? "{$count} jogos disponíveis" : 'Não foi possível contar os jogos';
                    }),

                Action::make('importar_playfiver')
                    ->label('Importar Jogos PlayFiver')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function () {
                        $this->importarJogosPlayFiver();
                    })
                    ->disabled(!$this->verificarStatusPlayFiver()),

                Action::make('excluir_playfiver')
                    ->label('Excluir Jogos PlayFiver')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->action(function () {
                        $this->excluirJogosPlayFiver();
                    })
                    ->disabled(function () {
                        return !$this->verificarStatusPlayFiver() || $this->getJogosPlayFiverCount() === 0;
                    }),
            ])
            ->collapsible()
            ->collapsed()
            ->visible($this->verificarStatusPlayFiver());
    }

    private function getMaxApiSection(): Section
    {
        return Section::make('Max API Games')
            ->schema([
                Placeholder::make('jogos_disponiveis_max_api')
                    ->label('Jogos Disponíveis')
                    ->content(function () {
                        $count = $this->getJogosDisponiveisMaxApi();
                        return $count > 0 ? "{$count} jogos disponíveis" : 'Não foi possível contar os jogos';
                    }),

                Action::make('importar_max_api')
                    ->label('Importar Jogos Max API')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function () {
                        $this->importarJogosMaxApi();
                    })
                    ->disabled(!$this->verificarStatusMaxApi()),

                Action::make('excluir_max_api')
                    ->label('Excluir Jogos Max API')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->action(function () {
                        $this->excluirJogosMaxApi();
                    })
                    ->disabled(function () {
                        return !$this->verificarStatusMaxApi() || $this->getJogosMaxApiCount() === 0;
                    }),
            ])
            ->collapsible()
            ->collapsed()
            ->visible($this->verificarStatusMaxApi());
    }

    private function getProvidersSection(): Section
    {
        return Section::make('Provedores de Jogos Importados')
            ->description('Visualize e gerencie os jogos por provedor')
            ->schema([
                Placeholder::make('providers_list')
                    ->label('Provedores com Jogos')
                    ->content(function () {
                        $providers = Provider::withCount('games')
                            ->whereHas('games')
                            ->orderBy('name')
                            ->get();

                        if ($providers->isEmpty()) {
                            return '<p class="text-gray-500">Nenhum jogo importado ainda. Importe jogos primeiro para visualizar os provedores.</p>';
                        }

                        $html = '<div class="space-y-3">';
                        foreach ($providers as $provider) {
                            $html .= '<div class="border rounded-lg p-4 bg-gray-50">';
                            $html .= '<div class="flex items-center justify-between">';
                            
                            // Imagem e nome do provedor
                            $html .= '<div class="flex items-center space-x-3">';
                            if ($provider->cover) {
                                $html .= '<img src="' . Storage::url($provider->cover) . '" alt="' . $provider->name . '" class="w-12 h-12 rounded-lg object-cover">';
                            } else {
                                $html .= '<div class="w-12 h-12 rounded-lg bg-gray-300 flex items-center justify-center">';
                                $html .= '<span class="text-gray-600 text-xs">' . strtoupper(substr($provider->name, 0, 2)) . '</span>';
                                $html .= '</div>';
                            }
                            $html .= '<div>';
                            $html .= '<h4 class="font-semibold text-gray-900">' . $provider->name . '</h4>';
                            $html .= '<p class="text-sm text-gray-500">' . $provider->games_count . ' jogo(s) importado(s)</p>';
                            $html .= '</div>';
                            $html .= '</div>';

                            // Botões de ação
                            $html .= '<div class="flex items-center space-x-2">';
                            $html .= '<a href="/admin/games?tableFilters[provider][value]=' . $provider->id . '" ';
                            $html .= 'class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition-colors">';
                            $html .= '<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">';
                            $html .= '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>';
                            $html .= '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>';
                            $html .= '</svg>';
                            $html .= 'Ver Jogos';
                            $html .= '</a>';
                            
                            $html .= '<button onclick="window.open(\'/admin/importar-jogos-api\', \'_blank\')" ';
                            $html .= 'class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 transition-colors">';
                            $html .= '<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">';
                            $html .= '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>';
                            $html .= '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>';
                            $html .= '</svg>';
                            $html .= 'Configurar';
                            $html .= '</button>';
                            $html .= '</div>';

                            $html .= '</div>';
                            $html .= '</div>';
                        }
                        $html .= '</div>';

                        return new \Illuminate\Support\HtmlString($html);
                    }),
            ])
            ->collapsible()
            ->collapsed(fn() => Provider::whereHas('games')->count() === 0);
    }

    private function getJogosDisponiveisPlayFiver(): int
    {
        try {
            $gamesKey = GamesKey::first();
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $gamesKey->playfiver_token,
            ])->get('https://api.playfivers.com/api/v2/games');

            if ($response->successful()) {
                $data = $response->json();
                return count($data['data'] ?? []);
            }
        } catch (Exception $e) {
            Log::error('Erro ao contar jogos PlayFiver: ' . $e->getMessage());
        }
        return 0;
    }

    private function getJogosDisponiveisMaxApi(): int
    {
        try {
            $gamesKey = GamesKey::first();
            $response = Http::post('https://maxapigames.com/api/v2', [
                'method' => 'game_list',
                'agent_code' => $gamesKey->max_api_agent_code,
                'agent_token' => $gamesKey->max_api_agent_token,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return count($data['data'] ?? []);
            }
        } catch (Exception $e) {
            Log::error('Erro ao contar jogos Max API: ' . $e->getMessage());
        }
        return 0;
    }

    private function getJogosPlayFiverCount(): int
    {
        return Game::where('technology', 'playfiver')->count();
    }

    private function getJogosMaxApiCount(): int
    {
        return Game::where('technology', 'max_api')->count();
    }

    private function importarJogosPlayFiver(): void
    {
        Log::info('LOG TESTE: Iniciando importação de jogos PlayFiver');
        
        try {
            $gamesKey = GamesKey::first();
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $gamesKey->playfiver_token,
            ])->get('https://api.playfivers.com/api/v2/games');

            if (!$response->successful()) {
                throw new Exception('Falha ao buscar jogos da API PlayFiver');
            }

            $gamesData = $response->json()['data'] ?? [];
            $importados = 0;

            DB::beginTransaction();
            Log::info('LOG TESTE: Iniciando transação DB para PlayFiver');

            foreach ($gamesData as $gameData) {
                $providerName = $gameData['provider'] ?? 'Unknown';
                $provider = Provider::firstOrCreate(
                    ['code' => Str::slug($providerName)],
                    [
                        'name' => $providerName,
                        'status' => true,
                        'cover' => $gameData['image_url'] ?? null,
                    ]
                );

                Game::updateOrCreate(
                    [
                        'game_code' => $gameData['game_code'],
                        'technology' => 'playfiver'
                    ],
                    [
                        'provider_id' => $provider->id,
                        'game_name' => $gameData['name'],
                        'game_type' => $gameData['type'] ?? 'slot',
                        'cover' => $gameData['image_url'],
                        'status' => $gameData['status'] ?? true,
                        'rtp' => $gameData['rtp'] ?? null,
                        'is_mobile' => $gameData['mobile_compatible'] ?? true,
                        'technology' => 'playfiver',
                        'original' => false,
                    ]
                );

                $importados++;
                Log::info("LOG TESTE: Jogo PlayFiver importado: {$gameData['name']}");
            }

            DB::commit();
            Log::info('LOG TESTE: Transação commitada com sucesso');

            Notification::make()
                ->title('Importação Concluída')
                ->body("{$importados} jogos PlayFiver importados com sucesso!")
                ->success()
                ->send();

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('LOG TESTE: Erro na importação PlayFiver: ' . $e->getMessage());
            
            Notification::make()
                ->title('Erro na Importação')
                ->body('Ocorreu um erro ao importar jogos PlayFiver: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    private function importarJogosMaxApi(): void
    {
        Log::info('LOG TESTE: Iniciando importação de jogos Max API');
        
        try {
            $gamesKey = GamesKey::first();
            $response = Http::post('https://maxapigames.com/api/v2', [
                'method' => 'game_list',
                'agent_code' => $gamesKey->max_api_agent_code,
                'agent_token' => $gamesKey->max_api_agent_token,
            ]);

            if (!$response->successful()) {
                throw new Exception('Falha ao buscar jogos da API Max API');
            }

            $gamesData = $response->json()['data'] ?? [];
            $importados = 0;

            DB::beginTransaction();
            Log::info('LOG TESTE: Iniciando transação DB para Max API');

            foreach ($gamesData as $gameData) {
                $providerName = $gameData['provider'] ?? 'Unknown';
                $provider = Provider::firstOrCreate(
                    ['code' => Str::slug($providerName)],
                    [
                        'name' => $providerName,
                        'status' => true,
                        'cover' => $gameData['image_url'] ?? null,
                    ]
                );

                Game::updateOrCreate(
                    [
                        'game_code' => $gameData['game_code'],
                        'technology' => 'max_api'
                    ],
                    [
                        'provider_id' => $provider->id,
                        'game_name' => $gameData['name'],
                        'game_type' => $gameData['type'] ?? 'slot',
                        'cover' => $gameData['image_url'],
                        'status' => $gameData['status'] ?? true,
                        'rtp' => $gameData['rtp'] ?? null,
                        'is_mobile' => $gameData['mobile_compatible'] ?? true,
                        'technology' => 'max_api',
                        'original' => false,
                    ]
                );

                $importados++;
                Log::info("LOG TESTE: Jogo Max API importado: {$gameData['name']}");
            }

            DB::commit();
            Log::info('LOG TESTE: Transação commitada com sucesso');

            Notification::make()
                ->title('Importação Concluída')
                ->body("{$importados} jogos Max API importados com sucesso!")
                ->success()
                ->send();

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('LOG TESTE: Erro na importação Max API: ' . $e->getMessage());
            
            Notification::make()
                ->title('Erro na Importação')
                ->body('Ocorreu um erro ao importar jogos Max API: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    private function excluirJogosPlayFiver(): void
    {
        Log::info('LOG TESTE: Iniciando exclusão de jogos PlayFiver');
        
        try {
            $count = Game::where('technology', 'playfiver')->count();
            
            if ($count === 0) {
                Notification::make()
                    ->title('Nenhum jogo para excluir')
                    ->body('Não há jogos PlayFiver para excluir.')
                    ->info()
                    ->send();
                return;
            }

            DB::beginTransaction();
            Game::where('technology', 'playfiver')->delete();
            DB::commit();

            Log::info("LOG TESTE: {$count} jogos PlayFiver excluídos com sucesso");

            Notification::make()
                ->title('Exclusão Concluída')
                ->body("{$count} jogos PlayFiver excluídos com sucesso!")
                ->success()
                ->send();

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('LOG TESTE: Erro na exclusão PlayFiver: ' . $e->getMessage());
            
            Notification::make()
                ->title('Erro na Exclusão')
                ->body('Ocorreu um erro ao excluir jogos PlayFiver: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    private function excluirJogosMaxApi(): void
    {
        Log::info('LOG TESTE: Iniciando exclusão de jogos Max API');
        
        try {
            $count = Game::where('technology', 'max_api')->count();
            
            if ($count === 0) {
                Notification::make()
                    ->title('Nenhum jogo para excluir')
                    ->body('Não há jogos Max API para excluir.')
                    ->info()
                    ->send();
                return;
            }

            DB::beginTransaction();
            Game::where('technology', 'max_api')->delete();
            DB::commit();

            Log::info("LOG TESTE: {$count} jogos Max API excluídos com sucesso");

            Notification::make()
                ->title('Exclusão Concluída')
                ->body("{$count} jogos Max API excluídos com sucesso!")
                ->success()
                ->send();

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('LOG TESTE: Erro na exclusão Max API: ' . $e->getMessage());
            
            Notification::make()
                ->title('Erro na Exclusão')
                ->body('Ocorreu um erro ao excluir jogos Max API: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }
}
