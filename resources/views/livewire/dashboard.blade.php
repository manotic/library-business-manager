<div class="space-y-6" x-data="{ privacy: @entangle('showData') }">

    <div class="bg-gray-800 p-6 rounded-2xl border border-gray-700 flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h1 class="text-2xl font-black text-white italic tracking-tighter">FINANCIAL COMMAND CENTER</h1>
            <p class="text-gray-400 text-xs font-bold uppercase tracking-widest">Business Overview & Analysis</p>
        </div>
        
        <div class="flex items-center gap-3">
            <input type="date" wire:model.live="date_from" class="p-2.5 bg-gray-900 border-gray-700 rounded-xl text-sm text-white">
            <input type="date" wire:model.live="date_to" class="p-2.5 bg-gray-900 border-gray-700 rounded-xl text-sm text-white">
            <button @click="privacy = !privacy" class="p-2.5 rounded-xl transition-all shadow-lg" 
                :class="privacy ? 'bg-indigo-600 text-white ring-4 ring-indigo-500/20' : 'bg-gray-700 text-gray-400'">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-gray-800 border border-gray-700 p-6 rounded-2xl border-t-4 border-t-blue-500">
            <p class="text-gray-500 text-[10px] font-black uppercase tracking-widest">Total Revenue</p>
            <p class="text-3xl font-black text-white mt-1" :class="!privacy && 'blur-lg'">{{ number_format($stats['total_revenue']) }}</p>
        </div>
        <div class="bg-gray-800 border border-gray-700 p-6 rounded-2xl border-t-4 border-t-red-500">
            <p class="text-gray-500 text-[10px] font-black uppercase tracking-widest">Operating Expenses</p>
            <p class="text-3xl font-black text-red-400 mt-1" :class="!privacy && 'blur-lg'">{{ number_format($stats['total_expenses']) }}</p>
        </div>
        <div class="bg-gray-800 border border-gray-700 p-6 rounded-2xl border-t-4 border-t-green-500 shadow-xl shadow-green-500/5">
            <p class="text-gray-500 text-[10px] font-black uppercase tracking-widest">Net Profit (Cash in Hand)</p>
            <p class="text-3xl font-black text-green-400 mt-1" :class="!privacy && 'blur-lg'">{{ number_format($stats['net_profit']) }}</p>
        </div>
        <div class="bg-gray-800 border border-gray-700 p-6 rounded-2xl border-t-4 border-t-yellow-500">
            <p class="text-gray-500 text-[10px] font-black uppercase tracking-widest">Market Risk (Debt)</p>
            <p class="text-3xl font-black text-yellow-500 mt-1" :class="!privacy && 'blur-lg'">{{ number_format($stats['total_debt']) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <div class="bg-gray-800 border border-gray-700 rounded-2xl overflow-hidden">
            <div class="p-5 border-b border-gray-700 bg-gray-700/20">
                <h3 class="text-white font-bold text-sm uppercase italic">Revenue by Sector</h3>
            </div>
            <div class="p-6 space-y-4">
                @foreach($revenue_breakdown as $sector => $val)
                <div>
                    <div class="flex justify-between text-xs mb-1.5">
                        <span class="text-gray-400 font-bold uppercase">{{ $sector }}</span>
                        <span class="text-white font-mono" :class="!privacy && 'blur-sm'">{{ number_format($val) }}</span>
                    </div>
                    <div class="w-full bg-gray-900 rounded-full h-1.5">
                        <div class="bg-indigo-500 h-1.5 rounded-full" style="width: {{ $stats['total_revenue'] > 0 ? ($val / $stats['total_revenue'] * 100) : 0 }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="bg-gray-800 border border-gray-700 rounded-2xl p-6 space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-[10px] font-black uppercase italic">Expense Ratio</p>
                    <p class="text-2xl font-black text-white">{{ $stats['efficiency'] }}%</p>
                </div>
                <div class="text-right">
                    <p class="text-gray-500 text-[10px] font-black uppercase italic">Top Money Pit</p>
                    <p class="text-xl font-black text-red-500 uppercase">{{ $stats['top_expense'] }}</p>
                </div>
            </div>

            <hr class="border-gray-700">

            <div>
                <h3 class="text-white font-bold text-sm uppercase mb-4">Debt Exposure Breakdown</h3>
                <div class="grid grid-cols-2 gap-4">
                    @foreach($debt_breakdown as $sector => $debt)
                    <div class="bg-gray-900/50 p-4 rounded-xl border border-gray-700">
                        <p class="text-gray-500 text-[9px] font-bold uppercase">{{ $sector }} Debt</p>
                        <p class="text-lg font-black text-white" :class="!privacy && 'blur-md'">{{ number_format($debt) }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    
</div>

