<?php

namespace App\Filament\Resources\GameResource\Pages;

use App\Models\Game;
use App\Models\Provider;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class ListGamesByProvider extends ListRecords
{
    protected static string $resource = \App\Filament\Resources\GameResource::class;

    protected static ?string $title = 'Jogos por Provedor';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Game::query()
                    ->with(['provider', 'categories'])
                    ->when(request()->has('provider'), function (Builder $query) {
                        $query->where('provider_id', request()->get('provider'));
                    })
            )
            ->columns([
                Tables\Columns\ImageColumn::make('cover')
                    ->label('Imagem')
                    ->size(60)
                    ->circular()
                    ->defaultImageUrl(url('/images/default-game.png')),

                Tables\Columns\TextColumn::make('game_name')
                    ->label('Nome do Jogo')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('provider.name')
                    ->label('Provedor')
                    ->sortable()
                    ->badge()
                    ->color(fn($record) => $record->provider?->status ? 'success' : 'danger'),

                Tables\Columns\TextColumn::make('game_type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn($record) => match($record->game_type) {
                        'slot' => 'primary',
                        'table' => 'warning',
                        'live' => 'success',
                        default => 'gray'
                    }),

                Tables\Columns\TextColumn::make('rtp')
                    ->label('RTP')
                    ->formatStateUsing(fn($state) => $state ? $state . '%' : 'N/A')
                ->sortable(),

                Tables\Columns\ToggleColumn::make('status')
                    ->label('Status')
                    ->disabled(!auth()->user()->hasRole('admin')),

                Tables\Columns\TextColumn::make('technology')
                    ->label('Tecnologia')
                    ->badge()
                    ->color(fn($record) => match($record->technology) {
                        'playfiver' => 'info',
                        'max_api' => 'warning',
                        default => 'gray'
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('provider')
                    ->relationship('provider', 'name')
                    ->label('Provedor'),

                Tables\Filters\SelectFilter::make('game_type')
                    ->options([
                        'slot' => 'Slots',
                        'table' => 'Mesas',
                        'live' => 'Ao Vivo',
                        'crash' => 'Crash',
                    ])
                    ->label('Tipo de Jogo'),

                Tables\Filters\SelectFilter::make('technology')
                    ->options([
                        'playfiver' => 'PlayFiver',
                        'max_api' => 'Max API Games',
                    ])
                    ->label('Tecnologia'),

                Tables\Filters\TernaryFilter::make('status')
                    ->label('Status')
                    ->placeholder('Todos')
                    ->trueLabel('Ativos')
                    ->falseLabel('Inativos'),
            ])
            ->actions([
                Tables\Actions\Action::make('edit')
                    ->label('Editar')
                    ->icon('heroicon-o-pencil')
                    ->modalHeading('Editar Jogo')
                    ->modalWidth('2xl')
                    ->form(EditGameModal::getFormSchema())
                    ->action(function (array $data, Game $record): void {
                        EditGameModal::saveGame($data, $record);
                    })
                    ->successNotificationTitle('Jogo atualizado com sucesso!'),

                Tables\Actions\Action::make('view_categories')
                    ->label('Categorias')
                    ->icon('heroicon-o-tag')
                    ->modalHeading('Categorias do Jogo')
                    ->modalContent(function (Game $record) {
                        $categories = $record->categories;
                        
                        if ($categories->isEmpty()) {
                            return '<p class="text-gray-500">Este jogo não está associado a nenhuma categoria.</p>';
                        }

                        $html = '<div class="space-y-2">';
                        foreach ($categories as $category) {
                            $html .= '<div class="flex items-center justify-between p-2 bg-gray-50 rounded">';
                            $html .= '<span class="font-medium">' . $category->name . '</span>';
                            $html .= '<span class="text-sm text-gray-500">' . $category->description . '</span>';
                            $html .= '</div>';
                        }
                        $html .= '</div>';

                        return $html;
                    })
                    ->modalWidth('lg')
                    ->disabled(fn($record) => $record->categories->isEmpty()),

                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Excluir Jogo')
                    ->modalDescription('Tem certeza que deseja excluir este jogo? Esta ação não pode ser desfeita.')
                    ->modalSubmitActionLabel('Sim, excluir')
                    ->modalCancelActionLabel('Cancelar'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('delete')
                        ->requiresConfirmation()
                        ->modalHeading('Excluir Jogos Selecionados')
                        ->modalDescription('Tem certeza que deseja excluir os jogos selecionados?')
                        ->modalSubmitActionLabel('Sim, excluir')
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->emptyStateHeading('Nenhum jogo encontrado')
            ->emptyStateDescription('Nenhum jogo encontrado para os filtros selecionados.')
            ->emptyStateActions([
                Tables\Actions\Action::make('import_games')
                    ->label('Importar Jogos')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url('/admin/importar-jogos-api'),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('import_games')
                ->label('Importar Jogos')
                ->icon('heroicon-o-arrow-down-tray')
                ->url('/admin/importar-jogos-api')
                ->color('primary'),

            Actions\Action::make('manage_categories')
                ->label('Gerenciar Categorias')
                ->icon('heroicon-o-tag')
                ->url('/admin/categories')
                ->color('secondary'),
        ];
    }

    public function getBreadcrumb(): ?string
    {
        return null;
    }

    public function getTitle(): string
    {
        $providerId = request()->get('provider');
        
        if ($providerId) {
            $provider = Provider::find($providerId);
            if ($provider) {
                return "Jogos - {$provider->name}";
            }
        }

        return static::$title;
    }
}
