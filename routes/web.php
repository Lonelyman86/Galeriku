<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FotoController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\AlbumController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\KomentarController;
use App\Http\Controllers\SigninController;
use App\Http\Controllers\SignupController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;

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


Route::get('/', [HomeController::class, 'index']);

//Cari
Route::get('/search', [SearchController::class, 'search'])->name('search');

// Guest-only routes
    Route::middleware('guest')->group(function () {
    Route::get('/sign-in', [SigninController::class, 'index'])->name('login');
    Route::post('/sign-in', [SigninController::class, 'authenticate']);
    Route::get('/sign-up', [SignupController::class, 'index']);
    Route::post('/sign-up', [SignupController::class, 'store']);
});

// Authenticated-only routes
    Route::middleware('auth')->group(function () {
    Route::post('/logout', [SigninController::class, 'logout'])->name('logout');

    Route::get('/studio', [HomeController::class, 'StudioIndex']);

    // Foto routes
    Route::get('/foto', [FotoController::class, 'index'])->name('foto');
    Route::get('/createfoto', [FotoController::class, 'create']);
    Route::post('/upload/photo', [FotoController::class, 'upload'])->name('upload.photo');
    Route::post('foto/{photo}/update-album', [FotoController::class, 'updateAlbum'])->name('foto.update.album');
    Route::delete('/photos/{photo}', [FotoController::class, 'destroy'])->name('photos.destroy');

    // Album routes
    Route::get('/createalbum', [AlbumController::class, 'index']);
    Route::post('/album/new', [AlbumController::class, 'store'])->name('album.new');
    

    // Komentar dan like
    Route::post('/albums/{photo}/toggle-like', [LikeController::class, 'toggle'])->name('likes.toggle');
    Route::get('/albums/{photo}/check-like', [LikeController::class, 'checkLike'])->name('likes.check');
    Route::post('/photos/{photo}/komentar', [KomentarController::class, 'store'])->name('komentar.store');
    Route::get('/liked', [LikeController::class, 'likedPhotos'])->name('photo.liked');
    Route::post('/photos/{id}/like', [LikeController::class, 'toggle'])->name('photo.like');

});
    // Profil
    Route::middleware('auth')->group(function () {
        Route::get('/profile',  [ProfileController::class, 'edit'])->name('profile');   // tampil + form
        Route::patch('/profile',[ProfileController::class, 'update'])->name('profile.update'); // update data + avatar + password opsional
    });
    Route::get('/albums/{album}', [AlbumController::class, 'show'])->name('album.show');

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/foto', [App\Http\Controllers\Admin\FotoAdminController::class, 'index'])->name('admin.foto.index');
    Route::patch('/foto/{id}/approve', [App\Http\Controllers\Admin\FotoAdminController::class, 'approve'])->name('admin.foto.approve');
    Route::patch('/foto/{id}/reject', [App\Http\Controllers\Admin\FotoAdminController::class, 'reject'])->name('admin.foto.reject');
    Route::delete('/foto/{id}', [App\Http\Controllers\Admin\FotoAdminController::class, 'destroy'])->name('admin.foto.destroy');
});

