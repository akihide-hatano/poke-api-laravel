<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\FavoritePokemon;

class PokemonController extends Controller
{
    public function index(Request $request)
    {
        $userFavoritePokemonIds = collect();
        if (Auth::check() && Auth::user()) {
            $userFavoritePokemonIds = Auth::user()->favoritePokemons
                                        ->pluck('pokemon_pokeapi_id')
                                        ->toArray();
        }
        return view('pokemons.index', compact('userFavoritePokemonIds'));
    }

    public function show(string $id)
    {
        return view('pokemons.show', compact('id'));
    }
}