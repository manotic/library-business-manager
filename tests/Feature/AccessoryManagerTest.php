<?php

namespace Tests\Feature;

use App\Livewire\AccessoryManager;
use App\Models\Accessory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AccessoryManagerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function component_renders_successfully()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(AccessoryManager::class)
            ->assertStatus(200)
            ->assertSee('Accessory Inventory');
    }

    #[Test]
    public function can_create_accessory_sale()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(AccessoryManager::class)
            ->set('accessory_name', 'Power Bank')
            ->set('buying_amount', 1000)
            ->set('selling_amount', 1500)
            ->set('paid_amount', 500)
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('notify');

        $this->assertDatabaseHas('accessories', [
            'accessory_name' => 'Power Bank',
            'paid_amount' => 500
        ]);
    }

    #[Test]
    public function can_add_installments_to_existing_record()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $record = Accessory::factory()->create([
            'user_id' => $user->id,
            'paid_amount' => 100
        ]);

        Livewire::test(AccessoryManager::class)
            ->set('additional_payment.' . $record->id, 200)
            ->call('addInstallment', $record->id)
            ->assertDispatched('notify');

        $this->assertEquals(300, $record->fresh()->paid_amount);
    }

    #[Test]
    public function profit_calculates_correctly_using_cost_first_recovery()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Record 1: Buy 500, Paid 400 (Profit should be 0 because cost not recovered)
        Accessory::factory()->create([
            'user_id' => $user->id,
            'buying_amount' => 500,
            'selling_amount' => 1000,
            'paid_amount' => 400,
        ]);

        // Record 2: Buy 500, Paid 800 (Profit should be 300)
        Accessory::factory()->create([
            'user_id' => $user->id,
            'buying_amount' => 500,
            'selling_amount' => 1000,
            'paid_amount' => 800,
        ]);

        /** Logic Check:
         * Total Invested: 1000
         * Actual Collected: 1200
         * Expected Profit: 300 (Only from Record 2)
         * Expected Debt: 800 (600 from Rec 1 + 200 from Rec 2)
         */

        Livewire::test(AccessoryManager::class)
            ->set('showData', true) // Bypass privacy blur
            ->assertViewHas('stats', function ($stats) {
                return $stats['profit'] == 300 && 
                       $stats['pending_debt'] == 800;
            })
            ->assertSee('300') 
            ->assertSee('800');
    }

    #[Test]
    public function it_filters_by_status()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Fully Paid
        Accessory::factory()->create([
            'user_id' => $user->id,
            'accessory_name' => 'Paid Item',
            'selling_amount' => 500,
            'paid_amount' => 500,
        ]);

        // Debt
        Accessory::factory()->create([
            'user_id' => $user->id,
            'accessory_name' => 'Debt Item',
            'selling_amount' => 500,
            'paid_amount' => 100,
        ]);

        Livewire::test(AccessoryManager::class)
            ->set('filter_status', 'paid')
            ->assertViewHas('records', fn($records) => $records->count() === 1 && $records->first()->accessory_name === 'Paid Item')
            ->set('filter_status', 'debt')
            ->assertViewHas('records', fn($records) => $records->count() === 1 && $records->first()->accessory_name === 'Debt Item');
    }

    #[Test]
    public function it_displays_stats_and_records_for_current_month_by_default()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Freeze time to March 2026
        \Carbon\Carbon::setTestNow('2026-03-15');

        // Record 1: Last Month (February) - Should be ignored
        Accessory::factory()->create([
            'user_id' => $user->id,
            'accessory_name' => 'Old Stock',
            'buying_amount' => 100,
            'created_at' => now()->subMonth(), 
        ]);

        // Record 2: This Month (March) - Should be counted
        Accessory::factory()->create([
            'user_id' => $user->id,
            'accessory_name' => 'Current Stock',
            'buying_amount' => 500,
            'created_at' => now(), 
        ]);

        Livewire::test(AccessoryManager::class)
            ->assertViewHas('stats', function ($stats) {
                // Total invested should only see the 500 from March
                return (int)$stats['total_invested'] === 500;
            })
            ->assertViewHas('records', function ($records) {
                // Should only show 1 record in the table
                return $records->count() === 1 && $records->first()->accessory_name === 'Current Stock';
            });

        \Carbon\Carbon::setTestNow(); // Reset time
    }

    #[Test]
    public function it_updates_accessory_data_when_manually_changing_date_range()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Freeze time
        \Carbon\Carbon::setTestNow('2026-03-15');

        // Create a record in February
        Accessory::factory()->create([
            'user_id' => $user->id,
            'accessory_name' => 'February Item',
            'buying_amount' => 1000,
            'created_at' => '2026-02-10',
        ]);

        Livewire::test(AccessoryManager::class)
            // Initially, March view is empty
            ->assertViewHas('records', fn($records) => $records->count() === 0)
            // Manually set range to February
            ->set('date_from', '2026-02-01')
            ->set('date_to', '2026-02-28')
            ->assertViewHas('records', function ($records) {
                return $records->count() === 1 && $records->first()->accessory_name === 'February Item';
            })
            ->assertViewHas('stats', function ($stats) {
                return (int)$stats['total_invested'] === 1000;
            });

        \Carbon\Carbon::setTestNow();
    }
}