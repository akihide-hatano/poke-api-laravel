<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // ★ BelongsTo を使うために必要

class FavoritePokemon extends Model
{
    use HasFactory;

    protected $table = 'favorite_pokemons';

    protected $primaryKey = null; // 主キーがないことを示す
    public $incrementing = false; // 自動増分IDではないことを示す


    protected $fillable = [
        'user_id',
        'pokemon_pokeapi_id',
    ];

    /**
     * このお気に入りポケモン記録が属するUserを取得するリレーションシップを定義します。
     * FavoritePokemon (個々の記録) は、一人のUserに属します。
     */
    public function user(): BelongsTo
    {

        return $this->belongsTo(User::class);
    }
}