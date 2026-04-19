<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-800 border-r border-gray-700 flex flex-col transition-transform duration-300 ease-in-out lg:static lg:translate-x-0">

    <div class="p-6 flex items-center justify-between">
        <div class="text-xl font-bold text-indigo-500 tracking-tight italic">Business Admin</div>
        <button @click="sidebarOpen = false" class="lg:hidden text-gray-400 hover:text-white">
            <i data-lucide="x" class="w-6 h-6"></i>
        </button>
    </div>

    <nav class="flex-1 px-4 space-y-1 overflow-y-auto">
        <a href="{{ route('dashboard') }}" wire:navigate
            class="flex items-center p-3 rounded-lg transition group {{ request()->routeIs('dashboard') ? 'bg-gray-700 text-white font-bold' : 'text-gray-400 hover:bg-gray-700' }}">
            <i data-lucide="layout-dashboard"
                class="w-5 h-5 mr-3 {{ request()->routeIs('dashboard') ? 'text-indigo-400' : '' }}"></i>
            <span>Dashboard</span>
        </a>

        <a href="{{ route('wifi') }}" wire:navigate
            class="flex items-center p-3 rounded-lg transition group {{ request()->is('wifi*') ? 'bg-gray-700 text-white font-bold' : 'text-gray-400 hover:bg-gray-700' }}">
            <i data-lucide="wifi" class="w-5 h-5 mr-3 {{ request()->is('wifi*') ? 'text-indigo-400' : '' }}"></i>
            <span>Wifi</span>
        </a>

        <a href="{{ route('library') }}" wire:navigate
            class="flex items-center p-3 rounded-lg transition group {{ request()->is('library*') ? 'bg-gray-700 text-white font-bold' : 'text-gray-400 hover:bg-gray-700' }}">
            <i data-lucide="clapperboard"
                class="w-5 h-5 mr-3 {{ request()->is('library*') ? 'text-indigo-400' : '' }}"></i>
            <span>Library</span>
        </a>

        <a href="{{ route('accessories') }}" wire:navigate
            class="flex items-center p-3 rounded-lg transition group {{ request()->is('accessories*') ? 'bg-gray-700 text-white font-bold' : 'text-gray-400 hover:bg-gray-700' }}">
            <i data-lucide="package-plus"
                class="w-5 h-5 mr-3 {{ request()->is('accessories*') ? 'text-indigo-400' : '' }}"></i>
            <span>Accessories</span>
        </a>

        <a href="{{ route('lendings') }}" wire:navigate
            class="flex items-center p-3 rounded-lg transition group {{ request()->is('lendings*') ? 'bg-gray-700 text-white font-bold' : 'text-gray-400 hover:bg-gray-700' }}">
            <i data-lucide="hand-coins"
                class="w-5 h-5 mr-3 {{ request()->is('lendings*') ? 'text-indigo-400' : '' }}"></i>
            <span>Lending</span>
        </a>

        <a href="{{ route('out-incomes') }}" wire:navigate
            class="flex items-center p-3 rounded-lg transition group {{ request()->is('out-incomes*') ? 'bg-gray-700 text-white font-bold' : 'text-gray-400 hover:bg-gray-700' }}">
            <i data-lucide="arrow-up-right"
                class="w-5 h-5 mr-3 {{ request()->is('out-incomes*') ? 'text-indigo-400' : '' }}"></i>
            <span>Out Income</span>
        </a>

        <a href="{{ route('expenses') }}" wire:navigate
            class="flex items-center p-3 rounded-lg transition group {{ request()->is('expenses*') ? 'bg-gray-700 text-white font-bold' : 'text-gray-400 hover:bg-gray-700' }}">
            <i data-lucide="receipt-text"
                class="w-5 h-5 mr-3 {{ request()->is('expenses*') ? 'text-indigo-400' : '' }}"></i>
            <span>Expenses</span>
        </a>

        {{-- <div class="pt-4 pb-2 px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Segments</div> --}}
    </nav>

    <div class="p-4 border-t border-gray-700">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="flex items-center w-full p-3 text-red-400 hover:bg-red-400/10 rounded-lg transition">
                <i data-lucide="log-out" class="w-5 h-5 mr-3"></i>
                <span class="font-medium">Sign Out</span>
            </button>
        </form>
    </div>
</aside>