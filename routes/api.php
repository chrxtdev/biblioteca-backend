<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\Api\InternalController;
use App\Http\Controllers\ReadingProgressController;
use App\Http\Controllers\Api\AuthController;

/*
|--------------------------------------------------------------------------
| ROTAS PÚBLICAS (Qualquer um acessa)
|--------------------------------------------------------------------------
*/

// Registro e Login
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:api');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:api');

// Vitrine de Livros (Aprovados)
Route::get('/livros', [BookController::class, 'index']);

/*
|--------------------------------------------------------------------------
| ROTAS PROTEGIDAS COM SANCTUM (Token Bearer OU Cookie de Sessão)
|--------------------------------------------------------------------------
| O Laravel Sanctum suporta autenticação "stateful" via cookies quando
| a requisição vem do mesmo domínio. Isso permite que o frontend Blade
| faça chamadas AJAX autenticadas usando a sessão existente.
*/
Route::middleware(['web', 'auth'])->group(function () {
    // Progresso de Leitura (para o frontend web com sessão)
    Route::get('/reading-progress/{bookId}', [ReadingProgressController::class, 'show']);
    Route::post('/reading-progress', [ReadingProgressController::class, 'update']);
    Route::post('/reading-progress/complete', [ReadingProgressController::class, 'markAsCompleted']);
    Route::get('/reading-progress', [ReadingProgressController::class, 'index']);

    // Marcar novidades como vistas
    Route::post('/mark-news-seen', [InternalController::class, 'markNewsSeen'])->name('api.mark-news-seen');
});

/*
|--------------------------------------------------------------------------
| ROTAS PROTEGIDAS COM TOKEN SANCTUM (Apenas Bearer Token)
|--------------------------------------------------------------------------
| Para aplicações mobile ou SPAs que usar token-based authentication.
*/
Route::middleware('auth:sanctum')->group(function () {

    // Dados do Usuário Logado
    Route::get('/user', [InternalController::class, 'user']);

    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);

    // Enviar Livro
    Route::post('/livros', [BookController::class, 'store'])->middleware('throttle:uploads');

    // Ver meus envios
    Route::get('/meus-livros', [BookController::class, 'myBooks']);
});
