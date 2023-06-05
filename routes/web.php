<?php

use App\Http\Controllers\EpisodesController;
use App\Http\Controllers\PodcastsController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/dashboard/podcasts', [PodcastsController::class, 'index'])->name('podcast.index');
    Route::get('/dashboard/podcasts/create', [PodcastsController::class, 'create'])->name('podcast.create');
    Route::post('/podcasts', [PodcastsController::class, 'store'])->middleware(['XssSanitizer'])->name('podcast.store');
    Route::patch('/podcasts/{podcast}', [PodcastsController::class, 'update'])->middleware(['XssSanitizer']);
    Route::delete('/podcasts/{podcast}', [PodcastsController::class, 'delete']);

    Route::post('/podcasts/{podcast}/episodes', [EpisodesController::class, 'store'])->middleware(['XssSanitizer']);
    Route::patch('/podcasts/{podcast}/episodes/{episode}', [EpisodesController::class, 'update'])->middleware(['XssSanitizer']);
    Route::delete('/podcasts/{podcast}/episodes/{episode}', [EpisodesController::class, 'delete']);
});

require __DIR__.'/auth.php';
