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

            // Aumenta limites para arquivos grandes
            ini_set('memory_limit', '512M');
            set_time_limit(120);

            $pdf = $this->parser->parseFile($filePath);
            $pages = $pdf->getPages();
            $count = count($pages);

            // Fallback se o parser retornar 0 (pode ser PDF criptografado ou malformado)
            if ($count === 0) {
                return $this->countPagesRegex($filePath);
            }

            return $count;
        } catch (\Throwable $e) {
            Log::error("PdfService: Erro ao processar PDF (Parser): {$e->getMessage()}", [
                'file' => $filePath,
            ]);
            
            // Tenta fallback com Regex em caso de erro fatal ou exceção
            return $this->countPagesRegex($filePath);
        }
    }

    /**
     * Tentativa de contar páginas via Regex (menos preciso, mas robusto para falhas).
     */
    private function countPagesRegex(string $filePath): int
    {
        try {
            if (!file_exists($filePath)) return 0;
            
            $content = file_get_contents($filePath);
            // Conta ocorrências de /Type /Page
            $count = preg_match_all("/\/Type\s*\/Page[^s]/", $content, $matches);
            
            if ($count === 0) {
                 // Tenta outra variação comum
                 $count = preg_match_all("/\/Page\W/", $content, $matches);
            }

            Log::info("PdfService: Contagem via Regex: {$count}", ['file' => $filePath]);
            return $count;
        } catch (\Throwable $e) {
            Log::error("PdfService: Erro no fallback Regex: {$e->getMessage()}");
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
