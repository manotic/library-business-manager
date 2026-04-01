<div class="space-y-6" x-data="{ 
    privacy: @entangle('showData'),
    notification: '',
    showNotify: false 
}" x-on:notify.window="notification = $event.detail; showNotify = true; setTimeout(() => showNotify = false, 3000)">

    <div x-show="showNotify" x-transition class="fixed bottom-10 left-1/2 -translate-x-1/2 z-[100] bg-indigo-600 text-white px-8 py-4 rounded-full shadow-2xl flex items-center gap-3">
        <span class="font-bold" x-text="notification"></span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-gray-800 border border-gray-700 p-4 rounded-2xl border-l-4 border-l-indigo-500">
            <p class="text-gray-400 text-[10px] font-bold uppercase tracking-wider">Total Outside Income</p>
            <p class="text-xl font-black text-white" :class="!privacy && 'blur-md'">{{ number_format($stats['total_income']) }}</p>
        </div>
        <div class="bg-gray-800 border border-gray-700 p-4 rounded-2xl">
            <p class="text-gray-400 text-[10px] font-bold uppercase">Entries Count</p>
            <p class="text-xl font-black text-white">{{ $stats['entry_count'] }}</p>
        </div>
        <div class="bg-gray-800 border border-gray-700 p-4 rounded-2xl">
            <p class="text-gray-400 text-[10px] font-bold uppercase">Average Per Entry</p>
            <p class="text-xl font-black text-green-400" :class="!privacy && 'blur-md'">
                {{ $stats['entry_count'] > 0 ? number_format($stats['total_income'] / $stats['entry_count']) : 0 }}
            </p>
        </div>
    </div>

    <div class="bg-gray-800 p-6 rounded-xl border border-gray-700 space-y-4">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-bold text-white italic">Outside Income Dashboard</h2>
            <button @click="privacy = !privacy" class="p-2.5 bg-gray-700 rounded-xl text-white transition-all shadow-lg" :class="privacy ? 'bg-indigo-600 ring-4 ring-indigo-500/20' : ''">
                <i :data-lucide="privacy ? 'eye' : 'eye-off'" class="w-5 h-5"></i>
            </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <input type="text" wire:model.live="search" placeholder="Search source..." class="p-3 bg-gray-900 border-gray-700 rounded-lg text-sm text-white focus:ring-indigo-500">
            <input type="date" wire:model.live="date_from" class="p-3 bg-gray-900 border-gray-700 rounded-lg text-sm text-white">
            <input type="date" wire:model.live="date_to" class="p-3 bg-gray-900 border-gray-700 rounded-lg text-sm text-white">
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="lg:col-span-4 bg-gray-800 border border-gray-700 rounded-xl p-6 h-fit">
            <h3 class="text-white font-bold mb-4 flex items-center gap-2">
                <i data-lucide="plus-circle" class="w-5 h-5 text-indigo-400"></i> Record Income
            </h3>
            <form wire:submit.prevent="save" class="space-y-4">
                <input type="text" wire:model="source" placeholder="Source Name (e.g., Client X, Gift)" class="p-3 w-full bg-gray-900 border-gray-700 rounded-lg text-white">
                <input type="text" wire:model="phone" placeholder="Contact Number (Optional)" class="p-3 w-full bg-gray-900 border-gray-700 rounded-lg text-white">
                <input type="number" wire:model="amount" placeholder="Amount Received" class="p-3 w-full bg-gray-900 border-gray-700 rounded-lg text-white">
                <textarea wire:model="description" placeholder="Description / Notes (Optional)" class="p-3 w-full bg-gray-900 border-gray-700 rounded-lg text-white h-24"></textarea>
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 rounded-xl shadow-lg transition-transform active:scale-95">SAVE INCOME ENTRY</button>
            </form>
        </div>

        <div class="lg:col-span-8 bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-700/50 text-gray-300 uppercase text-[10px]">
                        <tr>
                            <th class="px-4 py-4">Date</th>
                            <th class="px-4 py-4">Source Detail</th>
                            <th class="px-4 py-4">Amount</th>
                            <th class="px-4 py-4">Note</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @foreach($records as $record)
                        <tr class="hover:bg-gray-700/30 transition-colors">
                            <td class="px-4 py-4 text-gray-500 font-mono text-[10px]">
                                {{ $record->created_at->format('d M, Y') }}
                            </td>
                            <td class="px-4 py-4">
                                <p class="font-bold text-indigo-400">{{ $record->source }}</p>
                                <p class="text-[10px] text-gray-500">{{ $record->phone ?? '—' }}</p>
                            </td>
                            <td class="px-4 py-4" :class="!privacy && 'blur-md'">
                                <p class="text-green-400 font-bold">+ {{ number_format($record->amount) }}</p>
                            </td>
                            <td class="px-4 py-4">
                                <p class="text-gray-400 text-[11px] italic leading-tight">
                                    {{ Str::limit($record->description, 50) ?? 'No notes' }}
                                </p>
                            </td>
                        </tr>
                        @endforeach
                        @if($records->isEmpty())
                        <tr>
                            <td colspan="4" class="px-4 py-10 text-center text-gray-500 italic">No records found for this period.</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="p-4 bg-gray-900/30 border-t border-gray-700">
                {{ $records->links() }}
            </div>
        </div>
    </div>
</div>