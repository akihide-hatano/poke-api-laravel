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
                    {{-- ポケモンリストが描画される場所 --}}
                    <div id="favoritePokemonList" class="pokemon-grid grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        <p>お気に入りのポケモンをロード中...</p>
                    </div>
                    {{-- ページネーションが必要ならここに div を追加 --}}
                    <div id="favoritePagination" class="flex justify-center mt-6"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- 作成するJavaScriptファイルを読み込む --}}
    @push('scripts')
        <script src="{{ asset('js/favorite_pokemons_index.js') }}"></script>
    @endpush
</x-app-layout>