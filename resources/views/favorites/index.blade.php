<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ Auth::user()->name }}さんのお気に入りのポケモン
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{-- ここから既存のお気に入りポケモン表示部分 --}}
                    <div class="pokemon-grid grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        @forelse ($favoritePokemons as $pokemon)
                            <div class="pokemon-card bg-white rounded-lg shadow-md overflow-hidden p-4 flex flex-col items-center">
                                <h2 class="text-2xl font-bold mb-2">{{ $pokemon->pokemon_name }}</h2>
                                <img src="{{ $pokemon->pokemon_image_url }}" alt="{{ $pokemon->pokemon_name }}" class="w-24 h-24 object-contain mb-2">
                                <p class="text-lg">ID: #{{ str_pad($pokemon->pokemon_pokeapi_id, 3, '0', STR_PAD_LEFT) }}</p>
                                {{-- タイプ表示など、他の情報も必要に応じて追加 --}}
                                {{-- ここに「お気に入り解除」ボタンなどを追加することもできます --}}
                            </div>
                        @empty
                            <p>お気に入りのポケモンはまだいません。</p>
                        @endforelse
                    </div>
                    {{-- ここまで既存のお気に入りポケモン表示部分 --}}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
