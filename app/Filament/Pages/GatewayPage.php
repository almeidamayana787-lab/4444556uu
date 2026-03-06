<?php

namespace App\Filament\Pages;

use App\Models\Gateway;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;


class GatewayPage extends Page
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.gateway-page';

    public ?array $data = [];
    public Gateway $setting;

    /**
     * @dev
     * @return bool
     */
    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('admin'); // Controla o acesso total à página
    }

    public static function canView(): bool
    {
        return auth()->user()->hasRole('admin'); // Controla a visualização de elementos específicos
    }

    /**
     * @return void
     */
    public function mount(): void
    {
        $gateway = Gateway::first();
        if (!empty($gateway)) {
            $this->setting = $gateway;
            $this->form->fill($this->setting->toArray());
        } else {
            $this->form->fill();
        }
    }

    /**
     * @param Form $form
     * @return Form
     */
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('REGISTRE SUAS CHAVES DE API GATEWAY')
                    ->description('Configure suas chaves de API para processamento de pagamentos')
                    ->schema([
                        Section::make('GGPIX API - PIX COMPLETO')
                            ->description(new HtmlString('
                                <div style="display: flex; align-items: center;">
                                    Integre pagamentos e transferências PIX com a GGPIX API, rápida e segura:
                                    <a class="dark:text-white"
                                        style="
                                            font-size: 14px;
                                            font-weight: 600;
                                            width: 150px;
                                            display: flex;
                                            background-color: #00b91e;
                                            padding: 10px;
                                            border-radius: 11px;
                                            justify-content: center;
                                            margin-left: 10px;
                                        "
                                        href="https://ggpix.com.br"
                                        target="_blank">
                                        Documentação
                                    </a>
                                </div>
                        '), )
                            ->schema([
                                TextInput::make('ggpix_uri')
                                    ->label('URL DA API GGPIX')
                                    ->placeholder('https://api.ggpix.com.br')
                                    ->maxLength(191)
                                    ->columnSpanFull(),
                                TextInput::make('ggpix_key')
                                    ->label('API KEY')
                                    ->placeholder('Digite a API Key da GGPIX')
                                    ->password()
                                    ->maxLength(191)
                                    ->columnSpanFull(),
                            ]),

                        // Confirmação de Alteração
                        Section::make('Confirmação de Alteração')
                            ->schema([
                                TextInput::make('admin_password')
                                    ->label('Senha de 2FA a que esta no arquivo (.env)')
                                    ->placeholder('Digite a senha de 2FA')
                                    ->password()
                                    ->required()
                                    ->dehydrateStateUsing(fn($state) => null), // Para que o valor não seja persistido
                            ]),

                    ]),
            ])
            ->statePath('data');
    }


    /**
     * @return void
     */
    public function submit(): void
    {
        try {
            if (env('APP_DEMO')) {
                Notification::make()
                    ->title('Atenção')
                    ->body('Você não pode realizar esta alteração na versão demo')
                    ->danger()
                    ->send();
                return;
            }

            // Validação da senha de 2FA
            if (
                !isset($this->data['admin_password']) ||
                $this->data['admin_password'] !== env('TOKEN_DE_2FA')
            ) {
                Notification::make()
                    ->title('Acesso Negado')
                    ->body('A senha de 2FA está incorreta. Você não pode atualizar os dados.')
                    ->danger()
                    ->send();
                return;
            }

            $setting = Gateway::first();
            if (!empty($setting)) {
                if ($setting->update($this->data)) {
                    Notification::make()
                        ->title('Configurações Atualizadas')
                        ->body('Suas configurações foram atualizadas com sucesso!')
                        ->success()
                        ->send();
                }
            } else {
                if (Gateway::create($this->data)) {
                    Notification::make()
                        ->title('Configurações Criadas')
                        ->body('Suas configurações foram criadas com sucesso!')
                        ->success()
                        ->send();
                }
            }
        } catch (\Filament\Support\Exceptions\Halt $exception) {
            Notification::make()
                ->title('Erro ao alterar dados!')
                ->body('Erro ao alterar dados!')
                ->danger()
                ->send();
        }
    }

}