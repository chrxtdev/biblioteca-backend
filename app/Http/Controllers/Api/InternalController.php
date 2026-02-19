<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class InternalController extends Controller
{
    /**
     * Marca as novidades como vistas na sessão.
     */
    public function markNewsSeen(): JsonResponse
    {
        session(['last_seen_news' => now()]);
        return response()->json(['success' => true]);
    }

    /**
     * Retorna o usuário autenticado.
     */
    public function user(Request $request)
    {
        return $request->user();
    }
}
