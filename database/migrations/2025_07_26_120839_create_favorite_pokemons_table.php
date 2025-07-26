// database/migrations/YYYY_MM_DD_HHMMSS_create_favorite_pokemons_table.php

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('favorite_pokemons', function (Blueprint $table) {
            // usersテーブルへの外部キー
            // user_id は users テーブルの id カラムを参照し、ユーザー削除時にお気に入りも削除される
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // PokeAPIのポケモンIDを保存
            $table->unsignedBigInteger('pokemon_pokeapi_id');
            // お気に入り登録日時と更新日時を自動で記録
            $table->timestamps();
            // user_id と pokemon_pokeapi_id の組み合わせがユニークであることを保証し、複合主キーとする
            // これにより、同じユーザーが同じポケモンを複数回お気に入り登録することを防ぐ
            $table->primary(['user_id', 'pokemon_pokeapi_id']); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favorite_pokemons');
    }
};