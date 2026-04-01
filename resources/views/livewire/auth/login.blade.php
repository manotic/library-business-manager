<div class="fixed inset-0 flex flex-col md:flex-row bg-slate-900 font-sans antialiased overflow-hidden">
    
    <div class="hidden md:flex md:w-1/2 lg:w-3/5 bg-indigo-600 relative items-center justify-center p-12 overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <svg class="h-full w-full" fill="currentColor" viewBox="0 0 100 100" preserveAspectRatio="none">
                <defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="white" stroke-width="0.5"/></pattern></defs>
                <rect width="100" height="100" fill="url(#grid)" />
            </svg>
        </div>
        
        <div class="relative z-10 max-w-lg text-center lg:text-left">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-white/10 backdrop-blur-lg rounded-3xl mb-8 border border-white/20 shadow-2xl">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            <h1 class="text-4xl lg:text-6xl font-black text-white leading-tight tracking-tighter">
                Master your <br><span class="text-indigo-200">Financial Data.</span>
            </h1>
            <p class="mt-6 text-xl text-indigo-100 font-medium">
                Real-time tracking for Wifi, Inventory, Lending, and Expenses in one secure dashboard.
            </p>
            
            <div class="mt-12 grid grid-cols-2 gap-6 text-left">
                <div class="bg-white/5 backdrop-blur-md p-4 rounded-2xl border border-white/10">
                    <p class="text-indigo-200 text-xs font-bold uppercase tracking-widest">Revenue</p>
                    <p class="text-white text-lg font-bold">End-to-end tracking</p>
                </div>
                <div class="bg-white/5 backdrop-blur-md p-4 rounded-2xl border border-white/10">
                    <p class="text-indigo-200 text-xs font-bold uppercase tracking-widest">Security</p>
                    <p class="text-white text-lg font-bold">Encrypted Storage</p>
                </div>
            </div>
        </div>
    </div>

    <div class="w-full md:w-1/2 lg:w-2/5 flex flex-col items-center justify-center p-8 sm:p-12 lg:p-16 bg-slate-900 overflow-y-auto">
        <div class="w-full max-w-md">
            <div class="md:hidden flex justify-center mb-8">
                <div class="w-12 h-12 bg-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-500/20">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
            </div>

            <div class="text-center md:text-left mb-10">
                <h2 class="text-3xl font-black text-white tracking-tight italic">WELCOME BACK</h2>
                <p class="mt-2 text-slate-400 font-medium">Please enter your credentials to access your account.</p>
            </div>

            <form wire:submit.prevent="login" class="space-y-5">
                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2 ml-1">Email Address</label>
                    <input wire:model.blur="email" id="email" type="email" required 
                        class="block w-full px-4 py-4 border border-slate-700 bg-slate-800 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-200"
                        placeholder="name@company.com">
                    @error('email') <span class="text-rose-400 text-xs mt-1 ml-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <div class="flex justify-between items-center mb-2 ml-1">
                        <label for="password" class="block text-xs font-bold uppercase tracking-widest text-slate-500">Password</label>
                        <a href="#" class="text-xs font-bold text-indigo-400 hover:text-indigo-300">Forgot?</a>
                    </div>
                    <input wire:model.blur="password" id="password" type="password" required 
                        class="block w-full px-4 py-4 border border-slate-700 bg-slate-800 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-200"
                        placeholder="••••••••">
                    @error('password') <span class="text-rose-400 text-xs mt-1 ml-1">{{ $message }}</span> @enderror
                </div>

                <div class="flex items-center">
                    <input id="remember" wire:model.blur="remember" type="checkbox" class="h-4 w-4 text-indigo-500 focus:ring-indigo-500 border-slate-700 rounded bg-slate-800 cursor-pointer">
                    <label for="remember" class="ml-2 block text-sm text-slate-400 cursor-pointer font-medium">Keep me signed in</label>
                </div>

                <button type="submit" 
                    class="group relative w-full flex justify-center py-4 px-4 rounded-xl text-sm font-black text-white bg-indigo-600 hover:bg-indigo-500 transition-all shadow-xl shadow-indigo-500/20 active:scale-[0.98]">
                    <span wire:loading.remove class="flex items-center uppercase tracking-widest">
                        Verify Identity
                        <svg class="ml-2 w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </span>
                    <span wire:loading class="flex items-center uppercase tracking-widest">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Authorizing...
                    </span>
                </button>
            </form>

            <div class="mt-10 pt-8 border-t border-slate-800">
                <p class="text-center text-sm text-slate-500 font-medium">
                    New to the platform? 
                    <a href="/register" class="font-bold text-emerald-400 hover:text-emerald-300">Create an account</a>
                </p>
            </div>
            
            <div class="mt-8 flex items-center justify-center space-x-2 text-slate-600">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" /></svg>
                <span class="text-[10px] uppercase tracking-[0.2em] font-black">SECURE BANK-GRADE ENCRYPTION</span>
            </div>
        </div>
    </div>
</div>