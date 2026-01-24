<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\ReadingProgressController;

/*
|--------------------------------------------------------------------------
| ROTAS PÚBLICAS (Qualquer um acessa)
|--------------------------------------------------------------------------
*/

// Registro e Login
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Vitrine de Livros (Aprovados)
Route::get('/livros', [BookController::class, 'index']);

/*
|--------------------------------------------------------------------------
| ROTAS PROTEGIDAS (Precisa enviar o Token de Login, Bearer Token)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // Dados do Usuário Logado
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);

    // Enviar Livro
    Route::post('/livros', [BookController::class, 'store']);

    // Ver meus envios
    Route::get('/meus-livros', [BookController::class, 'myBooks']);

    // Progresso de Leitura
    Route::get('/reading-progress/{bookId}', [ReadingProgressController::class, 'show']);
    Route::post('/reading-progress', [ReadingProgressController::class, 'update']);
    Route::post('/reading-progress/complete', [ReadingProgressController::class, 'markAsCompleted']);
    Route::get('/reading-progress', [ReadingProgressController::class, 'index']);

    // Marcar novidades como vistas
    Route::post('/mark-news-seen', function () {
        session(['last_seen_news' => now()]);
        return response()->json(['success' => true]);
    })->name('api.mark-news-seen');
});

