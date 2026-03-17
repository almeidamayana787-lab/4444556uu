<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    // List users with search and pagination
    public function listUsers(Request $request)
    {
        $query = User::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(10);
        return response()->json($users);
    }

    // Create a new user (Influencer/Demo/Normal)
    public function createUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20|unique:users',
            'password' => 'required|string|min:6',
            'is_demo' => 'boolean',
            'is_admin' => 'boolean',
            'balance' => 'numeric|min:0',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'password_plain' => $request->password,
            'is_demo' => $request->boolean('is_demo'),
            'is_admin' => $request->boolean('is_admin'),
            'balance' => $request->balance ?? 0.00,
        ]);

        return response()->json(['success' => true, 'user' => $user]);
    }

    // Update user details
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'string|max:255',
            'email' => "nullable|string|email|max:255|unique:users,email,{$id}",
            'phone' => "nullable|string|max:20|unique:users,phone,{$id}",
            'password' => 'nullable|string|min:6',
            'balance' => 'numeric',
            'is_demo' => 'boolean',
            'is_admin' => 'boolean',
        ]);

        if ($request->has('name'))
            $user->name = $request->name;
        if ($request->has('email'))
            $user->email = $request->email;
        if ($request->has('phone'))
            $user->phone = $request->phone;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
            $user->password_plain = $request->password;
        }
        if ($request->has('balance'))
            $user->balance = $request->balance;

        if ($request->has('is_demo'))
            $user->is_demo = $request->boolean('is_demo');

        if ($request->has('is_admin'))
            $user->is_admin = $request->boolean('is_admin');

        $user->save();

        return response()->json(['success' => true, 'user' => $user]);
    }

    // Get user transaction history
    public function getUserHistory($id)
    {
        $transactions = Transaction::where('user_id', $id)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return response()->json($transactions);
    }
}
