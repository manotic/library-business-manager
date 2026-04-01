<?php

namespace Tests\Feature;

use App\Livewire\OutIncomeManager;
use App\Models\OutIncome;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OutIncomeManagerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function component_renders_successfully()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(OutIncomeManager::class)
            ->assertStatus(200)
            ->assertSee('Outside Income Dashboard');
    }

    #[Test]
    public function can_record_outside_income()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(OutIncomeManager::class)
            ->set('source', 'Freelance Project')
            ->set('amount', 1500)
            ->set('phone', '0788123456')
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('notify');

        $this->assertDatabaseHas('out_incomes', [
            'source' => 'Freelance Project',
            'amount' => 1500,
            'user_id' => $user->id
        ]);
    }

    #[Test]
    public function stats_calculate_correctly_for_income()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        OutIncome::factory()->create(['user_id' => $user->id, 'amount' => 1000]);
        OutIncome::factory()->create(['user_id' => $user->id, 'amount' => 2500]);

        Livewire::test(OutIncomeManager::class)
            ->assertViewHas('stats', function ($stats) {
                return $stats['total_income'] == 3500 && 
                       $stats['entry_count'] == 2;
            });
    }

    #[Test]
    public function search_filter_works()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        OutIncome::factory()->create(['user_id' => $user->id, 'source' => 'Unique Source']);
        OutIncome::factory()->create(['user_id' => $user->id, 'source' => 'Common Item']);

        Livewire::test(OutIncomeManager::class)
            ->set('search', 'Unique')
            ->assertViewHas('records', function ($records) {
                return $records->count() === 1 && 
                       $records->first()->source === 'Unique Source';
            });
    }

    #[Test]
    public function validation_rules_are_enforced()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Test Required
        Livewire::test(OutIncomeManager::class)
            ->set('source', '')
            ->set('amount', '')
            ->call('save')
            ->assertHasErrors([
                'source' => 'required',
                'amount' => 'required'
            ]);

        // Test Min Value (fails min, not numeric)
        Livewire::test(OutIncomeManager::class)
            ->set('source', 'Valid Name')
            ->set('amount', 0)
            ->call('save')
            ->assertHasErrors(['amount' => 'min']);

        // Test Numeric
        Livewire::test(OutIncomeManager::class)
            ->set('amount', 'abc')
            ->call('save')
            ->assertHasErrors(['amount' => 'numeric']);
    }

    #[Test]
    public function it_displays_stats_and_records_for_current_month_by_default()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Freeze time to March 15, 2026
        \Carbon\Carbon::setTestNow('2026-03-15');

        // Entry 1: Last Month (February) - Should be excluded by default
        OutIncome::factory()->create([
            'user_id' => $user->id,
            'amount' => 1000,
            'created_at' => now()->subMonth(), 
        ]);

        // Entry 2: This Month (March) - Should be included
        OutIncome::factory()->create([
            'user_id' => $user->id,
            'source' => 'Current Month Income',
            'amount' => 500,
            'created_at' => now(), 
        ]);

        Livewire::test(OutIncomeManager::class)
            ->assertViewHas('stats', function ($stats) {
                // Should only see the 500 from March
                return (int)$stats['total_income'] === 500;
            })
            ->assertViewHas('records', function ($records) {
                // Should only show 1 record (the March one)
                return $records->count() === 1 && $records->first()->source === 'Current Month Income';
            });

        \Carbon\Carbon::setTestNow(); // Reset time
    }

    #[Test]
    public function it_updates_income_data_when_manually_changing_date_range()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        \Carbon\Carbon::setTestNow('2026-03-15');

        // Create an entry in February
        OutIncome::factory()->create([
            'user_id' => $user->id,
            'source' => 'February Entry',
            'amount' => 2000,
            'created_at' => '2026-02-10',
        ]);

        Livewire::test(OutIncomeManager::class)
            // Default March view should show 0 entries
            ->assertViewHas('records', fn($records) => $records->count() === 0)
            // Manually set range to include February
            ->set('date_from', '2026-02-01')
            ->set('date_to', '2026-02-28')
            ->assertViewHas('records', function ($records) {
                return $records->count() === 1 && $records->first()->source === 'February Entry';
            })
            ->assertViewHas('stats', function ($stats) {
                return (int)$stats['total_income'] === 2000;
            });

        \Carbon\Carbon::setTestNow();
    }
}