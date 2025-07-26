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
                    
                    {{-- JavaScriptに渡すのはお気に入りIDリストのみ --}}
                    {{-- シンプル化のため、このIDも最初は使わないが、後で使う可能性を考慮して残す --}}
                    <input type="hidden" id="userFavoritePokemonIds" value='@json($userFavoritePokemonIds)'>
                    
                    {{-- ポケモンカードのグリッドは最初は空にしておく（JavaScriptが動的に追加） --}}
                    <div id="pokemonList" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        {{-- ポケモンはここにJavaScriptによって追加されます --}}
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