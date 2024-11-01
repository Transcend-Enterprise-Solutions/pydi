<?php

use App\Livewire\Admin\Association;
use App\Livewire\Home;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Livewire\Admin\AssociationDues;
use App\Livewire\Admin\ElectricBills;
use App\Livewire\Admin\WaterBills;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;


Route::redirect('/', '/login');
Route::get('/register', function () {
    return view('registeraccount'); })->name('register');




/* Admin account role ------------------------------------------------------------------------------*/
Route::middleware(['auth', 'checkrole:sa,admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/monthly-payments/association-dues', AssociationDues::class)->name('association-dues');
    Route::get('/monthly-payments/water-bills', WaterBills::class)->name('water-bills');
    Route::get('/monthly-payments/electric-bills', ElectricBills::class)->name('electric-bills');
});




/* Homeowner account role --------------------------------------------------------------------------*/
Route::middleware(['auth', 'checkrole:homeowner'])->group(function () {
    Route::get('/home', Home::class)->name('home');
});




/* Homeowner and Admin account role --------------------------------------------------------------------------*/
Route::middleware(['auth', 'checkrole:sa,admin,homeowner'])->group(function () {
    Route::get('/association', Association::class)->name('association');
});




/* Profile Photo -----------------------------------------------------------------------------------*/
Route::get('/profile-photo/{filename}', function ($filename) {
    $path = 'profile-photos/' . $filename;

    if (!Storage::disk('public')->exists($path)) {
        abort(404);
    }

    $file = Storage::disk('public')->get($path);
    $type = File::mimeType(storage_path('app/public/' . $path));

    return response($file, 200)->header('Content-Type', $type);
})->name('profile-photo.file');
