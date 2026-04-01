<div class="space-y-6" x-data="{ 
    privacy: @entangle('showData'),
    notification: '',
    showNotify: false 
}" x-on:notify.window="notification = $event.detail; showNotify = true; setTimeout(() => showNotify = false, 3000)">

    <div x-show="showNotify" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-90"
         class="fixed bottom-10 left-1/2 -translate-x-1/2 z-[100] bg-indigo-600 text-white px-8 py-4 rounded-full shadow-2xl border border-indigo-400 flex items-center gap-3">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
        </svg>
        <span class="font-bold tracking-wide" x-text="notification"></span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-gray-800 border border-gray-700 p-5 rounded-2xl">
            <p class="text-gray-400 text-xs font-bold uppercase tracking-wider">This Month</p>
            <p class="text-2xl font-black text-white mt-1" :class="!privacy && 'blur-md'">{{ number_format($stats['monthly']) }}</p>
        </div>
        <div class="bg-gray-800 border border-gray-700 p-5 rounded-2xl border-l-4 border-l-green-500">
            <p class="text-gray-400 text-xs font-bold uppercase tracking-wider">Actual Profit</p>
            <p class="text-2xl font-black text-green-400 mt-1" :class="!privacy && 'blur-md'">{{ number_format($stats['profit']) }}</p>
        </div>
        <div class="bg-gray-800 border border-gray-700 p-5 rounded-2xl border-l-4 border-l-red-500">
            <p class="text-gray-400 text-xs font-bold uppercase tracking-wider">Pending Debt</p>
            <p class="text-2xl font-black text-red-400 mt-1" :class="!privacy && 'blur-md'">{{ number_format($stats['debt']) }}</p>
        </div>
    </div>

    <div class="bg-gray-800 p-6 rounded-xl border border-gray-700 space-y-4">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-bold text-white italic">Wifi Dashboard</h2>
            <button @click="$wire.togglePrivacy()" class="p-2.5 rounded-xl transition-all duration-300 shadow-lg" 
                :class="privacy ? 'bg-indigo-600 text-white ring-4 ring-indigo-500/20' : 'bg-gray-700 text-gray-400'">
                <template x-if="privacy">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </template>
                <template x-if="!privacy">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                    </svg>
                </template>
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="text" wire:model.live="search" placeholder="Search name or MAC..." class="p-3 bg-gray-900 border-gray-700 rounded-lg text-sm text-white focus:ring-indigo-500">
            <input type="date" wire:model.live="date_from" class="p-3 bg-gray-900 border-gray-700 rounded-lg text-sm text-white">
            <input type="date" wire:model.live="date_to" class="p-3 bg-gray-900 border-gray-900 border-gray-700 rounded-lg text-sm text-white">
            <select wire:model.live="filter_debt" class="p-3 bg-gray-900 border-gray-700 rounded-lg text-sm text-white">
                <option value="">All Status</option>
                <option value="1">Debt Only</option>
                <option value="0">Paid Only</option>
            </select>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="lg:col-span-4 bg-gray-800 border border-gray-700 rounded-xl p-6 h-fit">
            <h3 class="text-white font-bold mb-4">Add Entry</h3>
            <form wire:submit.prevent="save" class="space-y-4">
                <input type="text" wire:model="name" placeholder="Name" class="p-3 w-full bg-gray-900 border-gray-700 rounded-lg text-white">
                <input type="text" wire:model="mac" placeholder="MAC Address" class="p-3 w-full bg-gray-900 border-gray-700 rounded-lg text-white">
                <div class="grid grid-cols-2 gap-4">
                    <input type="number" wire:model="amount" placeholder="Amount" class="p-3 bg-gray-900 border-gray-700 rounded-lg text-white">
                    <input type="number" wire:model="hours" placeholder="Hours" class="p-3 bg-gray-900 border-gray-700 rounded-lg text-white">
                </div>
                <label class="flex items-center text-gray-400 text-sm cursor-pointer">
                    <input type="checkbox" wire:model="is_debt" class="rounded text-indigo-600 mr-2 bg-gray-900 border-gray-700"> Mark as Debt
                </label>
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 rounded-xl shadow-lg transition-transform active:scale-95">SAVE RECORD</button>
            </form>
        </div>

        <div class="lg:col-span-8 bg-gray-800 border border-gray-700 rounded-xl overflow-hidden flex flex-col">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-700/50 text-gray-300 text-[10px] uppercase">
                        <tr>
                            <th class="px-4 py-4">Date</th>
                            <th class="px-4 py-4">User/MAC</th>
                            <th class="px-4 py-4 text-center">Expiry</th>
                            <th class="px-4 py-4">Amount</th>
                            <th class="px-4 py-4 text-center">Debt</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @foreach($records as $record)
                        <tr class="hover:bg-gray-700/30 transition-colors">
                            <td class="px-4 py-4 text-gray-500 font-mono text-[10px]">
                                {{ $record->created_at->format('d M, Y') }}
                            </td>
                            <td class="px-4 py-4">
                                <p class="font-bold text-white" :class="!privacy && 'blur-md'">{{ $record->name }}</p>
                                <p class="text-[10px] text-gray-500 font-mono tracking-tighter" :class="!privacy && 'blur-sm'">{{ $record->mac }}</p>
                            </td>
                            <td class="px-4 py-4 text-center">
                                @if(now()->gt($record->expires_at))
                                    <span class="px-2 py-1 bg-red-500/10 text-red-500 font-black text-[9px] rounded uppercase">Expired</span>
                                @else
                                    <span class="text-green-400 font-mono">{{ $record->expires_at->format('H:i') }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 font-bold text-gray-200" :class="!privacy && 'blur-md'">
                                {{ number_format($record->amount) }}
                            </td>
                            <td class="px-4 py-4 text-center">
                                <button wire:click="toggleDebt({{ $record->id }})" 
                                    class="px-3 py-1 rounded-full text-[10px] font-bold transition-all {{ $record->is_debt ? 'bg-red-500/20 text-red-500 border border-red-500/50' : 'bg-gray-700 text-gray-400' }}">
                                    {{ $record->is_debt ? 'UNPAID' : 'PAID' }}
                                </button>
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