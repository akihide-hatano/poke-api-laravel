<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('ポケモン詳細') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-2xl font-bold text-gray-800 mb-6">ポケモン詳細ページ (まだコンテンツなし)</h3>
                    {{-- ここにポケモンの詳細情報が表示されます --}}
                    <p>このポケモンID: {{ $id ?? '不明' }}</p> {{-- 実際は $pokemon などの変数を受け取って表示します --}}
                    <a href="{{ route('pokemons.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                        一覧に戻る
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>