<?php

namespace App\Filament\Resources\BookResource\Pages;

use App\Filament\Resources\BookResource;
use Filament\Actions;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Pages\EditRecord;

class EditBook extends EditRecord
{
    protected static string $resource = BookResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('aprovar')
                ->label('Aprovar Livro')
                ->color('success')
                ->icon('heroicon-o-check-circle')
                ->action(function () {
                    $this->record->update([
                        'is_verified' => true,
                        'rejection_reason' => null
                    ]);
                    $this->notify('success', 'Livro aprovado com sucesso!');
                    $this->redirect($this->getResource()::getUrl('index'));
                })
                ->visible(fn () => !$this->record->is_verified),

            Actions\Action::make('rejeitar')
                ->label('Rejeitar Livro')
                ->color('danger')
                ->icon('heroicon-o-x-circle')
                ->form([
                    Textarea::make('reason')
                        ->label('Motivo da Recusa')
                        ->required()
                        ->default($this->record->rejection_reason),
                ])
                ->action(function (array $data) {
                    $this->record->update([
                        'is_verified' => false,
                        'rejection_reason' => $data['reason'],
                    ]);
                    $this->notify('danger', 'Livro rejeitado!');
                    $this->redirect($this->getResource()::getUrl('index'));
                }),

            Actions\DeleteAction::make(),
        ];
    }
}
