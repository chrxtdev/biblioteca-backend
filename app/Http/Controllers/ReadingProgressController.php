<?php

namespace App\Http\Controllers;

use App\Models\ReadingProgress;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ReadingProgressController extends Controller
{
    /**
     * Obter progresso de leitura de um livro específico
     */
    public function show(Request $request, $bookId): JsonResponse
    {
        $user = $request->user();
        
        $progress = ReadingProgress::where('user_id', $user->id)
            ->where('book_id', $bookId)
            ->first();

        return response()->json([
            'progress' => $progress
        ]);
    }

    /**
     * Atualizar ou criar progresso de leitura
     */
    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
            'current_page' => 'required|integer|min:1',
            'total_pages' => 'required|integer|min:1'
        ]);

        $user = $request->user();
        $bookId = $request->book_id;
        $currentPage = $request->current_page;
        $totalPages = $request->total_pages;

        // Buscar ou criar progresso
        $progress = ReadingProgress::firstOrCreate(
            [
                'user_id' => $user->id,
                'book_id' => $bookId
            ],
            [
                'current_page' => 0,
                'total_pages' => $totalPages,
                'progress_percentage' => 0,
                'is_completed' => false
            ]
        );

        // Atualizar progresso
        $progress->updateProgress($currentPage, $totalPages);

        return response()->json([
            'success' => true,
            'progress' => $progress->fresh(['book'])
        ]);
    }

    /**
     * Marcar livro como concluído
     */
    public function markAsCompleted(Request $request): JsonResponse
    {
        $request->validate([
            'book_id' => 'required|exists:books,id'
        ]);

        $user = $request->user();
        $bookId = $request->book_id;

        $progress = ReadingProgress::firstOrCreate(
            [
                'user_id' => $user->id,
                'book_id' => $bookId
            ]
        );

        // Marcar como concluído
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
     * Obter todos os progressos do usuário
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $progress = $user->readingProgress()
            ->with('book')
            ->orderBy('last_read_at', 'desc')
            ->get();

        return response()->json([
            'progress' => $progress
        ]);
    }
}
