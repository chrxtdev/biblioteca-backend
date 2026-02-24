<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * Gerencia autenticação via API (Token Sanctum).
 *
 * Responsável por registro, login e logout de usuários
 * que acessam o sistema via app mobile ou SPA.
 */
class AuthController extends Controller
{
    /**
     * Registra um novo usuário e retorna token de acesso.
     *
     * Cria o usuário, gera um token Sanctum e o retorna
     * para uso imediato nas requisições autenticadas.
     */
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Usuário cadastrado com sucesso!',
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Autentica o usuário e retorna um novo token Sanctum.
     *
     * Revoga todos os tokens anteriores (single-session) antes de
     * gerar um novo, garantindo que apenas uma sessão ativa exista.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Login ou senha incorretos'], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();

        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login realizado!',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    /**
     * Revoga todos os tokens do usuário (logout global).
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Deslogado com sucesso']);
    }
}
