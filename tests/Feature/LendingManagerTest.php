<?php

namespace Tests\Feature;

use App\Livewire\LendingManager;
use App\Models\Lending;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LendingManagerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function component_renders_successfully()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(LendingManager::class)
            ->assertStatus(200)
            ->assertSee('Lending Records');
    }

    #[Test]
    public function can_record_new_loan()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(LendingManager::class)
            ->set('source', 'John Doe')
            ->set('phone', '0712345678')
            ->set('amount', 5000)
            ->set('description', 'Business loan')
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('notify');

        $this->assertDatabaseHas('lendings', [
            'source' => 'John Doe',
            'amount' => 5000,
            'user_id' => $user->id,
        ]);
    }

    #[Test]
    public function can_add_installment_to_loan()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $loan = Lending::factory()->create([
            'user_id' => $user->id,
            'amount' => 10000,
            'amount_returned' => 2000,
        ]);

        Livewire::test(LendingManager::class)
            ->set('additional_payment.'.$loan->id, 3000)
            ->call('addInstallment', $loan->id)
            ->assertDispatched('notify');

        $this->assertEquals(5000, $loan->fresh()->amount_returned);
    }

    #[Test]
    public function stats_calculate_correctly()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Loan 1: 5000 lent, 1000 returned (4000 debt)
        Lending::factory()->create([
            'user_id' => $user->id,
            'amount' => 5000,
            'amount_returned' => 1000,
        ]);

        // Loan 2: 3000 lent, 3000 returned (0 debt)
        Lending::factory()->create([
            'user_id' => $user->id,
            'amount' => 3000,
            'amount_returned' => 3000,
        ]);

        Livewire::test(LendingManager::class)
            ->set('showData', true) // Show totals
            ->assertViewHas('stats', function ($stats) {
                return $stats['total_invested'] == 8000 &&
                       $stats['actual_collected'] == 4000 &&
                       $stats['pending_debt'] == 4000;
            });
    }

    #[Test]
    public function it_filters_by_status()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Fully Paid
        Lending::factory()->create([
            'user_id' => $user->id,
            'source' => 'Fully Paid Borrower',
            'amount' => 1000,
            'amount_returned' => 1000,
        ]);

        // Debt
        Lending::factory()->create([
            'user_id' => $user->id,
            'source' => 'Debtor Borrower',
            'amount' => 1000,
            'amount_returned' => 200,
        ]);

        Livewire::test(LendingManager::class)
            // Test Paid Filter
            ->set('filter_status', 'paid')
            ->assertViewHas('records', fn ($records) => $records->count() === 1 && $records->first()->source === 'Fully Paid Borrower'
            )
            // Test Debt Filter
            ->set('filter_status', 'debt')
            ->assertViewHas('records', fn ($records) => $records->count() === 1 && $records->first()->source === 'Debtor Borrower'
            );
    }

    #[Test]
    public function validation_works_as_expected()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Test 1: Required fields are missing
        Livewire::test(LendingManager::class)
            ->set('source', '')
            ->set('amount', '')
            ->call('save')
            ->assertHasErrors([
                'source' => 'required',
                'amount' => 'required',
            ]);

        // Test 2: Amount is not a number
        Livewire::test(LendingManager::class)
            ->set('source', 'John Doe')
            ->set('amount', 'not-a-number')
            ->call('save')
            ->assertHasErrors(['amount' => 'numeric']);

        // Test 3: Amount is less than 1 (This was your specific error)
        Livewire::test(LendingManager::class)
            ->set('source', 'John Doe')
            ->set('amount', -10)
            ->call('save')
            ->assertHasErrors(['amount' => 'min']);
    }

    #[Test]
    public function it_displays_stats_and_records_for_current_month_by_default()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Freeze time to March 2026
        \Carbon\Carbon::setTestNow('2026-03-15');

        // Loan 1: Last Month (February) - Should be excluded by default
        Lending::factory()->create([
            'user_id' => $user->id,
            'amount' => 5000,
            'created_at' => now()->subMonth(),
        ]);

        // Loan 2: This Month (March) - Should be included
        Lending::factory()->create([
            'user_id' => $user->id,
            'source' => 'March Borrower',
            'amount' => 2000,
            'created_at' => now(),
        ]);

        Livewire::test(LendingManager::class)
            ->assertViewHas('stats', function ($stats) {
                // Should only see the 2000 from March
                return (int) $stats['total_invested'] === 2000;
            })
            ->assertViewHas('records', function ($records) {
                // Should only show the March borrower
                return $records->count() === 1 && $records->first()->source === 'March Borrower';
            });

        \Carbon\Carbon::setTestNow(); // Reset time
    }

    #[Test]
    public function it_updates_lending_data_when_manually_changing_date_range()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        \Carbon\Carbon::setTestNow('2026-03-15');

        // Create a loan in February
        Lending::factory()->create([
            'user_id' => $user->id,
            'source' => 'February Borrower',
            'amount' => 10000,
            'created_at' => '2026-02-10',
        ]);

        Livewire::test(LendingManager::class)
            // Default March view should be empty
            ->assertViewHas('records', fn ($records) => $records->count() === 0)
            // Manually set range to February
            ->set('date_from', '2026-02-01')
            ->set('date_to', '2026-02-28')
            ->assertViewHas('records', function ($records) {
                return $records->count() === 1 && $records->first()->source === 'February Borrower';
            })
            ->assertViewHas('stats', function ($stats) {
                return (int) $stats['total_invested'] === 10000;
            });

        \Carbon\Carbon::setTestNow();
    }
}