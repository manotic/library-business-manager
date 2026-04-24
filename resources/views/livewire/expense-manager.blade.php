<div class="space-y-6" x-data="{ privacy: @entangle('showData'), notification: '', showNotify: false }"
    x-on:notify.window="notification = $event.detail; showNotify = true; setTimeout(() => showNotify = false, 3000)">

    <div x-show="showNotify" x-transition
        class="fixed bottom-10 left-1/2 -translate-x-1/2 z-[100] bg-red-600 text-white px-8 py-4 rounded-full shadow-2xl flex items-center gap-3">
        <span class="font-bold" x-text="notification"></span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-gray-800 border border-gray-700 p-4 rounded-2xl border-l-4 border-l-red-500">
            <p class="text-gray-400 text-[10px] font-bold uppercase">Monthly Expenses</p>
            <p class="text-xl font-black text-white" :class="!privacy && 'blur-md'">
                {{ number_format($stats['total_spent']) }}
            </p>
        </div>
        <div class="bg-gray-800 border border-gray-700 p-4 rounded-2xl">
            <p class="text-gray-400 text-[10px] font-bold uppercase">Highest Spending</p>
            <p class="text-xl font-black text-indigo-400 uppercase tracking-tighter">{{ $stats['top_category'] }}</p>
        </div>
        <div class="bg-gray-800 border border-gray-700 p-4 rounded-2xl">
            <p class="text-gray-400 text-[10px] font-bold uppercase">Date Range</p>
            <p class="text-sm font-bold text-gray-300">
                @if(!$date_from && !$date_to) Current Month @else Custom Filter @endif
            </p>
        </div>
    </div>

    <div class="bg-gray-800 p-6 rounded-xl border border-gray-700 space-y-4">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-bold text-white italic">Expense Tracker</h2>
            <button @click="privacy = !privacy" class="p-2.5 bg-gray-700 rounded-xl text-white transition-all shadow-lg"
                :class="privacy ? 'bg-indigo-600 ring-4 ring-indigo-500/20' : ''">
                <i :data-lucide="privacy ? 'eye' : 'eye-off'" class="w-5 h-5"></i>
            </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="text" wire:model.live="search" placeholder="Search description..."
                class="p-3 bg-gray-900 border-gray-700 rounded-lg text-sm text-white focus:ring-red-500">
            <input type="date" wire:model.live="date_from"
                class="p-3 bg-gray-900 border-gray-700 rounded-lg text-sm text-white">
            <input type="date" wire:model.live="date_to"
                class="p-3 bg-gray-900 border-gray-700 rounded-lg text-sm text-white">
            <select wire:model.live="filter_category"
                class="p-3 bg-gray-900 border-gray-700 rounded-lg text-sm text-white">
                <option value="">All Categories</option>
                <option value="Rent">Rent</option>
                <option value="Food">Food</option>
                <option value="Electricity">Electricity</option>
                <option value="Stock">Stock/Inventory</option>
                <option value="Salaries">Salaries</option>
                <option value="Other">Other</option>
            </select>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="lg:col-span-4 bg-gray-800 border border-gray-700 rounded-xl p-6 h-fit">
            <h3 class="text-white font-bold mb-4 flex items-center gap-2">
                <i data-lucide="minus-circle" class="w-5 h-5 text-red-500"></i> New Expense
            </h3>
            <form wire:submit.prevent="save" class="space-y-4">
                <select wire:model="category" class="p-3 w-full bg-gray-900 border-gray-700 rounded-lg text-white">
                    <option value="">Select Category</option>
                    <option value="Rent">Rent</option>
                    <option value="Food">Food</option>
                    <option value="Bundle">Bundle</option>
                    <option value="Stock">Stock/Inventory</option>
                    <option value="Salaries">Salaries</option>
                    <option value="Other">Other</option>
                </select>
                <input type="number" wire:model="amount" placeholder="Amount"
                    class="p-3 w-full bg-gray-900 border-gray-700 rounded-lg text-white">
                <input type="date" wire:model="date"
                    class="p-3 w-full bg-gray-900 border-gray-700 rounded-lg text-white">
                <textarea wire:model="description" placeholder="Description (Optional)"
                    class="p-3 w-full bg-gray-900 border-gray-700 rounded-lg text-white h-20"></textarea>
                <button type="submit"
                    class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-4 rounded-xl shadow-lg transition-transform active:scale-95 uppercase tracking-widest">Record
                    Expense</button>
            </form>
        </div>

        <div class="lg:col-span-8 bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-700/50 text-gray-300 uppercase text-[10px]">
                        <tr>
                            <th class="px-4 py-4">Date</th>
                            <th class="px-4 py-4">Category</th>
                            <th class="px-4 py-4">Amount</th>
                            <th class="px-4 py-4">Description</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @foreach($records as $record)
                            <tr class="hover:bg-gray-700/30 transition-colors">
                                <td class="px-4 py-4 text-gray-500 font-mono text-[10px]">
                                    {{ \Carbon\Carbon::parse($record->date)->format('d M, Y') }}
                                </td>
                                <td class="px-4 py-4 font-bold text-red-400 uppercase text-[10px]">{{ $record->category }}
                                </td>
                                <td class="px-4 py-4 text-white font-bold" :class="!privacy && 'blur-md'">
                                    - {{ number_format($record->amount) }}
                                </td>
                                <td class="px-4 py-4 text-gray-400 italic text-[11px]">
                                    {{ $record->description ?? 'No details' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4 bg-gray-900/30 border-t border-gray-700">
                {{ $records->links() }}
            </div>
        </div>
    </div>
</div>