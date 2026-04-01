<?php

namespace App\Livewire;

use App\Models\OutIncome;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class OutIncomeManager extends Component
{
    use WithPagination;

    // Form fields
    public $source;

    public $phone;

    public $amount;

    public $description;

    // UI state & Filters
    public $search = '';

    public $date_from;

    public $date_to;

    public $showData = false;

    protected $rules = [
        'source' => 'required|min:3',
        'amount' => 'required|numeric|min:1',
        'phone' => 'nullable',
        'description' => 'nullable',
    ];

    // Reset pagination when filters change to avoid empty state bugs
    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedDateFrom()
    {
        $this->resetPage();
    }

    public function updatedDateTo()
    {
        $this->resetPage();
    }

    public function togglePrivacy()
    {
        $this->showData = ! $this->showData;
    }

    public function save()
    {
        $this->validate();

        OutIncome::create([
            'user_id' => auth()->id(),
            'source' => $this->source,
            'phone' => $this->phone,
            'amount' => $this->amount,
            'description' => $this->description,
        ]);

        $this->reset(['source', 'phone', 'amount', 'description']);
        $this->dispatch('notify', 'Income Recorded Successfully!');
    }

    public function render()
    {
        // 1. Determine the Date Range (Invisible Monthly Filter)
        $start = $this->date_from ?: Carbon::now()->startOfMonth()->toDateString();
        $end = $this->date_to ?: Carbon::now()->endOfMonth()->toDateString();

        // 2. Base Query (Filtered ONLY by date range - used for Stats)
        $baseQuery = OutIncome::where('user_id', auth()->id())
            ->whereDate('created_at', '>=', $start)
            ->whereDate('created_at', '<=', $end);

        // 3. Stats Calculation (Strictly following the date range)
        $stats = [
            'total_income' => (clone $baseQuery)->sum('amount'),
            'entry_count' => (clone $baseQuery)->count(),
        ];

        // 4. Records Query (Base Query + Search - used for Table)
        $records = (clone $baseQuery)
            ->where('source', 'like', '%'.$this->search.'%')
            ->latest()
            ->paginate(10);

        return view('livewire.out-income-manager', [
            'records' => $records,
            'stats' => $stats,
        ]);
    }
}
