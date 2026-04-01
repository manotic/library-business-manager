<header class="h-16 bg-gray-800 border-b border-gray-700 flex items-center justify-between px-4 sm:px-8 flex-shrink-0">
    <div class="flex items-center">
        <button @click="sidebarOpen = true" class="lg:hidden mr-4 text-gray-400 hover:text-white">
            <i data-lucide="menu" class="w-6 h-6"></i>
        </button>
        <h1 class="text-lg font-semibold truncate hidden sm:block uppercase tracking-wider text-gray-200">
            @if(request()->routeIs('dashboard')) Overview
            @elseif(request()->is('wifi*')) Wifi Manager
            @elseif(request()->is('library*')) Library Manager
            @elseif(request()->is('accessories*')) Accessories
            @elseif(request()->is('lending*')) Lendings
            @elseif(request()->is('out-incomes*')) Outside Incomes
            @elseif(request()->is('expenses*')) Expenses
            @else Business Admin
            @endif
        </h1>
    </div>
    
    <div class="flex items-center space-x-3 sm:space-x-6">
        <div class="text-right">
            <p class="text-sm font-medium leading-none">{{ auth()->user()?->name }}</p>
        </div>
    </div>
</header>