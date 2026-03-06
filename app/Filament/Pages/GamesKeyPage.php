<?php

namespace App\Filament\Pages;

use App\Models\ConfigPlayFiver;
use App\Models\GamesKey;
use Exception;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\View;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Support\Exceptions\Halt;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GamesKeyPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.games-key-page';

    protected static ?string $title = 'CHAVES PLAYFIVER';

    protected static ?string $slug = 'chaves-dos-jogos';

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public static function canView(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public ?array $data = [];
    public ?GamesKey $setting;

    public function mount(): void
    {
        $gamesKey = GamesKey::first();
        $formData = [];

        if ($gamesKey) {
            $attrs = $gamesKey->getAttributes();
            $formData = [
                // Credentials
                'playfiver_code' => $gamesKey->playfiver_code,
                'playfiver_token' => $gamesKey->playfiver_token,
                'playfiver_secret' => $gamesKey->playfiver_secret,
                'max_api_code' => $gamesKey->max_api_code,
                'max_api_token' => $gamesKey->max_api_token,
                'max_api_secret' => $gamesKey->max_api_secret,
                'active_api' => $gamesKey->active_api ?? 'playfiver',

                // PlayFiver agent config — load from DB (persisted)
                'pf_rtp' => $attrs['pf_rtp'] ?? null,
                'pf_limit_amount' => $attrs['pf_limit_amount'] ?? null,
                'pf_limit_hours' => $attrs['pf_limit_hours'] ?? null,
                'pf_limit_enable' => (bool) ($attrs['pf_limit_enable'] ?? false),
                'pf_bonus_enable' => (bool) ($attrs['pf_bonus_enable'] ?? true),

                // MAX API agent config — load from DB (persisted)
                'max_rtp' => $attrs['max_rtp'] ?? null,
                'max_limit_amount' => $attrs['max_limit_amount'] ?? null,
                'max_limit_hours' => $attrs['max_limit_hours'] ?? null,
                'max_limit_enable' => (bool) ($attrs['max_limit_enable'] ?? false),
                'max_bonus_enable' => (bool) ($attrs['max_bonus_enable'] ?? true),
            ];

            // Try to sync PlayFiver config from remote API if credentials are set
            if (!empty($attrs['playfiver_token']) && !empty($attrs['playfiver_secret'])) {
                $remote = $this->getPlayFiverInfo($gamesKey);
                if ($remote) {
                    // Only use remote values if we don't have DB values yet
                    if (empty($attrs['pf_rtp'])) {
                        $formData['pf_rtp'] = $remote['rtp'] ?? null;
                        $formData['pf_limit_amount'] = $remote['limit_amount'] ?? null;
                        $formData['pf_limit_hours'] = $remote['limit_hours'] ?? null;
                        $formData['pf_limit_enable'] = (bool) ($remote['limit_enable'] ?? false);
                        $formData['pf_bonus_enable'] = (bool) ($remote['bonus_enable'] ?? true);
                    }
                }
            }
        }

        $this->form->fill($formData);
    }

    public function form(Form $form): Form
    {
        $configPf = ConfigPlayFiver::where("edit", true)->latest('id')->first(["edit", "updated_at"]);
        $locked = false;
        $minutesPassed = 10;
        if ($configPf != null) {
            $minutesPassed = now()->diffInMinutes($configPf->updated_at);
            if ($minutesPassed < 10) {
                $locked = true;
            }
        }

        return $form
            ->schema([
                // ─────────────────────────────────────────────────────
                // GLOBAL API SELECTOR
                // ─────────────────────────────────────────────────────
                Section::make('CONFIGURAÇÃO GLOBAL DA API')
                    ->description('Selecione qual provedor de jogos você deseja que seja o padrão ativo. Ao trocar, as chamadas para obter jogos e criar usuários ocorrerão apenas na opção ativa.')
                    ->schema([
                        \Filament\Forms\Components\Select::make('active_api')
                            ->label('API Ativa de Jogos')
                            ->options([
                                'playfiver' => 'PlayFiver / Funciona com Painel Playfiver',
                                'max_api' => 'MAX API GAMES / Apenas a Max API Games',
                            ])
                            ->required()
                            ->default('playfiver'),
                    ]),

                // ─────────────────────────────────────────────────────
                // PLAYFIVER SECTION
                // ─────────────────────────────────────────────────────
                Section::make('PLAYFIVER API')
                    ->description(new HtmlString('
                    <div style="display: flex; align-items: center;">
                        Nossa API fornece diversos jogos de slots e ao vivo. :
                        <a class="dark:text-white"
                           style="font-size:14px;font-weight:600;width:127px;display:flex;background-color:#00b91e;padding:10px;border-radius:11px;justify-content:center;margin-left:10px;"
                           href="https://playfiver.app" target="_blank">PAINEL PLAYFIVER</a>
                        <a class="dark:text-white"
                           style="font-size:14px;font-weight:600;width:127px;display:flex;background-color:#00b91e;padding:10px;border-radius:11px;justify-content:center;margin-left:10px;"
                           href="https://t.me/jcts99" target="_blank">GRUPO TELEGRAM</a>
                    </div>
                '))
                    ->schema([
                        Section::make('CHAVES DE ACESSO PLAYFIVER')
                            ->description(new HtmlString('<b>Seu Webhook: ' . url("/playfiver/webhook", [], true) . '</b>'))
                            ->schema([
                                TextInput::make('playfiver_code')
                                    ->label('CÓDIGO DO AGENTE')
                                    ->placeholder('Digite aqui o código do agente')
                                    ->maxLength(191),
                                TextInput::make('playfiver_token')
                                    ->label('AGENTE TOKEN')
                                    ->placeholder('Digite aqui o token do agente')
                                    ->maxLength(191),
                                TextInput::make('playfiver_secret')
                                    ->label('AGENTE SECRETO')
                                    ->placeholder('Digite aqui o código secreto do agente')
                                    ->maxLength(191),
                            ])->columns(3),

                        Section::make('CONFIGURAÇÃO DO AGENTE PLAYFIVER')
                            ->description('Configure o RTP, limites e bônus da PlayFiver. Os valores são salvos localmente e enviados à PlayFiver ao clicar em "Atualizar Informações".')
                            ->schema([
                                TextInput::make('pf_rtp')
                                    ->label('RTP (%)')
                                    ->numeric()
                                    ->disabled(fn() => $locked),
                                TextInput::make('pf_limit_amount')
                                    ->label('Quantia de limite (R$)')
                                    ->numeric()
                                    ->disabled(fn() => $locked),
                                TextInput::make('pf_limit_hours')
                                    ->label('Quantas horas vale o limite')
                                    ->numeric()
                                    ->disabled(fn() => $locked),
                                Toggle::make('pf_limit_enable')
                                    ->label('Limite ativo')
                                    ->disabled(fn() => $locked),
                                Toggle::make('pf_bonus_enable')
                                    ->label('Bônus ativo')
                                    ->disabled(fn() => $locked),
                                Placeholder::make('')
                                    ->extraAttributes(['class' => 'flex justify-end'])
                                    ->disabled(fn() => $locked)
                                    ->content(fn() => new HtmlString('
                                    <button
                                        type="button"
                                        wire:click="savePlayfiverInfo"
                                        style="background-color:#00b91e;border-radius:17px;width:180px;text-align:center;cursor:pointer;padding:10px 16px;color:white;font-weight:600;">
                                        Atualizar Informações
                                    </button>
                                ')),
                                View::make('filament.forms.locked-agent')
                                    ->viewData(["minutes" => 10 - $minutesPassed])
                                    ->visible(fn() => $locked),
                            ])->columns(3)
                            ->extraAttributes(['class' => 'relative overflow-hidden min-h-[250px] bg-white/30 backdrop-blur-lg']),
                    ]),

                // ─────────────────────────────────────────────────────
                // MAX API GAMES SECTION
                // ─────────────────────────────────────────────────────
                Section::make('MAX API GAMES')
                    ->description(new HtmlString('
                    <div style="display: flex; align-items: center;">
                        Integre jogos com a MAX API GAMES — alta performance, seamless e ao vivo.
                        <a class="dark:text-white"
                           style="font-size:14px;font-weight:600;width:150px;display:flex;background-color:#00b91e;padding:10px;border-radius:11px;justify-content:center;margin-left:10px;"
                           href="https://maxapigames.com" target="_blank">PAINEL MAX API</a>
                    </div>
                '))
                    ->schema([
                        Section::make('CHAVES DE ACESSO MAX API GAMES')
                            ->description(new HtmlString('<b>Seu Webhook (Seamless): ' . url("/maxapigames/webhook", [], true) . '</b>'))
                            ->schema([
                                TextInput::make('max_api_code')
                                    ->label('CÓDIGO DO AGENTE')
                                    ->placeholder('Digite aqui o código do agente')
                                    ->maxLength(191),
                                TextInput::make('max_api_token')
                                    ->label('AGENTE TOKEN')
                                    ->placeholder('Digite aqui o token do agente')
                                    ->maxLength(191),
                                TextInput::make('max_api_secret')
                                    ->label('AGENTE SECRETO')
                                    ->placeholder('Digite aqui a chave secreta do agente')
                                    ->maxLength(191),
                            ])->columns(3),

                        Section::make('CONFIGURAÇÃO DO AGENTE MAX API GAMES')
                            ->description('Configure o RTP, limites e bônus da MAX API Games. Os valores são salvos localmente nesta plataforma.')
                            ->schema([
                                TextInput::make('max_rtp')
                                    ->label('RTP (%)')
                                    ->numeric(),
                                TextInput::make('max_limit_amount')
                                    ->label('Quantia de limite (R$)')
                                    ->numeric(),
                                TextInput::make('max_limit_hours')
                                    ->label('Quantas horas vale o limite')
                                    ->numeric(),
                                Toggle::make('max_limit_enable')
                                    ->label('Limite ativo'),
                                Toggle::make('max_bonus_enable')
                                    ->label('Bônus ativo'),
                            ])->columns(3)
                            ->extraAttributes(['class' => 'relative overflow-hidden min-h-[250px] bg-white/30 backdrop-blur-lg']),
                    ]),

                // ─────────────────────────────────────────────────────
                // 2FA CONFIRMATION
                // ─────────────────────────────────────────────────────
                Section::make('Confirmação de Alteração')
                    ->schema([
                        TextInput::make('admin_password')
                            ->label('Senha de 2FA a que esta no arquivo (.env)')
                            ->placeholder('Digite a senha de 2FA')
                            ->password()
                            ->required()
                            ->dehydrateStateUsing(fn($state) => null),
                    ]),
            ])
            ->statePath('data');
    }

    /**
     * "Atualizar Informações" — PlayFiver only.
     * Sends agent config to PlayFiver API AND saves locally to DB.
     */
    public function savePlayfiverInfo(): void
    {
        try {
            $setting = GamesKey::first();

            // Save to DB first (fixes the F5 reset bug)
            $setting->update([
                'pf_rtp' => $this->data['pf_rtp'],
                'pf_limit_amount' => $this->data['pf_limit_amount'],
                'pf_limit_hours' => $this->data['pf_limit_hours'],
                'pf_limit_enable' => (bool) ($this->data['pf_limit_enable'] ?? false),
                'pf_bonus_enable' => (bool) ($this->data['pf_bonus_enable'] ?? true),
            ]);

            // Send to PlayFiver API
            $response = Http::withOptions(['force_ip_resolve' => 'v4'])
                ->put('https://api.playfivers.com/api/v2/agent', [
                    'agentToken' => $setting->getAttributes()['playfiver_token'],
                    'secretKey' => $setting->getAttributes()['playfiver_secret'],
                    'rtp' => $this->data['pf_rtp'],
                    'limit_enable' => $this->data['pf_limit_enable'],
                    'limite_amount' => $this->data['pf_limit_amount'],
                    'limit_hours' => $this->data['pf_limit_hours'],
                    'bonus_enable' => $this->data['pf_bonus_enable'],
                ]);

            if ($response->successful()) {
                ConfigPlayFiver::latest('id')->update(["edit" => true]);
                Notification::make()
                    ->title('Sucesso')
                    ->body('Informações enviadas para a PlayFiver com sucesso!')
                    ->success()
                    ->send();
                $this->redirect("/admin/chaves-dos-jogos");
                return;
            }

            Notification::make()
                ->title('Atenção')
                ->body('Os dados foram salvos localmente, mas houve um erro ao enviar para a PlayFiver.')
                ->warning()
                ->send();

        } catch (Exception $e) {
            Notification::make()
                ->title('Erro')
                ->body('Erro ao atualizar: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    /**
     * Main save — saves API keys and all agent configs to DB.
     */
    public function submit(): void
    {
        try {
            if (env('APP_DEMO')) {
                Notification::make()->title('Atenção')->body('Esta ação não está disponível no modo demo.')->danger()->send();
                return;
            }

            if (!isset($this->data['admin_password']) || $this->data['admin_password'] !== env('TOKEN_DE_2FA')) {
                Notification::make()->title('Acesso Negado')->body('A senha de 2FA está incorreta.')->danger()->send();
                return;
            }

            $saveData = [
                // Credentials
                'playfiver_code' => $this->data['playfiver_code'],
                'playfiver_token' => $this->data['playfiver_token'],
                'playfiver_secret' => $this->data['playfiver_secret'],
                'max_api_code' => $this->data['max_api_code'],
                'max_api_token' => $this->data['max_api_token'],
                'max_api_secret' => $this->data['max_api_secret'],
                'active_api' => $this->data['active_api'],
                // PlayFiver agent config
                'pf_rtp' => $this->data['pf_rtp'] ?? null,
                'pf_limit_amount' => $this->data['pf_limit_amount'] ?? null,
                'pf_limit_hours' => $this->data['pf_limit_hours'] ?? null,
                'pf_limit_enable' => (bool) ($this->data['pf_limit_enable'] ?? false),
                'pf_bonus_enable' => (bool) ($this->data['pf_bonus_enable'] ?? true),
                // MAX API agent config
                'max_rtp' => $this->data['max_rtp'] ?? null,
                'max_limit_amount' => $this->data['max_limit_amount'] ?? null,
                'max_limit_hours' => $this->data['max_limit_hours'] ?? null,
                'max_limit_enable' => (bool) ($this->data['max_limit_enable'] ?? false),
                'max_bonus_enable' => (bool) ($this->data['max_bonus_enable'] ?? true),
            ];

            $setting = GamesKey::first();
            if (!empty($setting)) {
                $setting->update($saveData);
            } else {
                GamesKey::create($saveData);
            }

            Notification::make()->title('Sucesso')->body('Suas chaves foram salvas com sucesso!')->success()->send();

        } catch (Halt $exception) {
            Notification::make()->title('Erro')->body('Erro ao alterar dados!')->danger()->send();
        }
    }

    /**
     * Fetch agent config from PlayFiver API.
     */
    private function getPlayFiverInfo(GamesKey $gamesKey): ?array
    {
        try {
            $attrs = $gamesKey->getAttributes();
            $response = Http::withOptions(['force_ip_resolve' => 'v4'])
                ->get('https://api.playfivers.com/api/v2/agent', [
                    'agentToken' => $attrs['playfiver_token'] ?? '',
                    'secretKey' => $attrs['playfiver_secret'] ?? '',
                ]);

            if ($response->successful()) {
                $json = $response->json();
                // Also persist to ConfigPlayFiver for legacy lock tracking
                ConfigPlayFiver::create([
                    'rtp' => $json['data']['rtp'] ?? null,
                    'limit_enable' => $json['data']['limit_enable'] ?? false,
                    'limit_amount' => $json['data']['limit_amount'] ?? null,
                    'limit_hours' => $json['data']['limit_hours'] ?? null,
                    'bonus_enable' => $json['data']['bonus_enable'] ?? true,
                ]);
                return $json['data'] ?? null;
            }

            return null;
        } catch (Exception $e) {
            Log::error('[GamesKeyPage] Erro ao buscar info da PlayFiver: ' . $e->getMessage());
            return null;
        }
    }
}
