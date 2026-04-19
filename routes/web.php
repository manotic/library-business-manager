<?php

use App\Livewire\AccessoryManager;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Dashboard;
use App\Livewire\ExpenseManager;
use App\Livewire\LendingManager;
use App\Livewire\LibraryManager;
use App\Livewire\OutIncomeManager;
use App\Livewire\WifiManager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/', function () {
    return redirect()->route('dashboard');
});
Route::middleware(['auth'])->group(function () {
    Route::get('/wifi', WifiManager::class)->name('wifi');
    Route::get('/library', LibraryManager::class)->name('library');
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/accessories', AccessoryManager::class)->name('accessories');
    Route::get('/lendings', LendingManager::class)->name('lendings');
    Route::get('/out-incomes', OutIncomeManager::class)->name('out-incomes');
    Route::get('/expenses', ExpenseManager::class)->name('expenses');

    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/');
    })->name('logout');

});

Route::middleware(['guest'])->group(function () {
    Route::get('/register', Register::class)->name('register');
    Route::get('/login', Login::class)->name('login');
});
