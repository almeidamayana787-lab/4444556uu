<?php

namespace App\Filament\Resources\GameResource\Pages;

use App\Models\Category;
use App\Models\Game;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Support\Exceptions\Halt;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EditGameModal
{
    public static function getFormSchema(): array
    {
        return [
            Forms\Components\Section::make('Configurações do Jogo')
                ->schema([
                    Forms\Components\TextInput::make('game_name')
                        ->label('Nome do Jogo')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\FileUpload::make('cover')
                        ->label('Imagem do Jogo')
                        ->image()
                        ->imageEditor()
                        ->directory('games/covers')
                        ->visibility('public')
                        ->maxSize(2048) // 2MB
                        ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/webp'])
                        ->helperText('Tamanho recomendado: 300x400px. Formatos aceitos: PNG, JPEG, WEBP. Máximo: 2MB.'),

                    Forms\Components\Select::make('category_id')
                        ->label('Categoria do Jogo')
                        ->options(function () {
                            return Category::where('status', true)
                                ->pluck('name', 'id')
                                ->toArray();
                        })
                        ->searchable()
                        ->required()
                        ->helperText('Selecione a categoria à qual este jogo pertence.'),

                    Forms\Components\Select::make('provider_id')
                        ->label('Provedor do Jogo')
                        ->relationship('provider', 'name')
                        ->disabled()
                        ->helperText('O provedor não pode ser alterado após a importação.'),

                    Forms\Components\Toggle::make('status')
                        ->label('Status do Jogo')
                        ->default(true)
                        ->helperText('Ative ou desative o jogo para exibição no site.'),

                    Forms\Components\TextInput::make('rtp')
                        ->label('RTP (%)')
                        ->numeric()
                        ->step(0.01)
                        ->minValue(0)
                        ->maxValue(100)
                        ->helperText('Retorno ao Jogador. Deixe em branco para usar o valor padrão.'),

                    Forms\Components\Toggle::make('is_featured')
                        ->label('Jogo em Destaque')
                        ->default(false)
                        ->helperText('Marque este jogo como destaque na página inicial.'),

                    Forms\Components\Toggle::make('show_home')
                        ->label('Exibir na Página Inicial')
                        ->default(true)
                        ->helperText('Exiba este jogo na página inicial do site.'),
                ])
                ->columns(2),
        ];
    }

    public static function saveGame(array $data, Game $game): void
    {
        try {
            // Processar upload da imagem se houver
            if (isset($data['cover']) && is_string($data['cover'])) {
                // Se for uma string (nova imagem), o FileUpload já tratou o upload
                // Apenas garantimos que o caminho está correto
                $data['cover'] = str_replace('storage/', '', $data['cover']);
            }

            $game->update($data);

            Notification::make()
                ->title('Sucesso')
                ->body('Jogo atualizado com sucesso!')
                ->success()
                ->send();

        } catch (Halt $exception) {
            throw $exception;
        } catch (\Exception $e) {
            Notification::make()
                ->title('Erro')
                ->body('Erro ao atualizar jogo: ' . $e->getMessage())
                ->danger()
                ->send();

            throw new Halt();
        }
    }

    public static function getRecommendedImageSize(): string
    {
        return '300x400px (largura x altura) - Proporção 3:4';
    }
}
