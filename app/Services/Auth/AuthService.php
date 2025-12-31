<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function login(string $email, string $password): User
    {
        $user = User::where('email', $email)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            throw new \Exception('Invalid credentials');
        }

        Auth::login($user);

        return $user;
    }

    public function register(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // Assign default 'user' role
        $user->assignRole('user');

        Auth::login($user);

        return $user;
    }

    public function logout(User $user): void
    {
        $user->tokens()->delete();
        Auth::logout();
    }
}

