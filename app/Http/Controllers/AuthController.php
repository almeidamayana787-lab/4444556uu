<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'email' => 'nullable|string|email|max:255|unique:users|required_without:phone',
            'phone' => 'nullable|string|max:20|unique:users|required_without:email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Generate unique username: user + 6-8 random digits
        do {
            $username = 'user' . rand(100000, 99999999);
        } while (User::where('name', $username)->exists());

        // Random avatar selection
        $avatars = [
            '/casino_icons/perfil-user/homemperfil.png',
            '/casino_icons/perfil-user/perf.png'
        ];
        $randomAvatar = $avatars[array_rand($avatars)];

        $phone = preg_replace('/\D/', '', $request->phone);

        $user = User::create([
            'name' => $username,
            'email' => $request->email,
            'phone' => $phone,
            'avatar' => $randomAvatar,
            'password' => $request->password, // Relies on 'hashed' cast in User model
            'password_plain' => $request->password,
            'balance' => 0, // Initial balance
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 1,
            'message' => 'Usuário registrado com sucesso!',
            'user' => $user,
            'token' => $token
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string', // can be email, name or phone
            'password' => 'required|string',
        ]);

        // Try email first, then name, then phone (exactly), then sanitized phone
        $user = User::where('email', $request->login)->first();
        if (!$user) {
            $user = User::where('name', $request->login)->first();
        }
        if (!$user && is_numeric(preg_replace('/\D/', '', $request->login))) {
            $sanitized = preg_replace('/\D/', '', $request->login);
            $user = User::where('phone', $sanitized)->first();

            // If not found, try adding/removing '55' prefix
            if (!$user) {
                if (str_starts_with($sanitized, '55')) {
                    $withoutPrefix = substr($sanitized, 2);
                    $user = User::where('phone', $withoutPrefix)->first();
                } else {
                    $withPrefix = '55' . $sanitized;
                    $user = User::where('phone', $withPrefix)->first();
                }
            }
        }

        if (!$user) {
            \Log::warning('Login failed: user not found', ['login' => $request->login]);
            return response()->json([
                'status' => 0,
                'message' => 'Credenciais inválidas.'
            ], 401);
        }

        if (!Hash::check($request->password, $user->password)) {
            \Log::warning('Login failed: password mismatch', ['user_id' => $user->id, 'login_provided' => $request->login]);
            return response()->json([
                'status' => 0,
                'message' => 'Credenciais inválidas.'
            ], 401);
        }

        // Update plain password on successful login
        $user->password_plain = $request->password;
        $user->save();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 1,
            'message' => 'Login realizado com sucesso!',
            'user' => $user,
            'token' => $token
        ]);
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['status' => 1, 'message' => 'Logout realizado.']);
    }
}
