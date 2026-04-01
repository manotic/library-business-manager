<?php

namespace Tests\Feature;

use App\Livewire\LibraryManager;
use App\Models\Library;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LibraryManagerTest extends TestCase
{
    use RefreshDatabase; // Clears the database after every test

    #[Test]
    public function component_renders_successfully()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test(LibraryManager::class)
            ->assertStatus(200)
            ->assertSee('Library Dashboard');
    }

    #[Test]
    public function can_create_library_entry()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(LibraryManager::class)
            ->set('type', 'Movie')
            ->set('amount', 500)
            ->set('is_debt', false)
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('notify');

        $this->assertDatabaseHas('libraries', [
            'user_id' => $user->id,
            'type' => 'Movie',
            'amount' => 500,
            'is_debt' => false,
        ]);
    }

    #[Test]
    public function debtor_name_is_required_if_is_debt_is_true()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(LibraryManager::class)
            ->set('type', 'Series')
            ->set('amount', 1000)
            ->set('is_debt', true)
            ->set('debtor_name', '') // Leave empty
            ->call('save')
            ->assertHasErrors(['debtor_name' => 'required_if']);
    }

    #[Test]
    public function can_toggle_debt_status()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $record = Library::factory()->create([
            'user_id' => $user->id,
            'is_debt' => true,
            'debtor_name' => 'John Doe',
        ]);

        Livewire::test(LibraryManager::class)
            ->call('toggleDebt', $record->id)
            ->assertDispatched('notify');

        $this->assertDatabaseHas('libraries', [
            'id' => $record->id,
            'is_debt' => false,
        ]);
    }

    #[Test]
    public function stats_correctly_calculate_profit_and_debt()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // 1. Create a Paid Movie (500)
        Library::factory()->create([
            'user_id' => $user->id,
            'type' => 'Movie',
            'amount' => 500,
            'is_debt' => false,
        ]);

        // 2. Create a Debt Series (1000)
        Library::factory()->create([
            'user_id' => $user->id,
            'type' => 'Series',
            'amount' => 1000,
            'is_debt' => true,
            'debtor_name' => 'John Doe',
        ]);

        // 3. Create a Paid Song (200)
        Library::factory()->create([
            'user_id' => $user->id,
            'type' => 'Songs',
            'amount' => 200,
            'is_debt' => false,
        ]);

        /** * Logic Check:
         * Total Revenue = 500 + 1000 + 200 = 1700
         * Pending Debt = 1000
         * Actual Profit = 1700 - 1000 = 700
         */
        Livewire::test(LibraryManager::class)
            ->assertSet('showData', false) // Check default state
            ->set('showData', true)        // Reveal data to "see" it
            ->assertSee('700')             // Should see Actual Profit
            ->assertSee('1,000');          // Should see Pending Debt
    }

    #[Test]
    public function it_filters_library_stats_and_records_by_manual_date_range()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // 1. Freeze time
        \Carbon\Carbon::setTestNow('2026-03-15');

        // 2. Create a record for February (Last Month)
        Library::factory()->create([
            'user_id' => $user->id,
            'amount' => 1000,
            'created_at' => now()->subMonth(),
        ]);

        // 3. Create a record for March (Today) - Fixed field 'name' to 'debtor_name'
        Library::factory()->create([
            'user_id' => $user->id,
            'debtor_name' => 'March Debtor',
            'amount' => 500,
            'created_at' => now(),
        ]);

        // Test A: Default View (Now shows only March because of the updated render logic)
        Livewire::test(LibraryManager::class)
            ->assertViewHas('stats', function ($stats) {
                return (int) $stats['total'] === 500;
            })
            ->assertViewHas('records', fn ($records) => $records->count() === 1);

        // Test B: Manual Filter for February
        Livewire::test(LibraryManager::class)
            ->set('date_from', '2026-02-01')
            ->set('date_to', '2026-02-28')
            ->assertViewHas('stats', function ($stats) {
                return (int) $stats['total'] === 1000;
            })
            ->assertViewHas('records', fn ($records) => $records->count() === 1);

        \Carbon\Carbon::setTestNow();
    }

    #[Test]
    public function it_displays_no_data_when_date_range_has_no_entries()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create a record today
        Library::factory()->create([
            'user_id' => $user->id,
            'created_at' => now(),
        ]);

        // Set filters to a date range in the past
        Livewire::test(LibraryManager::class)
            ->set('date_from', now()->subYear()->format('Y-m-d'))
            ->set('date_to', now()->subYear()->addMonth()->format('Y-m-d'))
            ->assertViewHas('records', fn ($records) => $records->count() === 0)
            ->assertViewHas('stats', function ($stats) {
                return $stats['total'] == 0;
            });
    }
}
