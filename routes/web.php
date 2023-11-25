<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Models\Post;
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

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/', [PostController::class, 'index']);
    Route::get('/posts/{id}', [PostController::class, 'show'])->name('post.show');
    Route::get('/edit-post/{id}', [PostController::class, 'edit'])->name('post.edit');
    Route::post('/update-post/{id}', [PostController::class, 'update'])->name('post.update');
    Route::post('/destroy-post/{id}', [PostController::class, 'destroy'])->name('post.destroy');


    Route::get('/profile/{id}', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/profile/', [PostController::class, 'store'])->name('post.store');
});

require __DIR__ . '/auth.php';
