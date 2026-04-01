<?php

namespace Database\Seeders;

use App\Models\Accessory;
use App\Models\Expense;
use App\Models\Lending;
use App\Models\Library;
use App\Models\OutIncome;
use App\Models\User;
use App\Models\Wifi;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        // 2. Loop through the last 6 months
        for ($i = 5; $i >= 0; $i--) {
            $monthDate = Carbon::now()->subMonths($i);

            // Create 5-10 records per month for each category
            $count = rand(5, 15);

            for ($j = 0; $j < $count; $j++) {
                $randomDay = $monthDate->copy()->startOfMonth()->addDays(rand(0, 27));

                // Accessories
                Accessory::factory()->create([
                    'user_id' => $user->id,
                    'created_at' => $randomDay,
                ]);

                // Lending
                Lending::factory()->create([
                    'user_id' => $user->id,
                    'created_at' => $randomDay,
                    'amount_returned' => rand(0, 1) ? 500 : 0, // Random partial payments
                ]);

                // Wifi
                Wifi::factory()->create([
                    'user_id' => $user->id,
                    'created_at' => $randomDay,
                ]);

                // Expenses
                Expense::factory()->create([
                    'user_id' => $user->id,
                    'created_at' => $randomDay,
                ]);

                // Out Income
                OutIncome::factory()->create([
                    'user_id' => $user->id,
                    'created_at' => $randomDay,
                ]);

                // Library
                Library::factory()->create([
                    'user_id' => $user->id,
                    'created_at' => $randomDay,
                ]);
            }
        }
    }
}

