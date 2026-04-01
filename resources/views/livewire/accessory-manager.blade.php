<div class="space-y-6" x-data="{ 
    privacy: @entangle('showData'),
    notification: '',
    showNotify: false 
}" x-on:notify.window="notification = $event.detail; showNotify = true; setTimeout(() => showNotify = false, 3000)">

    <div x-show="showNotify" x-transition class="fixed bottom-10 left-1/2 -translate-x-1/2 z-[100] bg-indigo-600 text-white px-8 py-4 rounded-full shadow-2xl flex items-center gap-3">
        <span class="font-bold" x-text="notification"></span>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="bg-gray-800 border border-gray-700 p-4 rounded-2xl">
            <p class="text-gray-400 text-[10px] font-bold uppercase">Invested</p>
            <p class="text-xl font-black text-white" :class="!privacy && 'blur-md'">{{ number_format($stats['total_invested']) }}</p>
        </div>
        <div class="bg-gray-800 border border-gray-700 p-4 rounded-2xl">
            <p class="text-gray-400 text-[10px] font-bold uppercase">Collected</p>
            <p class="text-xl font-black text-white" :class="!privacy && 'blur-md'">{{ number_format($stats['actual_collected']) }}</p>
        </div>
        <div class="bg-gray-800 border border-gray-700 p-4 rounded-2xl border-l-4 border-l-green-500">
            <p class="text-gray-400 text-[10px] font-bold uppercase text-green-400">Net Profit</p>
            <p class="text-xl font-black text-green-400" :class="!privacy && 'blur-md'">{{ number_format($stats['profit']) }}</p>
        </div>
        <div class="bg-gray-800 border border-gray-700 p-4 rounded-2xl border-l-4 border-l-red-500">
            <p class="text-gray-400 text-[10px] font-bold uppercase text-red-400">Total Debt</p>
            <p class="text-xl font-black text-red-400" :class="!privacy && 'blur-md'">{{ number_format($stats['pending_debt']) }}</p>
        </div>
        <div class="bg-gray-800 border border-gray-700 p-4 rounded-2xl">
            <p class="text-gray-400 text-[10px] font-bold uppercase">Forecast</p>
            <p class="text-xl font-black text-indigo-400" :class="!privacy && 'blur-md'">{{ number_format($stats['expected_revenue']) }}</p>
        </div>
    </div>

    <div class="bg-gray-800 p-6 rounded-xl border border-gray-700 space-y-4">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-bold text-white italic">Accessory Inventory</h2>
            <button @click="privacy = !privacy" class="p-2.5 bg-gray-700 rounded-xl text-white transition-all shadow-lg" :class="privacy ? 'bg-indigo-600 ring-4 ring-indigo-500/20' : ''">
                <i :data-lucide="privacy ? 'eye' : 'eye-off'" class="w-5 h-5"></i>
            </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="text" wire:model.live="search" placeholder="Search item or client..." class="p-3 bg-gray-900 border-gray-700 rounded-lg text-sm text-white focus:ring-indigo-500">
            <input type="date" wire:model.live="date_from" class="p-3 bg-gray-900 border-gray-700 rounded-lg text-sm text-white">
            <input type="date" wire:model.live="date_to" class="p-3 bg-gray-900 border-gray-700 rounded-lg text-sm text-white">
            <select wire:model.live="filter_status" class="p-3 bg-gray-900 border-gray-700 rounded-lg text-sm text-white">
                <option value="">All Status</option>
                <option value="paid">Fully Paid</option>
                <option value="debt">Remaining Balance</option>
            </select>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="lg:col-span-4 bg-gray-800 border border-gray-700 rounded-xl p-6 h-fit">
            <h3 class="text-white font-bold mb-4 flex items-center gap-2">
                <i data-lucide="plus-circle" class="w-5 h-5 text-indigo-400"></i> New Sale
            </h3>
            <form wire:submit.prevent="save" class="space-y-4">
                <input type="text" wire:model="accessory_name" placeholder="Accessory Name" class="p-3 w-full bg-gray-900 border-gray-700 rounded-lg text-white">
                <div class="grid grid-cols-2 gap-4">
                    <input type="number" wire:model="buying_amount" placeholder="Buy Price" class="p-3 bg-gray-900 border-gray-700 rounded-lg text-white">
                    <input type="number" wire:model="selling_amount" placeholder="Sell Price" class="p-3 bg-gray-900 border-gray-700 rounded-lg text-white">
                </div>
                <input type="number" wire:model="paid_amount" placeholder="Initial Paid Amount" class="p-3 w-full bg-gray-900 border-gray-700 rounded-lg text-white">
                <input type="text" wire:model="name" placeholder="Client Name (Optional)" class="p-3 w-full bg-gray-900 border-gray-700 rounded-lg text-white">
                <input type="text" wire:model="contact" placeholder="Contact Info (Optional)" class="p-3 w-full bg-gray-900 border-gray-700 rounded-lg text-white">
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 rounded-xl shadow-lg transition-transform active:scale-95">RECORD SALE</button>
            </form>
        </div>

        <div class="lg:col-span-8 bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-700/50 text-gray-300 uppercase text-[10px]">
                        <tr>
                            <th class="px-4 py-4">Date</th>
                            <th class="px-4 py-4">Item/Client</th>
                            <th class="px-4 py-4">Financials</th>
                            <th class="px-4 py-4">Balance</th>
                            <th class="px-4 py-4 text-center">Installment</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @foreach($records as $record)
                        @php $isPaid = $record->paid_amount >= $record->selling_amount; @endphp
                        <tr class="hover:bg-gray-700/30 transition-colors">
                            <td class="px-4 py-4 text-gray-500 font-mono text-[10px]">
                                {{ $record->created_at->format('d M, Y') }}
                            </td>
                            <td class="px-4 py-4">
                                <p class="font-bold text-indigo-400">{{ $record->accessory_name }}</p>
                                <p class="text-[10px] text-gray-500">{{ $record->name ?? 'Walk-in' }} {{ $record->contact ? '('.$record->contact.')' : '' }}</p>
                            </td>
                            <td class="px-4 py-4" :class="!privacy && 'blur-md'">
                                <p class="text-white font-bold">S: {{ number_format($record->selling_amount) }}</p>
                                <p class="text-[10px] text-gray-500">B: {{ number_format($record->buying_amount) }}</p>
                            </td>
                            <td class="px-4 py-4">
                                @if($isPaid)
                                    <span class="px-2 py-1 bg-green-500/10 text-green-500 text-[10px] font-bold rounded">FULLY PAID</span>
                                @else
                                    <p class="text-red-400 font-bold" :class="!privacy && 'blur-md'">{{ number_format($record->selling_amount - $record->paid_amount) }}</p>
                                    <p class="text-[9px] text-gray-500">Paid: {{ number_format($record->paid_amount) }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-center">
                                @if(!$isPaid)
                                    <div class="flex gap-2">
                                        <input type="number" wire:model="additional_payment.{{ $record->id }}" placeholder="+" class="w-20 p-1 bg-gray-900 border-gray-700 rounded text-xs text-white">
                                        <button wire:click="addInstallment({{ $record->id }})" class="p-1 bg-indigo-600 rounded text-white shadow-md active:scale-90 transition-transform">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                        </button>
                                    </div>
                                @else
                                    <i data-lucide="check-circle" class="w-5 h-5 mx-auto text-green-500"></i>
                                @endif
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