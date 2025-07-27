// resources/js/favorite_pokemons_index.js

const POKEMON_API_BASE_URL = 'https://pokeapi.co/api/v2/pokemon/';
const LARAVEL_API_FAVORITES_IDS_URL = '/api/favorite-pokemon-ids';
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
        return null;
    }
}

function createFavoritePokemonCard(pokemon) {
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

    // ★ CSRFトークンを取得 ★
    // Laravelが自動で<meta name="csrf-token" content="...">をHTMLに埋め込んでいるはずです
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    try {
        const response = await fetch(LARAVEL_API_FAVORITES_IDS_URL, {
            headers: {
                'Accept': 'application/json',
                // ★ CSRFトークンをヘッダーに含める ★
                'X-CSRF-TOKEN': csrfToken,
            }
        });

        if (!response.ok) {
            if (response.status === 401) {
                displayMessage('お気に入りの表示にはログインが必要です。', true);
            } else {
                const errorText = await response.text();
                throw new Error(`お気に入りIDリスト取得失敗: ${response.status} - ${errorText.substring(0, 100)}...`);
            }
            return;
        }

        const favoritePokemonIds = await response.json();

        if (favoritePokemonIds.length > 0) {
            const pokemonDataPromises = favoritePokemonIds.map(id => fetchPokemonData(id));
            const pokemons = (await Promise.all(pokemonDataPromises)).filter(p => p !== null);

            if (pokemons.length > 0) {
                favoritePokemonListDiv.innerHTML = pokemons.map(createFavoritePokemonCard).join('');
            } else {
                displayMessage('お気に入りのポケモンが見つかりませんでした。');
            }
        } else {
            displayMessage('お気に入りのポケモンはまだいません。');
        }

    } catch (error) {
        console.error('お気に入りデータの取得中にエラーが発生しました:', error);
        displayMessage(`お気に入りデータの読み込みに失敗しました: ${error.message}`, true);
    }
}

document.addEventListener('DOMContentLoaded', fetchAndDisplayFavoritePokemons);

console.log('favorite_pokemons_index.js が読み込まれました！');