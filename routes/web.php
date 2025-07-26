<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PokemonController; // ★ この行を追加
use App\Http\Controllers\FavoritePokemonController; // ★ APIコントローラーも必要なのでそのまま残すか追加
use App\Models\FavoritePokemon;

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

// ★ ポケモン関連のWebルーティングを追加 (認証済みユーザーのみアクセス可能)
    Route::get('/pokemons', [PokemonController::class, 'index'])->name('pokemons.index');
    Route::get('/pokemons/{id}', [PokemonController::class, 'show'])->name('pokemons.show');

    // ★ ポケモンのお気に入りのルーティング
    Route::resource('/favorites',FavoritePokemonController::class);
});

require __DIR__.'/auth.php';