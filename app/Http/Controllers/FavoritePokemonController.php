<?php

namespace App\Http\Controllers;

use App\Models\FavoritePokemon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoritePokemonController extends Controller
{
    /**
     * Display a listing of the resource (for the web page).
     * This method will return the Blade view.
     */
    public function index()
    {
        // Check if user is authenticated (middleware 'auth' handles most of this)
        if (!Auth::check()) {
            return redirect()->route('login'); // 未認証ならログインページへリダイレクト
        }

        // Bladeビューを返す。ここでは、ビューに直接データを渡す必要はない。
        // データはJavaScriptがAPIから取得する。
        return view('favorites.index');
    }

    /**
     * Get the authenticated user's favorite pokemon IDs as JSON (for API calls).
     * This is the endpoint JavaScript will call.
     */
    public function getFavoritePokemonIds()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Get only the pokemon_pokeapi_id and order them
        $favoritePokemonIds = $user->favoritePokemons()
                                 ->orderBy('pokemon_pokeapi_id')
                                 ->pluck('pokemon_pokeapi_id'); // IDだけを取得

        // IDのコレクションをJSONで返す
        return response()->json($favoritePokemonIds);
    }


    /**
     * Store a newly created favorite pokemon in storage.
     */
    public function store(Request $request)
    {
        // ... (このメソッドは以前のままでOK) ...
        $request->validate([
            'pokemon_pokeapi_id' => 'required|integer|unique:favorite_pokemons,pokemon_pokeapi_id,NULL,id,user_id,'.Auth::id(),
            'pokemon_name' => 'required|string|max:255',
            'pokemon_image_url' => 'required|url|max:255',
        ], [
            'pokemon_pokeapi_id.unique' => 'このポケモンは既にお気に入りに追加されています。',
        ]);

        $favoritePokemon = Auth::user()->favoritePokemons()->create([
            'pokemon_pokeapi_id' => $request->pokemon_pokeapi_id,
            'pokemon_name' => $request->pokemon_name,
            'pokemon_image_url' => $request->pokemon_image_url,
        ]);

        return response()->json([
            'message' => 'ポケモンがお気に入りに追加されました！',
            'favorite' => $favoritePokemon
        ], 201);
    }

    /**
     * Remove the specified favorite pokemon from storage.
     */
    public function destroy($pokemon_pokeapi_id)
    {
        // ... (このメソッドも以前のままでOK) ...
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