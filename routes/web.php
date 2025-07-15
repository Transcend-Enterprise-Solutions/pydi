<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Livewire\Admin\DimensionIndicatorManager;
use App\Livewire\Admin\ViewDatasets;
use App\Livewire\Admin\UserList;
use App\Livewire\User\PydiDataEntry;
use App\Livewire\User\InputDatasets;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;



Route::redirect('/', '/login');
Route::get('/register', function () {
    return view('registeraccount'); })->name('register');




/* Admin account role ------------------------------------------------------------------------------*/
Route::middleware(['auth', 'checkrole:sa,admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/representatives', UserList::class)->name('representatives');
    Route::get('/dimension-indicator', DimensionIndicatorManager::class)->name('dimension-indicator');
    Route::get('/view-datasets', ViewDatasets::class)->name('view-datasets');
});




/* Homeowner account role --------------------------------------------------------------------------*/
Route::middleware(['auth', 'checkrole:user'])->group(function () {
    Route::get('/data-entry', PydiDataEntry::class)->name('data-entry');
    Route::get('/input-datasets', InputDatasets::class)->name('input-datasets');
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
