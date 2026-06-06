<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'nama'     => fake()->name(),
            'username' => fake()->unique()->userName(),
            'email'    => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'nomor_hp' => fake()->phoneNumber(),
            'gender'   => fake()->randomElement(['Laki-laki', 'Perempuan']),
            'role'     => 'pedagang',
            'no_kios'  => null,
            'remember_token' => Str::random(10),
        ];
    }
}
