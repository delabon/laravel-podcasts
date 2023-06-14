<?php

use App\Http\Controllers\DashboardEpisodesController;
use App\Http\Controllers\DashboardPodcastsController;
use App\Http\Controllers\EpisodesController;
use App\Http\Controllers\HomeController;
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

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Manage profiles
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Dashboard - podcasts
    Route::get('/dashboard/podcasts', [DashboardPodcastsController::class, 'index'])->name('dashboard.podcast.index');
    Route::get('/dashboard/podcasts/{podcast}', [DashboardPodcastsController::class, 'show'])->name('dashboard.podcast.show');
    Route::get('/dashboard/podcasts/create', [DashboardPodcastsController::class, 'create'])->name('dashboard.podcast.create');

    // Dashboard - episodes
    Route::get('/dashboard/podcasts/{podcast}/episodes/create', [DashboardEpisodesController::class, 'create'])->name('dashboard.episode.create');
    Route::get('/dashboard/podcasts/{podcast}/episodes/{episode}/edit', [DashboardEpisodesController::class, 'edit'])->name('dashboard.episode.edit');

    // Manage podcasts
    Route::post('/podcasts', [PodcastsController::class, 'store'])->middleware(['XssSanitizer'])->name('podcast.store');
    Route::patch('/podcasts/{podcast}', [PodcastsController::class, 'update'])->middleware(['XssSanitizer']);
    Route::delete('/podcasts/{podcast}', [PodcastsController::class, 'delete']);

    // Manage episodes
    Route::post('/podcasts/{podcast}/episodes', [EpisodesController::class, 'store'])->middleware(['XssSanitizer'])->name('episode.store');
    Route::patch('/podcasts/{podcast}/episodes/{episode}', [EpisodesController::class, 'update'])->middleware(['XssSanitizer'])->name('episode.update');
    Route::delete('/podcasts/{podcast}/episodes/{episode}', [EpisodesController::class, 'delete'])->name('episode.delete');
});

require __DIR__.'/auth.php';
