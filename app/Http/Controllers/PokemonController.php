<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http; // ★ この行は再び必要になります
use Illuminate\Support\Facades\Auth;
use App\Models\FavoritePokemon;
use Illuminate\Support\Facades\Log;

class PokemonController extends Controller
{
    /**
     * Display a listing of the pokemons.
     */
    public function index(Request $request)
    {
        // PokeAPIからポケモン一覧データを取得（Laravel側で実行）
        // ページネーションのためにlimitとoffsetをクエリパラメータから取得
        $limit = $request->input('limit', 20); // デフォルト20件
        $offset = $request->input('offset', 0); // デフォルト0から開始

        $response = Http::get('https://pokeapi.co/api/v2/pokemon', [
            'limit' => $limit,
            'offset' => $offset,
        ]);

        $pokemons = collect(); // デフォルトは空のコレクション

        // PHPでtry/catchを使う場合は、Http::get() の外側でキャッチする
        try {
            if ($response->successful()) {
                $pokemonData = $response->json();
                
                // resultsから各ポケモンのIDと基本情報を抽出
                $pokemons = collect($pokemonData['results'])->map(function ($pokemon) {
                    $urlParts = explode('/', rtrim($pokemon['url'], '/'));
                    $pokemonId = end($urlParts);
                    
                    return [
                        'id' => (int)$pokemonId, // IDを整数型にキャスト
                        'name' => $pokemon['name'],
                        'image_url' => "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/{$pokemonId}.png",
                    ];
                });

                // 各ポケモンの詳細データ（タイプなど）もLaravel側で取得
                // これはパフォーマンスに影響する可能性があるので注意
                $pokemons = $pokemons->map(function ($pokemon) {
                    $detailResponse = Http::get("https://pokeapi.co/api/v2/pokemon/{$pokemon['id']}");
                    if ($detailResponse->successful()) {
                        $detailData = $detailResponse->json();
                        $pokemon['types'] = collect($detailData['types'])->pluck('type.name')->toArray();
                        // 必要であれば、高さ、重さ、特性などもここに追加
                        $pokemon['height'] = $detailData['height']; // 例
                        $pokemon['weight'] = $detailData['weight']; // 例
                        $pokemon['abilities'] = collect($detailData['abilities'])->pluck('ability.name')->toArray(); // 例
                    }
                    return $pokemon;
                });
                
                // ページネーション情報も渡す
                $totalPokemonCount = $pokemonData['count'];

            } else {
                // HTTPエラーレスポンスの場合の処理
                Log::error('PokeAPI list request failed: ' . $response->status() . ' ' . $response->body());
                session()->flash('error', 'ポケモンリストの取得に失敗しました。');
            }
        } catch (\Exception $e) {
            // ネットワークエラーなど、リクエスト自体が失敗した場合の処理
            Log::error('Caught exception during PokeAPI list request: ' . $e->getMessage());
            session()->flash('error', 'ポケモンリストの取得中に予期せぬエラーが発生しました。');
        }

        // ログインユーザーのお気に入りポケモンIDのリストを取得
        $userFavoritePokemonIds = collect();

                Log::info('Checking Auth::check(): ' . (Auth::check() ? 'true' : 'false')); // ★ デバッグログ
        if (Auth::check()) {
            $user = Auth::user();
            Log::info('Checking Auth::user(): ' . ($user ? 'exists' : 'null')); // ★ デバッグログ
            if ($user) {
                // ここで $user->favoritePokemons が null になるか確認
                $favoritePokemonsRelation = $user->favoritePokemons;
                Log::info('Checking favoritePokemons relation: ' . ($favoritePokemonsRelation ? 'exists' : 'null')); // ★ デバッグログ

                if ($favoritePokemonsRelation) { // リレーションが null でないことを確認
                    $userFavoritePokemonIds = $favoritePokemonsRelation
                                                ->pluck('pokemon_pokeapi_id')
                                                ->toArray();
                } else {
                    Log::warning('User favoritePokemons relation is null or invalid for user ID: ' . $user->id);
                }
            } else {
                Log::warning('Auth::check() is true, but Auth::user() is null. This should not happen under normal circumstances.');
            }
        }

        // ビューに渡すデータを compact() でまとめる
        return view('pokemons.index', compact('pokemons', 'userFavoritePokemonIds', 'totalPokemonCount', 'limit', 'offset'));
    }

    /**
     * Display the specified pokemon. (これはまだ実装しません)
     */
    public function show(string $id)
    {
        return view('pokemons.show');
    }
}