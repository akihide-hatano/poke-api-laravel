// resources/js/favorite_pokemons_index.js

const POKEMON_API_BASE_URL = 'https://pokeapi.co/api/v2/pokemon/';
const favoritePokemonListDiv = document.getElementById('favoritePokemonList');

function displayMessage(message, isError = false) {
    if (favoritePokemonListDiv) {
        favoritePokemonListDiv.innerHTML = `<p class="${isError ? 'text-red-500' : 'text-gray-500'} col-span-full text-center">${message}</p>`;
    }
}

async function fetchPokemonData(pokemonId) {
    try {
        const response = await fetch(`${POKEMON_API_BASE_URL}${pokemonId}/`);
        if (!response.ok) {
            throw new Error(`ポケモンデータ取得失敗 (ID: ${pokemonId}): ${response.status}`);
        }
        const data = await response.json();
        return {
            id: data.id,
            name: data.name,
            imageUrl: data.sprites.front_default || data.sprites.other['official-artwork'].front_default,
        };
    } catch (error) {
        console.error(`Error fetching Pokemon ID ${pokemonId}:`, error);
        return null; // エラー時はnullを返す
    }
}

function createFavoritePokemonCard(pokemon) {
    // 受け取るデータはPokeAPIから取得したデータになる
    return `
        <div class="pokemon-card bg-white rounded-lg shadow-md overflow-hidden p-4 flex flex-col items-center">
            <h2 class="text-2xl font-bold mb-2 capitalize">${pokemon.name}</h2>
            <img src="${pokemon.imageUrl}" alt="${pokemon.name}" class="w-24 h-24 object-contain mb-2">
            <p class="text-lg">ID: #${String(pokemon.id).padStart(3, '0')}</p>
            <p class="text-sm text-gray-600 mt-2">（※登録日はデータベースから別途取得しない限り表示できません）</p>
            </div>
    `;
}

async function fetchAndDisplayFavoritePokemons() {
    displayMessage('お気に入りのポケモンをロード中...');

    try {
        // Laravelのバックエンドからお気に入りのポケモンIDのリストを取得
        const response = await fetch('/favorites');

        if (!response.ok) {
            const errorText = await response.text(); // JSONではない可能性も考慮しtextで取得
            throw new Error(`お気に入りIDリスト取得失敗: ${response.status} - ${errorText.substring(0, 100)}...`);
        }

        const favoritePokemonIds = await response.json(); // ★ ここでJSONとしてIDリストを取得

        if (favoritePokemonIds.length > 0) {
            // Promise.all を使って全てのポケモンデータを並行して取得
            const pokemonDataPromises = favoritePokemonIds.map(id => fetchPokemonData(id));
            const pokemons = (await Promise.all(pokemonDataPromises)).filter(p => p !== null); // 失敗したリクエストを除外

            if (pokemons.length > 0) {
                favoritePokemonListDiv.innerHTML = pokemons.map(createFavoritePokemonCard).join('');
            } else {
                displayMessage('お気に入りのポケモンが見つかりませんでした。'); // APIからの取得に失敗した場合
            }
        } else {
            displayMessage('お気に入りのポケモンはまだいません。');
        }

    } catch (error) {
        console.error('お気に入りデータの取得中にエラーが発生しました:', error);
        displayMessage(`お気に入りデータの読み込みに失敗しました: ${error.message}`, true);
    }
}

// ページロード時に実行
document.addEventListener('DOMContentLoaded', fetchAndDisplayFavoritePokemons);
// あるいは、Laravelのapp.jsの後に読み込む場合は window.onload でも良い
// window.onload = fetchAndDisplayFavoritePokemons;

console.log('favorite_pokemons_index.js が読み込まれました！'); // デバッグ用
// alert('JavaScriptが読み込まれました！'); // 念のためアラートも追加して確認