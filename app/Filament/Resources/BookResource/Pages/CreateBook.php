<?php

namespace App\Filament\Resources\BookResource\Pages;

use App\Filament\Resources\BookResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBook extends CreateRecord
{
    protected static string $resource = BookResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        $book = $this->record;
        
        // Se por algum motivo o total_pages não foi calculado no formulário (ex: erro no parser síncrono),
        // ou apenas para garantir, despachamos o job.
        // O job verifica se o arquivo existe e atualiza.
        if (is_null($book->total_pages) || $book->total_pages === 0) {
            \App\Jobs\ProcessBookPdfJob::dispatch($book);
        }
    }
}
