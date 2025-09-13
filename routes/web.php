<?php

use App\Http\Controllers\ProfileController;
use App\Livewire\Posts\CreatePost;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Livewire
Route::get('/create-post', CreatePost::class)->name('create-post');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
