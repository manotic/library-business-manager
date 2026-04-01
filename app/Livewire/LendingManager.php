<?php

namespace App\Livewire;

use App\Models\Lending;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class LendingManager extends Component
{
    use WithPagination;

    // Form fields
    public $source, $phone, $amount, $description;

    // UI state & Filters
    public $search = '';
    public $date_from;
    public $date_to;
    public $filter_status = ''; // 'paid' or 'debt'
    public $showData = false;
    public $additional_payment = [];

    protected $rules = [
        'source' => 'required|min:3',
        'amount' => 'required|numeric|min:1',
        'phone' => 'nullable',
        'description' => 'nullable',
    ];

    // Reset pagination when any filter changes
    public function updatedSearch() { $this->resetPage(); }
    public function updatedDateFrom() { $this->resetPage(); }
    public function updatedDateTo() { $this->resetPage(); }
    public function updatedFilterStatus() { $this->resetPage(); }

    public function togglePrivacy()
    {
        $this->showData = !$this->showData;
    }

    public function save()
    {
        $this->validate();

        Lending::create([
            'user_id' => auth()->id(),
            'source' => $this->source,
            'phone' => $this->phone,
            'amount' => $this->amount,
            'description' => $this->description,
        ]);

        $this->reset(['source', 'phone', 'amount', 'description']);
        $this->dispatch('notify', 'Loan Recorded Successfully!');
    }

    public function addInstallment($id)
    {
        $amountToReturn = $this->additional_payment[$id] ?? 0;
        if ($amountToReturn <= 0) return;

        $loan = Lending::findOrFail($id);
        $loan->increment('amount_returned', $amountToReturn);

        $this->additional_payment[$id] = '';
        $this->dispatch('notify', 'Payment Received!');
    }

    public function render()
    {
        // 1. Setup Date Range (Monthly default if inputs are empty)
        $start = $this->date_from ?: Carbon::now()->startOfMonth()->toDateString();
        $end = $this->date_to ?: Carbon::now()->endOfMonth()->toDateString();

        // 2. Base Query (Filtered ONLY by date - used for Stats)
        $baseQuery = Lending::where('user_id', auth()->id())
            ->whereDate('created_at', '>=', $start)
            ->whereDate('created_at', '<=', $end);

        // 3. Table Query (Base Query + Search + Status - used for Table)
        $records = (clone $baseQuery)
            ->where('source', 'like', '%' . $this->search . '%')
            ->when($this->filter_status, function ($q) {
                if ($this->filter_status === 'paid') {
                    return $q->whereRaw('amount_returned >= amount');
                }
                if ($this->filter_status === 'debt') {
                    return $q->whereRaw('amount_returned < amount');
                }
            })
            ->latest()
            ->paginate(10);

        // 4. Calculate Stats strictly within the date range
        $stats = [
            'total_invested' => (clone $baseQuery)->sum('amount'),
            'actual_collected' => (clone $baseQuery)->sum('amount_returned'),
            'pending_debt' => (clone $baseQuery)->get()->sum(fn ($l) => $l->amount - $l->amount_returned),
        ];

        return view('livewire.lending-manager', [
            'records' => $records,
            'stats' => $stats,
        ]);
    }
}