<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'password' => 'required',
        ]);

        $avatars = [
            '/casino_icons/perfil-user/homemperfil.png',
            '/casino_icons/perfil-user/perf.png',
        ];

        $randomAvatar = $avatars[array_rand($avatars)];
        $randomName = 'User122' . mt_rand(1000, 9999);

        $user = User::create([
            'name' => $randomName,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'avatar' => $randomAvatar,
        ]);

        // Auto-login for simplicity
        Auth::login($user);

        return response()->json([
            'message' => 'User registered',
            'user' => $user
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required', // Can be name or phone
            'password' => 'required'
        ]);

        $user = User::where('name', trim($request->login))
            ->orWhere('phone', trim($request->login))
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Credenciais inválidas'], 401);
        }

        Auth::login($user);

        $token = $user->createToken('admin-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token
        ]);
    }

    public function me(Request $request)
    {
        if (Auth::check()) {
            return response()->json(['user' => Auth::user()]);
        }
        return response()->json(['user' => null], 401);
    }
}
