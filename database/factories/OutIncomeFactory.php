<?php

namespace Database\Factories;

use App\Models\OutIncome;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OutIncomeFactory extends Factory
{
    protected $model = OutIncome::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'source' => $this->faker->company() . ' Services',
            'phone' => $this->faker->phoneNumber(),
            'amount' => $this->faker->randomFloat(2, 500, 10000),
            'description' => $this->faker->sentence(),
        ];
    }
}