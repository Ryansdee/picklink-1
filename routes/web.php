<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Response;
use App\Http\Controllers\PostController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\ProfileController;


Route::get('/', function () {
    return view('welcome');
});



Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/posts/create', [PostController::class, 'create'])->name('post.create');
    Route::post('/post', [PostController::class, 'store'])->name('post.store');
    Route::get('/post/{id}/image', [PostController::class, 'getImage'])->name('post.image');
    Route::get('/search', [ProfileController::class, 'search'])->name('search');
    Route::get('/profile', [ProfilController::class, 'showProfile'])->name('profile');
    Route::post('/profile/update', [ProfilController::class, 'update'])->name('profile.update');
    Route::post('/update-profile', [ProfilController::class, 'update'])->name('update_profile');

    // Route pour afficher le profil d'un utilisateur par son nom d'utilisateur
    Route::get('/profile/{name}', [ProfilController::class, 'showByName'])->name('profile.show');



    Route::get('/profile/image/{username}', [ProfilController::class, 'getProfileImage'])->name('profile.image');
});

Route::post('/posts/{post}/like', [PostController::class, 'like'])->name('posts.like');
Route::post('/posts/{post}/comment', [PostController::class, 'comment'])->name('posts.comment');
