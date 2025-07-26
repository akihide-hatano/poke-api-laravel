<?php

namespace App\Http\Controllers;

use App\Models\FavoritePokemon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoritePokemonController extends Controller
{
    // ★★★ 新しく追加または編集するメソッド ★★★
    public function index()
    {
        // ログインユーザーが取得できているか確認
        $user = Auth::user();
        if (!$user) {
            // ユーザーがログインしていない場合
            // dd('ユーザーがログインしていません！'); // デバッグ用
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $favoritePokemonsRelation = $user->favoritePokemons();
        // 実際にデータを取得
        $favoritePokemons = $favoritePokemonsRelation
                                 ->orderBy('pokemon_pokeapi_id')
                                 ->get();

        // 取得したお気に入りポケモンのコレクションの内容を確認（デバッグ用）
        // dd($favoritePokemons); // ★ ここで dd() を使って内容を確認！

        // 最終的にはJSONレスポンスを返す
        return response()->json($favoritePokemons);
    }

    // ★★★ 既存のstoreメソッド（お気に入り追加） ★★★
    public function store(Request $request)
    {
        $request->validate([
            'pokemon_pokeapi_id' => 'required|integer|unique:favorite_pokemons,pokemon_pokeapi_id,NULL,id,user_id,'.Auth::id(),
        ], [
            'pokemon_pokeapi_id.unique' => 'このポケモンは既にお気に入りに追加されています。',
        ]);

        $favoritePokemon = Auth::user()->favoritePokemons()->create([
            'pokemon_pokeapi_id' => $request->pokemon_pokeapi_id,
        ]);

        return response()->json([
            'message' => 'ポケモンがお気に入りに追加されました！',
            'favorite' => $favoritePokemon
        ], 201);
    }

    // ★★★ 既存のdestroyメソッド（お気に入り解除） ★★★
    public function destroy($pokemon_pokeapi_id)
    {
        $favoritePokemon = Auth::user()->favoritePokemons()
                            ->where('pokemon_pokeapi_id', $pokemon_pokeapi_id)
                            ->first();

        if (!$favoritePokemon) {
            return response()->json(['message' => 'お気に入りに見つかりませんでした。'], 404);
        }

        $favoritePokemon->delete();

        return response()->json(['message' => 'お気に入りからポケモンを削除しました。'], 200);
    }
}