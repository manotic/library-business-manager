<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Accessory>
 */
class AccessoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    // database/factories/AccessoryFactory.php
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'accessory_name' => 'USB Cable',
            'buying_amount' => 200,
            'selling_amount' => 500,
            'paid_amount' => 0,
            'name' => 'Test Client',
            'contact' => '0712345678',
        ];
    }
}
