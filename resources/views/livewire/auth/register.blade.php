<div class="fixed inset-0 flex flex-col md:flex-row bg-slate-900 font-sans antialiased overflow-hidden">
    
    <div class="hidden md:flex md:w-1/2 lg:w-3/5 bg-emerald-600 relative items-center justify-center p-12 overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <svg class="h-full w-full" fill="currentColor" viewBox="0 0 100 100" preserveAspectRatio="none">
                <defs>
                    <pattern id="dots" width="20" height="20" patternUnits="userSpaceOnUse">
                        <circle cx="2" cy="2" r="1" fill="white" />
                    </pattern>
                </defs>
                <rect width="100" height="100" fill="url(#dots)" />
            </svg>
        </div>
        
        <div class="relative z-10 max-w-lg text-center lg:text-left">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-white/10 backdrop-blur-lg rounded-3xl mb-8 border border-white/20 shadow-2xl">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h1 class="text-4xl lg:text-6xl font-black text-white leading-tight tracking-tighter">
                Start your <br><span class="text-emerald-200">Growth Journey.</span>
            </h1>
            <p class="mt-6 text-xl text-emerald-100 font-medium">
                Join hundreds of business owners managing their revenue and risk with precision.
            </p>
            
            <div class="mt-12 space-y-4">
                <div class="flex items-center gap-4 text-white font-bold">
                    <div class="bg-white/20 p-2 rounded-full">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                    </div>
                    <span>No credit card required to start</span>
                </div>
                <div class="flex items-center gap-4 text-white font-bold">
                    <div class="bg-white/20 p-2 rounded-full">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                    </div>
                    <span>Instant access to all 7 business modules</span>
                </div>
            </div>
        </div>
    </div>

    <div class="w-full md:w-1/2 lg:w-2/5 flex flex-col items-center justify-center p-8 sm:p-12 lg:p-16 bg-slate-900 overflow-y-auto">
        <div class="w-full max-w-md">
            
            <div class="md:hidden flex justify-center mb-8">
                <div class="w-12 h-12 bg-emerald-500 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/20">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>

            <div class="text-center md:text-left mb-10">
                <h2 class="text-3xl font-black text-white tracking-tight italic uppercase">Create your vault</h2>
                <p class="mt-2 text-slate-400 font-medium">Set up your profile to start tracking your finances.</p>
            </div>

            <form wire:submit.prevent="register" class="space-y-5">
                <div>
                    <label for="name" class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2 ml-1">Full Name</label>
                    <input wire:model="name" id="name" type="text" required 
                        class="block w-full px-4 py-4 border border-slate-700 bg-slate-800 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 transition duration-200"
                        placeholder="John Doe">
                    @error('name') <span class="text-rose-400 text-xs mt-1 ml-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2 ml-1">Email Address</label>
                    <input wire:model="email" id="email" type="email" required 
                        class="block w-full px-4 py-4 border border-slate-700 bg-slate-800 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 transition duration-200"
                        placeholder="you@finance.com">
                    @error('email') <span class="text-rose-400 text-xs mt-1 ml-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2 ml-1">Password</label>
                    <input wire:model="password" id="password" type="password" required 
                        class="block w-full px-4 py-4 border border-slate-700 bg-slate-800 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 transition duration-200"
                        placeholder="••••••••">
                    <p class="mt-2 text-[10px] text-slate-500 italic ml-1 font-bold uppercase tracking-tighter">Minimum 8 characters with mixed strength</p>
                </div>

                <button type="submit" 
                    class="group relative w-full flex justify-center py-4 px-4 rounded-xl text-sm font-black text-white bg-emerald-600 hover:bg-emerald-500 transition-all shadow-xl shadow-emerald-500/20 active:scale-[0.98]">
                    <span wire:loading.remove class="flex items-center uppercase tracking-widest">
                        Initialize Account
                        <svg class="ml-2 w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </span>
                    <span wire:loading class="flex items-center uppercase tracking-widest">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Building Vault...
                    </span>
                </button>
            </form>

            <div class="mt-10 pt-8 border-t border-slate-800">
                <p class="text-center text-sm text-slate-500 font-medium">
                    Already part of the network? 
                    <a href="{{ route('login') }}" class="font-bold text-indigo-400 hover:text-indigo-300">Sign in to Dashboard</a>
                </p>
            </div>
        </div>
    </div>
</div>