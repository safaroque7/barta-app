<?php

use App\Http\Controllers\CommentController;
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

    // $users = DB::users()->get();

    Route::get('/', [PostController::class, 'index']);
    Route::get('/single-post/{id}', [PostController::class, 'show'])->name('post.show');
    Route::get('/edit-post/{id}', [PostController::class, 'edit'])->name('post.edit');
    Route::post('/update-post/{id}', [PostController::class, 'update'])->name('post.update');
    Route::post('/destroy-post/{id}', [PostController::class, 'destroy'])->name('post.destroy');

    Route::get('/user/profile/{id}', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile/edit', [ProfileController::class, 'update'])->name('profile.update');

    // Route::post('/profile/{id}', [ProfileController::class, 'update'])->name('update');

    Route::delete('/user/destroy-profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/', [PostController::class, 'store'])->name('post.store');

    // for comments
    Route::post('/comments/', [CommentController::class, 'store'])->name('comment.store');

    Route::post('/search/', [PostController::class, 'search'])->name('post.search');

    //Route::get('/profile');
    //Route::get('/profile/edit');
    //Route::post('/profile/edit');

    //Route::match(['POST', 'GET'], profile/edit);

});

require __DIR__ . '/auth.php';
