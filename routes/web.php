<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Admin\DimensionIndicatorManager;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

use App\Livewire\{DashboardIndex, UserLogs};
use App\Livewire\User\{PydiDatasetDetailIndex, PydiDatasetIndex, PydpDatasetIndex, PydpDatasetDetailIndex, PydpLevelController};
use App\Livewire\Admin\{UserList, ManagePydiIndex, ManagePydiDetailIndex, CoverYearIndex, ManagePydpIndex, ManagePydpDetailIndex};
use App\Livewire\Landing\{HomeIndex, AdvocacyIndex};

Route::redirect('/', '/landing');
Route::get('/register', function () {
    return view('registeraccount');
})->name('register');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', DashboardIndex::class)->name('dashboard');
    Route::get('/logs', UserLogs::class)->name('logs');
});

/* Admin account role ------------------------------------------------------------------------------*/
Route::middleware(['auth', 'checkrole:sa,admin'])->group(function () {
    // Settings
    Route::get('/cover-year', CoverYearIndex::class)->name('cover-year');
    Route::get('/representatives', UserList::class)->name('representatives');
    Route::get('/dimension-indicator', DimensionIndicatorManager::class)->name('dimension-indicator');

    // Manage PYDP Datasets
    Route::get('/manage-pydp-datasets', ManagePydpIndex::class)->name('manage-pydp-datasets');
    Route::get('/manage-pydp-datasets/{id}', ManagePydpDetailIndex::class)->name('manage-pydp-dataset-details');

    // Manage PYDI Datasets
    Route::get('/manage-pydi-datasets', ManagePydiIndex::class)->name('manage-pydi-datasets');
    Route::get('/manage-pydi-datasets/{id}', ManagePydiDetailIndex::class)->name('manage-pydi-dataset-details');
});

/* Homeowner account role --------------------------------------------------------------------------*/
Route::middleware(['auth', 'checkrole:user'])->group(function () {
    Route::get('/pydp-levels', PydpLevelController::class)->name('pydp-levels');

    // PYDP Datasets
    Route::get('/pydp-datasets', PydpDatasetIndex::class)->name('pydp-datasets');
    Route::get('/pydp-dataset-details/{id}', PydpDatasetDetailIndex::class)->name('pydp-dataset-details');

    // PYDI Datasets
    Route::get('/pydi-datasets', PydiDatasetIndex::class)->name('pydi-datasets');
    Route::get('/pydi-datasets/{id}', PydiDatasetDetailIndex::class)->name('pydi-dataset-details');
});


Route::get('/landing', HomeIndex::class)->name('landing');
Route::get('/advocacy/{id}', AdvocacyIndex::class)->name('advocacy');







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
