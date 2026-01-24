<?php

namespace App\Services;

use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\Log;

class PdfService
{
    private Parser $parser;

    public function __construct()
    {
        $this->parser = new Parser();
    }

    /**
     * Conta o número de páginas de um arquivo PDF.
     *
     * @param string $filePath Caminho absoluto do arquivo PDF
     * @return int Número de páginas (0 em caso de erro)
     */
    public function countPages(string $filePath): int
    {
        try {
            if (!file_exists($filePath)) {
                Log::warning("PdfService: Arquivo não encontrado: {$filePath}");
                return 0;
            }

            $pdf = $this->parser->parseFile($filePath);
            return count($pdf->getPages());
        } catch (\Exception $e) {
            Log::error("PdfService: Erro ao processar PDF: {$e->getMessage()}", [
                'file' => $filePath,
                'exception' => $e
            ]);
            return 0;
        }
    }

    /**
     * Extrai metadados do PDF (título, autor, etc.)
     *
     * @param string $filePath Caminho absoluto do arquivo PDF
     * @return array Metadados extraídos
     */
    public function extractMetadata(string $filePath): array
    {
        try {
            if (!file_exists($filePath)) {
                return [];
            }

            $pdf = $this->parser->parseFile($filePath);
            $details = $pdf->getDetails();

            return [
                'title' => $details['Title'] ?? null,
                'author' => $details['Author'] ?? null,
                'subject' => $details['Subject'] ?? null,
                'creator' => $details['Creator'] ?? null,
                'pages' => count($pdf->getPages()),
            ];
        } catch (\Exception $e) {
            Log::error("PdfService: Erro ao extrair metadados: {$e->getMessage()}");
            return [];
        }
    }
}
