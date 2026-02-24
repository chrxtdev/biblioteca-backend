<?php

namespace App\Http\Controllers;

use App\Models\ReadingProgress;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Gerencia o progresso de leitura e preferências do leitor de PDF.
 *
 * Persiste página atual, total de páginas, modo de visualização
 * e nível de zoom por livro para cada usuário.
 */
class ReadingProgressController extends Controller
{
    /**
     * Retorna o progresso de leitura de um livro específico.
     *
     * Se o usuário nunca leu o livro, retorna um objeto com
     * valores padrão (sem persistir) para evitar erros no frontend.
     */
    public function show(Request $request, $bookId): JsonResponse
    {
        $progress = ReadingProgress::where('user_id', $request->user()->id)
            ->where('book_id', $bookId)
            ->first();

        if (!$progress) {
            $progress = new ReadingProgress([
                'user_id' => $request->user()->id,
                'book_id' => $bookId,
                'current_page' => 0,
                'total_pages' => 0,
                'progress_percentage' => 0,
                'view_mode' => 'single',
                'pdf_scale' => 1.0,
            ]);
        }

        return response()->json(['progress' => $progress]);
    }

    /**
     * Atualiza ou cria o progresso de leitura de um livro.
     *
     * Além da página atual, persiste opcionalmente as preferências
     * do leitor (viewMode e zoom) para restaurar na próxima sessão.
     */
    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
            'current_page' => 'required|integer|min:1',
            'total_pages' => 'required|integer|min:1',
            'view_mode' => 'sometimes|string|in:single,double,scroll',
            'pdf_scale' => 'sometimes|numeric|min:0.5|max:3.0',
        ]);

        $progress = ReadingProgress::getForUser($request->user(), $request->book_id);
        $progress->updateProgress($request->current_page, $request->total_pages);

        if ($request->has('view_mode')) {
            $progress->view_mode = $request->view_mode;
        }
        if ($request->has('pdf_scale')) {
            $progress->pdf_scale = $request->pdf_scale;
        }
        $progress->save();

        return response()->json([
            'success' => true,
            'progress' => $progress->fresh(['book'])
        ]);
    }

    /**
     * Marca um livro como concluído (100% lido).
     */
    public function markAsCompleted(Request $request): JsonResponse
    {
        $request->validate([
            'book_id' => 'required|exists:books,id'
        ]);

        $progress = ReadingProgress::getForUser($request->user(), $request->book_id);
        $progress->is_completed = true;
        $progress->progress_percentage = 100;
        $progress->last_read_at = now();
        $progress->save();

        return response()->json([
            'success' => true,
            'progress' => $progress->fresh(['book']),
            'message' => 'Livro marcado como concluído!'
        ]);
    }

    /**
     * Lista todo o histórico de leitura do usuário, ordenado por último acesso.
     */
    public function index(Request $request): JsonResponse
    {
        $progress = $request->user()->readingProgress()
            ->with('book')
            ->orderBy('last_read_at', 'desc')
            ->get();

        return response()->json(['progress' => $progress]);
    }
}
