<?php

namespace App\Livewire;

use App\Models\Accessory;
use App\Models\Expense;
use App\Models\Lending;
use App\Models\Library;
use App\Models\OutIncome;
use App\Models\Wifi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public $date_from;
    public $date_to;
    public $showData = false;

    // Reset components when dates change
    public function updatedDateFrom() { }
    public function updatedDateTo() { }

    public function togglePrivacy()
    {
        $this->showData = !$this->showData;
    }

    public function render()
    {
        $user = Auth::user();

        // 1. Setup Date Range (Monthly default)
        $start = $this->date_from ?: Carbon::now()->startOfMonth()->toDateTimeString();
        $end = $this->date_to ?: Carbon::now()->endOfMonth()->toDateTimeString();

        // 2. Fetch Data within Range for each Model
        $wifi = $user->wifis()->whereBetween('created_at', [$start, $end])->get();
        $accessories = $user->accessories()->whereBetween('created_at', [$start, $end])->get();
        $libraries = $user->libraries()->whereBetween('created_at', [$start, $end])->get();
        $lendings = $user->lendings()->whereBetween('created_at', [$start, $end])->get();
        $outIncomes = $user->outIncomes()->whereBetween('created_at', [$start, $end])->get();
        $expenses = $user->expenses()->whereBetween('created_at', [$start, $end])->get();

        // 3. Primary Calculations (Revenue)
        $revenue = [
            'wifi' => $wifi->sum('amount'),
            'accessories' => $accessories->sum('paid_amount'),
            'library' => $libraries->sum('amount'),
            'out_income' => $outIncomes->sum('amount'),
        ];
        
        $totalRevenue = array_sum($revenue);
        $totalExpenses = $expenses->sum('amount');

        // 4. Debt/Risk Calculations
        $debts = [
            'wifi' => $wifi->where('is_debt', true)->sum('amount'),
            'accessories' => $accessories->sum(fn($i) => (float)$i->selling_amount - (float)$i->paid_amount),
            'library' => $libraries->where('is_debt', true)->sum('amount'),
            'lending' => $lendings->sum(fn($l) => (float)$l->amount - (float)$l->amount_returned),
        ];

        $totalDebt = array_sum($debts);

        // 5. Advanced Analysis
        $stats = [
            'total_revenue' => $totalRevenue,
            'total_expenses' => $totalExpenses,
            'net_profit' => $totalRevenue - $totalExpenses,
            'total_debt' => $totalDebt,
            'efficiency' => $totalRevenue > 0 ? round(($totalExpenses / $totalRevenue) * 100, 1) : 0,
            'top_expense' => $expenses->groupBy('category')
                                ->map->sum('amount')
                                ->sortDesc()
                                ->keys()
                                ->first() ?? 'N/A',
        ];

        return view('livewire.dashboard', [
            'stats' => $stats,
            'revenue_breakdown' => $revenue,
            'debt_breakdown' => $debts,
        ])->layout('layouts.app');
    }
}