<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class AccessoryManager extends Component
{
    use WithPagination;

    // Form Fields
    public $accessory_name, $buying_amount, $selling_amount, $paid_amount = 0, $name, $contact;

    // Installment Field
    public $additional_payment = [];

    // Filters
    public $search = '', $date_from, $date_to, $filter_status = '', $showData = false;

    // Reset pagination when filters change to avoid showing empty pages
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
        $this->validate([
            'accessory_name' => 'required|string',
            'buying_amount' => 'required|numeric|min:0',
            'selling_amount' => 'required|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
        ]);

        Auth::user()->accessories()->create([
            'accessory_name' => $this->accessory_name,
            'buying_amount' => $this->buying_amount,
            'selling_amount' => $this->selling_amount,
            'paid_amount' => $this->paid_amount ?? 0,
            'name' => $this->name,
            'contact' => $this->contact,
        ]);

        $this->reset(['accessory_name', 'buying_amount', 'selling_amount', 'paid_amount', 'name', 'contact']);
        $this->dispatch('notify', 'Accessory added successfully!');
    }

    public function addInstallment($id)
    {
        $amount = $this->additional_payment[$id] ?? 0;
        if ($amount <= 0) return;

        $record = Auth::user()->accessories()->findOrFail($id);
        $record->increment('paid_amount', $amount);

        $this->additional_payment[$id] = '';
        $this->dispatch('notify', 'Installment updated!');
    }

    public function render()
    {
        // 1. Setup Date Range (Monthly default if inputs are empty)
        $start = $this->date_from ?: Carbon::now()->startOfMonth()->toDateString();
        $end = $this->date_to ?: Carbon::now()->endOfMonth()->toDateString();

        // 2. Base Query (Filtered ONLY by date - used for Stats)
        $baseQuery = Auth::user()->accessories()
            ->whereDate('created_at', '>=', $start)
            ->whereDate('created_at', '<=', $end);

        // 3. Table Query (Base Query + Search + Status - used for Table)
        $tableQuery = (clone $baseQuery)
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('accessory_name', 'like', '%' . $this->search . '%')
                        ->orWhere('name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filter_status !== '', function ($q) {
                if ($this->filter_status == 'paid') $q->whereRaw('paid_amount >= selling_amount');
                if ($this->filter_status == 'debt') $q->whereRaw('paid_amount < selling_amount');
            });

        // 4. Calculate Stats based strictly on the Date Range
        $statsRecords = (clone $baseQuery)->get();

        $stats = [
            'total_invested' => $statsRecords->sum('buying_amount'),
            'actual_collected' => $statsRecords->sum('paid_amount'),
            'pending_debt' => $statsRecords->sum(fn($i) => (float) $i->selling_amount - (float) $i->paid_amount),
            'profit' => $statsRecords->sum(function ($item) {
                $earned = (float) $item->paid_amount - (float) $item->buying_amount;
                return $earned > 0 ? $earned : 0;
            }),
            'expected_revenue' => $statsRecords->sum('selling_amount'),
        ];

        return view('livewire.accessory-manager', [
            'records' => $tableQuery->latest()->paginate(10),
            'stats' => $stats,
        ]);
    }
}