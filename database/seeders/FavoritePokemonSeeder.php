<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User; // Userモデルを使うために追加
use App\Models\FavoritePokemon; // FavoritePokemonモデルを使うために追加
use Illuminate\Support\Facades\DB; // DBファサードを使うために追加

class FavoritePokemonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 既存のFavoritePokemonデータをクリア (テスト実行ごとにリセットするため)
        DB::table('favorite_pokemons')->truncate();

        // データベースに存在する全てのユーザーを取得
        // UserSeederで作成されたユーザーがここで取得されます
        $users = User::all();

        // 各ユーザーに対してお気に入りポケモンを登録
        $users->each(function ($user) {
            // 各ユーザーがランダムな数のポケモンをお気に入り登録する
            $numberOfFavorites = rand(3, 7); // 各ユーザーが3〜7体お気に入り登録する例

            // お気に入りポケモンの候補リスト (PokeAPIのIDのみ)
            // ここに、お気に入りとして登録したいポケモンのPokeAPI IDを列挙します。
            $pokemonCandidates = [
                1,   // フシギダネ
                4,   // ヒトカゲ
                7,   // ゼニガメ
                25,  // ピカチュウ
                6,   // リザードン
                143, // カビゴン
                133, // イーブイ
                150, // ミュウツー
                151, // ミュウ
                387, // ナエトル
                390, // ヒコザル
                393, // ポッチャマ
                // 必要に応じてさらにポケモンIDを追加してください
            ];

            // 候補リストからランダムに指定数のお気に入りポケモンIDを選び、重複を避ける
            $selectedPokemonIds = collect($pokemonCandidates)->shuffle()->take($numberOfFavorites);

            foreach ($selectedPokemonIds as $pokeapiId) {
                // FavoritePokemon モデルを使ってレコードを作成
                FavoritePokemon::create([
                    'user_id' => $user->id,
                    'pokemon_pokeapi_id' => $pokeapiId,
                    // pokemon_name と pokemon_image_url はマイグレーションから削除されたため、
                    // ここでは含めません。これらの情報はフロントエンドでPokeAPIから取得します。
                ]);
            }
        });
    }
}