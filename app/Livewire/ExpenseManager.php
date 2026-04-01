<?php

namespace App\Livewire;

use App\Models\Expense;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class ExpenseManager extends Component
{
    use WithPagination;

    // Form fields
    public $category, $amount, $date, $description;
    
    // UI state & Filters
    public $search = '';
    public $date_from;
    public $date_to;
    public $filter_category = '';
    public $showData = false;

    protected $rules = [
        'category' => 'required',
        'amount' => 'required|numeric|min:1',
        'date' => 'nullable|date',
        'description' => 'nullable',
    ];

    // Reset pagination when filters change to avoid showing empty pages
    public function updatedSearch() { $this->resetPage(); }
    public function updatedDateFrom() { $this->resetPage(); }
    public function updatedDateTo() { $this->resetPage(); }
    public function updatedFilterCategory() { $this->resetPage(); }

    public function togglePrivacy() { $this->showData = !$this->showData; }

    public function save()
    {
        $this->validate();

        Expense::create([
            'user_id' => auth()->id(),
            'category' => $this->category,
            'amount' => $this->amount,
            'date' => $this->date ?? now()->toDateString(), 
            'description' => $this->description,
        ]);

        $this->reset(['category', 'amount', 'date', 'description']);
        $this->dispatch('notify', 'Expense Recorded!');
    }

    public function render()
    {
        // 1. Setup Date Range (Monthly default if inputs are empty)
        $start = $this->date_from ?: Carbon::now()->startOfMonth()->toDateString();
        $end = $this->date_to ?: Carbon::now()->endOfMonth()->toDateString();

        // 2. Base Query (Filtered ONLY by user and date - used for Stats)
        // We use whereDate for consistency across the app
        $baseQuery = Expense::where('user_id', auth()->id())
            ->whereDate('created_at', '>=', $start)
            ->whereDate('created_at', '<=', $end);

        // 3. Stats Calculation strictly within the date range
        $stats = [
            'total_spent' => (clone $baseQuery)->sum('amount'),
            'top_category' => (clone $baseQuery)->select('category')
                                ->groupBy('category')
                                ->orderByRaw('SUM(amount) DESC')
                                ->first()?->category ?? 'N/A',
        ];

        // 4. Records Query (Base Query + Search + Category Filter - used for Table)
        $records = (clone $baseQuery)
            ->when($this->filter_category, fn($q) => $q->where('category', $this->filter_category))
            ->where('description', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(10);

        return view('livewire.expense-manager', [
            'records' => $records,
            'stats' => $stats
        ]);
    }
}