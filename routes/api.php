// routes/api.php
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FavoritePokemonController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// このルートはデフォルトで残しておいてください
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ★ この行から 'auth:sanctum' ミドルウェアを一時的に削除 ★
Route::get('/favorite-pokemon-ids', [FavoritePokemonController::class, 'getFavoritePokemonIds']);

// もし以前に /favorites のルートがここにあったら、削除またはコメントアウトしてください。
// 例: Route::get('/favorites', [FavoritePokemonController::class, 'index']); // これは削除