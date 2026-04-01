<?php

namespace Tests\Feature;

use App\Livewire\ExpenseManager;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ExpenseManagerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function component_renders_successfully()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(ExpenseManager::class)
            ->assertStatus(200)
            ->assertSee('Expense Tracker');
    }

    #[Test]
    public function can_record_new_expense()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(ExpenseManager::class)
            ->set('category', 'Electricity')
            ->set('amount', 1200)
            ->set('date', now()->toDateString())
            ->set('description', 'Monthly bill')
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('notify');

        $this->assertDatabaseHas('expenses', [
            'category' => 'Electricity',
            'amount' => 1200,
            'user_id' => $user->id,
        ]);
    }

    #[Test]
    public function stats_calculate_total_and_top_category()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Rent total: 5000
        Expense::factory()->create(['user_id' => $user->id, 'category' => 'Rent', 'amount' => 5000]);

        // Stock total: 2000
        Expense::factory()->create(['user_id' => $user->id, 'category' => 'Stock', 'amount' => 1000]);
        Expense::factory()->create(['user_id' => $user->id, 'category' => 'Stock', 'amount' => 1000]);

        Livewire::test(ExpenseManager::class)
            ->assertViewHas('stats', function ($stats) {
                return $stats['total_spent'] == 7000 &&
                       $stats['top_category'] === 'Rent';
            });
    }

    #[Test]
    public function filtering_by_category_works()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Expense::factory()->create(['user_id' => $user->id, 'category' => 'Rent']);
        Expense::factory()->create(['user_id' => $user->id, 'category' => 'Other']);

        Livewire::test(ExpenseManager::class)
            ->set('filter_category', 'Rent')
            ->assertViewHas('records', fn ($records) => $records->count() === 1 && $records->first()->category === 'Rent'
            );
    }

    #[Test]
    public function validation_enforces_rules()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Test missing fields
        Livewire::test(ExpenseManager::class)
            ->set('category', '')
            ->set('amount', '')
            ->call('save')
            ->assertHasErrors([
                'category' => 'required',
                'amount' => 'required',
            ]);

        // Test amount below minimum
        Livewire::test(ExpenseManager::class)
            ->set('category', 'Other')
            ->set('amount', 0)
            ->call('save')
            ->assertHasErrors(['amount' => 'min']);

        // Test invalid date format
        Livewire::test(ExpenseManager::class)
            ->set('date', 'not-a-date')
            ->call('save')
            ->assertHasErrors(['date' => 'date']);
    }

    #[Test]
    public function it_displays_expenses_for_current_month_by_default()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // 1. Freeze time to March 15, 2026
        Carbon::setTestNow('2026-03-15');

        // 2. Create an expense from February (Should be excluded)
        Expense::factory()->create([
            'user_id' => $user->id,
            'category' => 'Rent',
            'amount' => 5000,
            'created_at' => '2026-02-15 10:00:00',
        ]);

        // 3. Create an expense from March (Should be included)
        Expense::factory()->create([
            'user_id' => $user->id,
            'category' => 'Electricity',
            'amount' => 1200,
            'created_at' => '2026-03-10 10:00:00',
        ]);

        Livewire::test(ExpenseManager::class)
            ->assertViewHas('stats', function ($stats) {
                // Should only sum the 1200 from March
                return (int) $stats['total_spent'] === 1200;
            })
            ->assertViewHas('records', function ($records) {
                // Should only show 1 record in the table
                return $records->count() === 1 && $records->first()->category === 'Electricity';
            });

        // Always reset time
        Carbon::setTestNow();
    }

    #[Test]
    public function it_updates_expense_data_when_manually_changing_date_range()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Freeze time to March
        Carbon::setTestNow('2026-03-15');

        // Create a past expense
        Expense::factory()->create([
            'user_id' => $user->id,
            'category' => 'Old Stock',
            'amount' => 3000,
            'created_at' => '2026-01-10 10:00:00',
        ]);

        Livewire::test(ExpenseManager::class)
            // Initially, March view is empty (0 records)
            ->assertViewHas('records', fn ($records) => $records->count() === 0)

            // Manually set range to January
            ->set('date_from', '2026-01-01')
            ->set('date_to', '2026-01-31')

            ->assertViewHas('records', function ($records) {
                return $records->count() === 1 && $records->first()->category === 'Old Stock';
            })
            ->assertViewHas('stats', function ($stats) {
                return (int) $stats['total_spent'] === 3000;
            });

        Carbon::setTestNow();
    }
}
