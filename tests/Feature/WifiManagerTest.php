<?php

namespace Tests\Feature;

use App\Livewire\WifiManager;
use App\Models\User;
use App\Models\Wifi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class WifiManagerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_calculates_monthly_and_debt_totals_correctly()
    {
        $user = User::factory()->create();

        // Use Factory to create clean data
        Wifi::factory()->create([
            'user_id' => $user->id,
            'amount' => 1000,
            'is_debt' => false,
        ]);

        Wifi::factory()->create([
            'user_id' => $user->id,
            'amount' => 500,
            'is_debt' => true,
        ]);

        Livewire::actingAs($user)
            ->test(WifiManager::class)
            ->assertViewHas('stats', function ($stats) {
                // Monthly is 1500, Debt is 500, Profit is 1000
                return $stats['monthly'] == 1500 &&
                       $stats['debt'] == 500 &&
                       $stats['profit'] == 1000;
            });
    }

    #[Test]
    public function it_filters_data_by_date_range()
    {
        $user = User::factory()->create();

        // Old record via factory
        Wifi::factory()->create([
            'user_id' => $user->id,
            'name' => 'Old Record',
            'created_at' => now()->subYear(),
        ]);

        // Target record via factory
        Wifi::factory()->create([
            'user_id' => $user->id,
            'name' => 'Target Record',
            'created_at' => now(), // Today
        ]);

        Livewire::actingAs($user)
            ->test(WifiManager::class)
            ->set('date_from', now()->startOfMonth()->format('Y-m-d'))
            ->set('date_to', now()->endOfMonth()->format('Y-m-d'))
            ->assertViewHas('records', function ($records) {
                return $records->count() === 1 && $records->first()->name === 'Target Record';
            });
    }

    #[Test]
    public function it_toggles_debt_status_successfully()
    {
        $user = User::factory()->create();
        $record = Wifi::factory()->create([
            'user_id' => $user->id,
            'is_debt' => true,
        ]);

        Livewire::actingAs($user)
            ->test(WifiManager::class)
            ->call('toggleDebt', $record->id)
            ->assertDispatched('notify');

        $this->assertFalse((bool) $record->fresh()->is_debt);
    }

    #[Test]
    public function it_displays_stats_for_current_month_by_default()
    {
        $user = User::factory()->create();
        
        // 1. Freeze time to the middle of March 2026
        $knownDate = Carbon::create(2026, 3, 15, 12, 0, 0);
        Carbon::setTestNow($knownDate);

        // 2. Create a record for "Last Month" (February)
        Wifi::factory()->create([
            'user_id' => $user->id,
            'amount' => 1000,
            'created_at' => $knownDate->copy()->subMonth(), 
        ]);

        // 3. Create a record for "This Month" (March)
        Wifi::factory()->create([
            'user_id' => $user->id,
            'amount' => 500,
            'created_at' => $knownDate,
        ]);

        Livewire::actingAs($user)
            ->test(WifiManager::class)
            ->assertViewHas('stats', function ($stats) {
                // This should now strictly be 500
                return (int)$stats['monthly'] === 500;
            });

        // 4. Always reset time after the test
        Carbon::setTestNow();
    }

    #[Test]
    public function it_updates_table_records_when_manually_changing_date_range()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // 1. Create a record from 10 days ago
        Wifi::factory()->create([
            'user_id' => $user->id,
            'name' => 'Past Record',
            'created_at' => now()->subDays(10),
        ]);

        // 2. Create a record for Today
        Wifi::factory()->create([
            'user_id' => $user->id,
            'name' => 'Today Record',
            'created_at' => now(),
        ]);

        // Test filtering to see ONLY the record from 10 days ago
        Livewire::test(WifiManager::class)
            ->set('date_from', now()->subDays(12)->format('Y-m-d'))
            ->set('date_to', now()->subDays(8)->format('Y-m-d'))
            ->assertViewHas('records', function ($records) {
                return $records->count() === 1 &&
                       $records->first()->name === 'Past Record';
            });

        // Test filtering to see ONLY today's record
        Livewire::test(WifiManager::class)
            ->set('date_from', now()->format('Y-m-d'))
            ->set('date_to', now()->format('Y-m-d'))
            ->assertViewHas('records', function ($records) {
                return $records->count() === 1 &&
                       $records->first()->name === 'Today Record';
            });
    }
}
