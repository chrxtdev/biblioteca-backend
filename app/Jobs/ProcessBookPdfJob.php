<?php

namespace App\Jobs;

use App\Models\Book;
use App\Services\PdfService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessBookPdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Número de tentativas antes de falhar.
     */
    public int $tries = 3;

    /**
     * Tempo máximo de execução em segundos.
     */
    public int $timeout = 120;

    /**
     * Tempo de espera entre tentativas (em segundos).
     */
    public int $backoff = 30;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Book $book
    ) {}

    /**
     * Execute the job.
     */
    public function handle(PdfService $pdfService): void
    {
        Log::info("ProcessBookPdfJob: Iniciando processamento do livro #{$this->book->id}");

        try {
            // Verifica se o arquivo existe
            $filePath = storage_path("app/public/{$this->book->file_path}");
            
            if (!file_exists($filePath)) {
                Log::error("ProcessBookPdfJob: Arquivo não encontrado", [
                    'book_id' => $this->book->id,
                    'file_path' => $filePath
                ]);
                return;
            }

            // Conta as páginas do PDF
            $totalPages = $pdfService->countPages($filePath);

            // Atualiza o livro com o total de páginas
            $this->book->update(['total_pages' => $totalPages]);

            Log::info("ProcessBookPdfJob: Processamento concluído", [
                'book_id' => $this->book->id,
                'total_pages' => $totalPages
            ]);

        } catch (\Exception $e) {
            Log::error("ProcessBookPdfJob: Erro no processamento", [
                'book_id' => $this->book->id,
                'error' => $e->getMessage()
            ]);

            // Re-lança a exceção para que o job seja marcado como falho
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("ProcessBookPdfJob: Job falhou definitivamente", [
            'book_id' => $this->book->id,
            'error' => $exception->getMessage()
        ]);

        // Marca o livro com 0 páginas para indicar erro no processamento
        $this->book->update(['total_pages' => 0]);
    }
}
