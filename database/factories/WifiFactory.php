<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Wifi>
 */
class WifiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(), // Automatically creates a user if none is provided
            'name' => $this->faker->name(),
            'mac' => $this->faker->macAddress(),
            'amount' => $this->faker->randomFloat(2, 100, 2000),
            'is_debt' => false,
            'expires_at' => now()->addHours(2),
            'created_at' => now(),
        ];
    }
}
