<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('ポケモン一覧') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-2xl font-bold text-gray-800 mb-6">すべてのポケモン</h3>
                    {{-- ★★★ ここに追加: ユーザーのお気に入りIDをJSに渡すためのhidden input ★★★ --}}
                    <input type="hidden" id="userFavoritePokemonIds" value='@json($userFavoritePokemonIds)'>
                    <input type="hidden" id="totalPokemonCount" value="{{ $totalPokemonCount }}">
                    <input type="hidden" id="currentLimit" value="{{ $limit }}">
                    <input type="hidden" id="currentOffset" value="{{ $offset }}">

                    {{-- ポケモンカードのグリッドをPHPで直接ループして表示 --}}
                    <div id="pokemonList" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        @forelse ($pokemons as $pokemon)
                            @php
                                $isFavorite = in_array($pokemon['id'], $userFavoritePokemonIds);
                                $type = $pokemon['types'][0] ?? 'normal'; // 最初のタイプ、なければnormal
                                $bgColorClass = "bg-{$type}"; // Tailwind CSSのカスタムカラーを使用
                            @endphp
                            <div class="pokemon-card bg-white rounded-lg shadow-md overflow-hidden relative {{ $bgColorClass }} bg-opacity-70 text-white p-4 flex flex-col items-center justify-center cursor-pointer" data-pokemon-id="{{ $pokemon['id'] }}">
                                {{-- お気に入りボタン --}}
                                <button data-pokemon-id="{{ $pokemon['id'] }}" data-pokemon-name="{{ $pokemon['name'] }}" data-pokemon-image="{{ $pokemon['image_url'] }}" 
                                        class="favorite-toggle-btn absolute top-2 right-2 text-2xl transition-colors duration-200 
                                               {{ $isFavorite ? 'text-red-500 hover:text-red-600' : 'text-gray-300 hover:text-red-400' }}">
                                    {{ $isFavorite ? '♥' : '♡' }}
                                </button>
                                <h3 class="text-2xl font-bold capitalize mb-2">{{ $pokemon['name'] }}</h3>
                                <img src="{{ $pokemon['image_url'] }}" alt="{{ $pokemon['name'] }}" class="w-24 h-24 object-contain">
                                <p class="text-lg">ID: #{{ str_pad($pokemon['id'], 3, '0', STR_PAD_LEFT) }}</p>
                                <div class="flex space-x-2 mt-2">
                                    @foreach ($pokemon['types'] as $type)
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold bg-white bg-opacity-30">{{ $type }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 col-span-full">ポケモンが見つかりませんでした。</p>
                        @endforelse
                    </div>

                    {{-- ページネーションの表示エリア (JavaScriptで生成) --}}
                    <div id="pagination" class="mt-8 flex justify-center">
                        {{-- JavaScriptでページネーションボタンがここに挿入されます --}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- JavaScriptファイルをViteで読み込むタグ --}}
    @vite('resources/js/pokemon_index.js')
</x-app-layout>