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
        // 認証済みのユーザーのお気に入りポケモンのみを取得
        // user_id順に並べ替え、必要であればページネーションを追加
        $favoritePokemons = Auth::user()->favoritePokemons()
                                 ->orderBy('pokemon_pokeapi_id') // ID順にソート（任意）
                                 ->get(); // または ->paginate(20);

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