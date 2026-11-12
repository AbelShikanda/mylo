<?php

namespace Database\Factories\Identity;

use App\Models\Identity\Reader;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReaderFactory extends Factory
{
    protected $model = Reader::class;

    public function definition(): array
    {
        return [
            'id' => \Illuminate\Support\Str::uuid(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/1iS', // password
            'email_verified_at' => now(),
            'remember_token' => null,
        ];
    }
}
