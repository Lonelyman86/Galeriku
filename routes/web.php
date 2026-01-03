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
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FotoAdminController;
use App\Http\Controllers\Admin\ReportAdminController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\ReportController;

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




// Halaman Utama & Search
Route::get('/', [HomeController::class, 'index']);

Route::get('/search', [SearchController::class, 'search'])->name('search');

// Guest-only routes (Belum Login)
Route::middleware('guest')->group(function () {
    Route::get('/sign-in', [SigninController::class, 'index'])->name('login');
    Route::post('/sign-in', [SigninController::class, 'authenticate']);
    Route::get('/sign-up', [SignupController::class, 'index']);
    Route::post('/sign-up', [SignupController::class, 'store']);
});

// Authenticated-only routes (Sudah Login)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [SigninController::class, 'logout'])->name('logout');
    Route::get('/studio', [HomeController::class, 'StudioIndex']);

    // --- Foto Routes ---
    Route::get('/foto', [FotoController::class, 'index'])->name('foto');
    Route::get('/createfoto', [FotoController::class, 'create']);
    Route::post('/upload/photo', [FotoController::class, 'upload'])->name('upload.photo');
    Route::post('foto/{photo}/update-album', [FotoController::class, 'updateAlbum'])->name('foto.update.album');
    Route::get('/photos/{photo}/edit', [FotoController::class, 'edit'])->name('photos.edit');
    Route::patch('/photos/{photo}', [FotoController::class, 'update'])->name('photos.update');
    Route::delete('/photos/{photo}', [FotoController::class, 'destroy'])->name('photos.destroy');

    // --- Album Routes ---
    Route::get('/createalbum', [AlbumController::class, 'index']);
    Route::post('/album/new', [AlbumController::class, 'store'])->name('album.new');
    
    // Route untuk fitur "Ambil dari Galeri" (Mass Add Photos)
    Route::post('/album/{album}/add-existing', [AlbumController::class, 'addExistingPhotos'])->name('album.add_existing');
    Route::patch('/albums/{album}', [AlbumController::class, 'update'])->name('albums.update');
    Route::delete('/albums/{album}', [AlbumController::class, 'destroy'])->name('albums.destroy');

    // --- Komentar & Like ---
    Route::post('/albums/{photo}/toggle-like', [LikeController::class, 'toggle'])->name('likes.toggle');
    Route::get('/albums/{photo}/check-like', [LikeController::class, 'checkLike'])->name('likes.check');
    Route::post('/photos/{photo}/komentar', [KomentarController::class, 'store'])->name('komentar.store');
    Route::get('/liked', [HomeController::class, 'likedPhotos'])->name('photo.liked');


    // --- Profil ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    
    // --- Follow System (BARU) ---
    Route::post('/user/{user}/follow', [FollowController::class, 'toggle'])->name('user.follow');
    Route::get('/following', [HomeController::class, 'followingFeed'])->name('feed.following'); // <--- BARU

    // --- Report System (BARU) ---
    Route::post('/report', [ReportController::class, 'store'])->name('report.store');
    
    // --- Notifikasi ---
    Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read-all');
    Route::get('/notifications/read-all', function() { return redirect('/studio'); });

    // --- Discovery (BARU) ---
    Route::get('/discovery', [\App\Http\Controllers\DiscoveryController::class, 'index'])->name('discovery');
});

// Route ini di luar 'auth' agar album bisa dilihat publik (jika diinginkan)
Route::get('/albums/{album}', [AlbumController::class, 'show'])->name('album.show');
Route::get('/user/{id}', [ProfileController::class, 'showPublicProfile'])->name('profile.public');

// --- Admin Routes ---
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/foto', [FotoAdminController::class, 'index'])->name('admin.foto.index');
    Route::patch('/foto/{id}/approve', [FotoAdminController::class, 'approve'])->name('admin.foto.approve');
    Route::patch('/foto/{id}/reject', [FotoAdminController::class, 'reject'])->name('admin.foto.reject');
    Route::delete('/foto/{id}', [FotoAdminController::class, 'destroy'])->name('admin.foto.destroy');

    // Admin Reports

    Route::patch('/reports/{id}/ban', [ReportAdminController::class, 'ban'])->name('admin.reports.ban');
    Route::patch('/reports/{id}/dismiss', [ReportAdminController::class, 'dismiss'])->name('admin.reports.dismiss');
});