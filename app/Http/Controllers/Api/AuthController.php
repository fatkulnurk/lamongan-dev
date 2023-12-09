<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'confirm_password' => 'required|same:password',
        ]);

        $data = collect($validated)->only(['name', 'email', 'password'])->toArray();
        $data['password'] = Hash::make($data['password']);
        $user = User::query()->create($data);
        $user->refresh();

        return response()->json([
            'message' => 'Pendaftaran berhasil!',
            'data' => $user
        ]);
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
        ]);

        $user = User::query()->where('email', $validated['email'])->first();

//        if (blank($user)) {
//            return response()->json([
//                'message' => 'Email tidak ditemukan!'
//            ], 401);
//        }

        if (!Hash::check($validated['password'], ($user->password ?? null))) {
            return response()->json([
                'message' => 'Password salah!'
            ], 401);
        }

        $token = $user->createToken('access_token');

        return response()->json([
            'message' => 'Login berhasil!',
            'data' => [
                'token' => $token->plainTextToken
            ]
        ]);
    }
}
