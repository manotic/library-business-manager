<?php

namespace Database\Factories;

use App\Models\Lending;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LendingFactory extends Factory
{
    protected $model = Lending::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'source' => $this->faker->name(),
            'phone' => $this->faker->phoneNumber(),
            'amount' => $this->faker->randomFloat(2, 1000, 50000),
            'amount_returned' => 0,
            'description' => $this->faker->sentence(),
            'created_at' => now(),
        ];
    }
}