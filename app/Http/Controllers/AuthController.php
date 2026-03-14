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

    public function me(Request $request)
    {
        if (Auth::check()) {
            return response()->json(['user' => Auth::user()]);
        }
        return response()->json(['user' => null], 401);
    }
}
