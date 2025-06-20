<?php

namespace App\Http\Service;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function register(array $data): string
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'] ?? 'lector',
        ]);

        return $user->createToken('api')->plainTextToken;
    }

    public function login(array $data): ?string
    {
        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return null;
        }

        return $user->createToken('api')->plainTextToken;
    }

    public function logout($user): void
    {
        $user->currentAccessToken()->delete();
    }
}
