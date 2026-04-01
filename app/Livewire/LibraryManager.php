<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class LibraryManager extends Component
{
    use WithPagination;

    // Form Fields
    public $type = 'Movie';
    public $amount;
    public $is_debt = false;
    public $debtor_name;

    // Filters
    public $search = '';
    public $filter_type = '';
    public $filter_debt = '';
    public $date_from;
    public $date_to;
    public $showData = false;

    protected $rules = [
        'type' => 'required|in:Movie,Series,Songs',
        'amount' => 'required|numeric|min:0',
        'is_debt' => 'boolean',
        'debtor_name' => 'required_if:is_debt,true',
    ];

    // Reset pagination when any filter changes
    public function updatedSearch() { $this->resetPage(); }
    public function updatedFilterType() { $this->resetPage(); }
    public function updatedFilterDebt() { $this->resetPage(); }
    public function updatedDateFrom() { $this->resetPage(); }
    public function updatedDateTo() { $this->resetPage(); }

    public function togglePrivacy()
    {
        $this->showData = ! $this->showData;
    }

    public function save()
    {
        $this->validate();

        Auth::user()->libraries()->create([
            'type' => $this->type,
            'amount' => $this->amount,
            'is_debt' => $this->is_debt,
            'debtor_name' => $this->is_debt ? $this->debtor_name : null,
        ]);

        $this->reset(['amount', 'is_debt', 'debtor_name']);
        $this->dispatch('notify', 'Library entry saved successfully!');
    }

    public function toggleDebt($id)
    {
        $record = Auth::user()->libraries()->findOrFail($id);
        $record->update(['is_debt' => ! $record->is_debt]);
        $this->dispatch('notify', 'Status updated!');
    }

    public function render()
    {
        // 1. Determine the Date Range (Invisible Monthly Filter)
        $start = $this->date_from ?: Carbon::now()->startOfMonth()->toDateString();
        $end = $this->date_to ?: Carbon::now()->endOfMonth()->toDateString();

        // 2. Build the Base Query restricted by date only
        // This is used for all top-level stats
        $baseQuery = Auth::user()->libraries()
            ->whereDate('created_at', '>=', $start)
            ->whereDate('created_at', '<=', $end);

        // 3. Build the Table Query (Base Query + Search/Type/Debt filters)
        $tableQuery = (clone $baseQuery)
            ->when($this->search, fn ($q) => $q->where('debtor_name', 'like', '%'.$this->search.'%'))
            ->when($this->filter_type, fn ($q) => $q->where('type', $this->filter_type))
            ->when($this->filter_debt !== '', fn ($q) => $q->where('is_debt', $this->filter_debt));

        // 4. Calculate stats strictly within the date range
        $totalRevenue = (clone $baseQuery)->sum('amount');
        $pendingDebt = (clone $baseQuery)->where('is_debt', true)->sum('amount');

        $stats = [
            'movie'  => (clone $baseQuery)->where('type', 'Movie')->sum('amount'),
            'series' => (clone $baseQuery)->where('type', 'Series')->sum('amount'),
            'songs'  => (clone $baseQuery)->where('type', 'Songs')->sum('amount'),
            'total'  => $totalRevenue,
            'debt'   => $pendingDebt,
            'profit' => $totalRevenue - $pendingDebt,
        ];

        return view('livewire.library-manager', [
            'records' => $tableQuery->latest()->paginate(10),
            'stats' => $stats,
        ]);
    }
}