<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function user(Request $request)
    {
        return response()->json($request->user()); // Returns auth user
    }
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Auth try 
        if (!($token = JWTAuth::attempt($request->only('email', 'password')))) {
            throw ValidationException::withMessages([
                'email' => 'Autenticação falhou, dados inválidos',
            ]);
        }

        //  Returns token
        $user = JWTAuth::user(); 
        return response()->json(compact('token', 'user'));
    }

    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(['message' => 'Successfully logged out']);
    }
    public function refresh(Request $request)
    {
        try {
            // Try to generate a new token
            if (!$token = JWTAuth::refresh(JWTAuth::getToken())) {
                return response()->json(['error' => 'Não foi possível gerar um novo token.'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Erro ao tentar atualizar o token.'], 500);
        }

        return response()->json(compact('token'));
    }

    // register
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // jwt token so the user is automatically logged in
        $token = JWTAuth::fromUser($user);
        // return user and token
        return response()->json(compact('user', 'token'));
        
    }
}
