<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Wifi;
use App\Models\Accessory;
use App\Models\Library;
use App\Models\Lending;
use App\Models\OutIncome;
use App\Models\Expense;
use App\Livewire\Dashboard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Carbon\Carbon;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function dashboard_calculates_total_revenue_and_profit_correctly()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Freeze time to March 15, 2026
        Carbon::setTestNow('2026-03-15');

        // 1. Setup Income (Total Revenue = 1500)
        Wifi::factory()->create(['user_id' => $user->id, 'amount' => 500]); // Wifi
        OutIncome::factory()->create(['user_id' => $user->id, 'amount' => 1000]); // Out Income

        // 2. Setup Expenses (Total Expense = 400)
        Expense::factory()->create(['user_id' => $user->id, 'amount' => 400]);

        // 3. Setup Debt (Total Debt = 2000)
        Lending::factory()->create([
            'user_id' => $user->id, 
            'amount' => 3000, 
            'amount_returned' => 1000 // 2000 remaining debt
        ]);

        // 4. Create an entry from LAST MONTH (Should be ignored by default)
        Wifi::factory()->create([
            'user_id' => $user->id, 
            'amount' => 9999, 
            'created_at' => now()->subMonth()
        ]);

        Livewire::test(Dashboard::class)
            ->assertViewHas('stats', function ($stats) {
                // Revenue: 500 + 1000 = 1500
                // Profit: 1500 - 400 = 1100
                // Debt: 2000
                return (int)$stats['total_revenue'] === 1500 &&
                       (int)$stats['net_profit'] === 1100 &&
                       (int)$stats['total_debt'] === 2000;
            });

        Carbon::setTestNow();
    }

    #[Test]
    public function dashboard_updates_totals_when_date_range_changes()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Carbon::setTestNow('2026-03-15');

        // Create February Data
        Wifi::factory()->create([
            'user_id' => $user->id, 
            'amount' => 5000, 
            'created_at' => '2026-02-10'
        ]);

        Livewire::test(Dashboard::class)
            // Default (March) should show 0
            ->assertViewHas('stats', fn($stats) => $stats['total_revenue'] == 0)
            
            // Switch to February
            ->set('date_from', '2026-02-01')
            ->set('date_to', '2026-02-28')
            
            // Should now show February data
            ->assertViewHas('stats', function ($stats) {
                return (int)$stats['total_revenue'] === 5000;
            });

        Carbon::setTestNow();
    }

    #[Test]
    public function dashboard_identifies_top_expense_category()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create different expenses
        Expense::factory()->create(['user_id' => $user->id, 'category' => 'Rent', 'amount' => 1000]);
        Expense::factory()->create(['user_id' => $user->id, 'category' => 'Electricity', 'amount' => 200]);
        Expense::factory()->create(['user_id' => $user->id, 'category' => 'Rent', 'amount' => 500]);

        Livewire::test(Dashboard::class)
            ->assertViewHas('stats', function ($stats) {
                return $stats['top_expense'] === 'Rent';
            });
    }
}