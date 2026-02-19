<?php

namespace App\Filament\Resources\BookResource\Pages;

use App\Filament\Resources\BookResource;
use Filament\Actions;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditBook extends EditRecord
{
    protected static string $resource = BookResource::class;

    public function getTitle(): string
    {
        return 'Editar / Inspecionar Livro';
    }

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
                    Notification::make()
                        ->title('Livro aprovado com sucesso!.')
                        ->success()
                        ->send();
                    $this->redirect($this->getResource()::getUrl('index'));
                })
                ->visible(fn() => !$this->record->is_verified),

            Actions\Action::make('retornar_analise')
                ->label('Voltar para Análise')
                ->color('warning')
                ->icon('heroicon-o-arrow-path')
                ->requiresConfirmation()
                ->modalHeading('Desfazer Aprovação')
                ->modalDescription('O livro deixará de aparecer no site. Deseja continuar?')
                ->action(function () {
                    $this->record->update([
                        'is_verified' => false,
                        'rejection_reason' => null
                    ]);
                    Notification::make()
                        ->title('Livro voltou para análise.')
                        ->warning()
                        ->send();
                    $this->redirect($this->getResource()::getUrl('index'));
                })
                ->visible(fn() => $this->record->is_verified),

            Actions\Action::make('rejeitar')
                ->label('Rejeitar Livro')
                ->color('danger')
                ->icon('heroicon-o-x-circle')
                ->form([
                    Textarea::make('reason')
                        ->label('Motivo da Rejeição?')
                        ->required()
                        ->default($this->record->rejection_reason),
                ])
                ->action(function (array $data) {
                    $this->record->update([
                        'is_verified' => false,
                        'rejection_reason' => $data['reason'],
                    ]);
                    Notification::make()
                        ->title('Livro rejeitado!')
                        ->body('O motivo foi salvo.')
                        ->danger()
                        ->send();
                    $this->redirect($this->getResource()::getUrl('index'));
                }),

            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $book = $this->record;

        // Se total_pages estiver inválido, tentamos recalcular via Job
        if (is_null($book->total_pages) || $book->total_pages === 0) {
            \App\Jobs\ProcessBookPdfJob::dispatch($book);
        }
    }
}
