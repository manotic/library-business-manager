<?php

namespace App\Livewire;

use App\Models\Wifi;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class WifiManager extends Component
{
    use WithPagination;

    public $name, $mac, $amount, $hours, $is_debt = false;
    public $search = '';
    public $date_from, $date_to;
    public $filter_debt = '';
    public $showData = false;

    // Reset pagination when filters change
    public function updatedSearch() { $this->resetPage(); }
    public function updatedDateFrom() { $this->resetPage(); }
    public function updatedDateTo() { $this->resetPage(); }
    public function updatedFilterDebt() { $this->resetPage(); }

    public function togglePrivacy() { $this->showData = !$this->showData; }

    public function toggleDebt($id)
    {
        $wifi = auth()->user()->wifis()->findOrFail($id);
        $wifi->update(['is_debt' => !$wifi->is_debt]);
        $this->dispatch('notify', 'Status updated!');
    }

    public function save()
    {
        $this->validate([
            'amount' => 'required|numeric',
            'hours' => 'nullable|numeric',
        ]);

        auth()->user()->wifis()->create([
            'name' => $this->name ?: 'Standard User',
            'mac' => $this->mac ?: '00:00:00:00:00:00',
            'amount' => $this->amount,
            'is_debt' => (bool)$this->is_debt,
            'expires_at' => $this->hours ? now()->addHours((int) $this->hours) : now()->endOfDay(),
        ]);

        $this->reset(['name', 'mac', 'amount', 'hours', 'is_debt']);
        $this->dispatch('notify', 'Success! New record added.'); 
    }

    public function render()
    {
        // 1. Determine the Date Range (Invisible Monthly Filter)
        $start = $this->date_from ?: Carbon::now()->startOfMonth()->toDateString();
        $end = $this->date_to ?: Carbon::now()->endOfMonth()->toDateString();

        // 2. Build the Base Query using the determined range
        $baseQuery = auth()->user()->wifis()
            ->whereDate('created_at', '>=', $start)
            ->whereDate('created_at', '<=', $end);

        // 3. Apply Search and Debt Filters to the Table Query
        $tableQuery = (clone $baseQuery)
            ->where(function($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                  ->orWhere('mac', 'like', '%'.$this->search.'%');
            })
            ->when($this->filter_debt !== '', fn($q) => $q->where('is_debt', $this->filter_debt));

        // 4. Calculate Totals for the stats box using the FILTERED baseQuery
        // This ensures the boxes update when you pick a date range
        $totalRevenue = (clone $baseQuery)->sum('amount');
        $pendingDebt = (clone $baseQuery)->where('is_debt', true)->sum('amount');

        return view('livewire.wifi-manager', [
            'records' => $tableQuery->latest()->paginate(10),
            'stats' => [
                'monthly' => $totalRevenue, // This is now filtered by the chosen month/range
                'debt' => $pendingDebt,
                'profit' => $totalRevenue - $pendingDebt, 
            ]
        ]);
    }
}