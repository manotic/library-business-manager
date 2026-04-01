<?php

namespace Database\Factories;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseFactory extends Factory
{
    protected $model = Expense::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'category' => $this->faker->randomElement(['Rent', 'Electricity', 'Stock', 'Salaries', 'Other']),
            'amount' => $this->faker->randomFloat(2, 100, 5000),
            'date' => $this->faker->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
            'description' => $this->faker->sentence(),
        ];
    }
}