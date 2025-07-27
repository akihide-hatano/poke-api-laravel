<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FavoritePokemonController; // ★この行を追加してください★

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ★ JavaScriptが呼び出す新しいAPIルートを追加 ★
// このルートは、お気に入りポケモンのIDリストをJSONで返します。
Route::middleware('auth:sanctum')->get('/favorite-pokemon-ids', [FavoritePokemonController::class, 'getFavoritePokemonIds']);