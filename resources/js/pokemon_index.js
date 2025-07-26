// resources/js/pokemon_index.js

document.addEventListener('DOMContentLoaded', async function() {
    const pokemonListDiv = document.getElementById('pokemonList');
    const paginationDiv = document.getElementById('pagination');

    // ページネーションの状態管理
    let currentLimit = 20; // 1ページあたりのポケモン数
    let currentOffset = 0; // 現在の開始位置
    let totalPokemonCount = 0; // 全体のポケモン数

    // エラーやローディングメッセージを表示するヘルパー関数
    function displayMessage(message, isError = false) {
        pokemonListDiv.innerHTML = `<p class="${isError ? 'text-red-500' : 'text-gray-500'} col-span-full">${message}</p>`;
    }

    // ポケモンカードを生成する関数
    function createPokemonCard(pokemon) {
        // タイプは今はシンプルに最初のものだけ。もしなければ'unknown'
        const type = pokemon.types && pokemon.types.length > 0 ? pokemon.types[0] : 'unknown';
        const bgColorClass = `bg-${type}`; // Tailwind CSSのカスタムカラーを使用
        // お気に入りボタンはHTMLには残すが、JSの機能は外す
        // data-pokemon-id だけは、詳細ページ遷移で使うので残す
        return `
            <div class="pokemon-card rounded-lg shadow-md overflow-hidden relative ${bgColorClass} bg-opacity-70 p-4 flex flex-col items-center justify-center cursor-pointer" data-pokemon-id="${pokemon.id}">
                <button class="favorite-toggle-btn absolute top-2 right-2 text-2xl text-gray-300">
                    ♡
                </button>
                <h3 class="text-2xl font-bold capitalize mb-2">${pokemon.name}</h3>
                <img src="${pokemon.image_url}" alt="${pokemon.name}" class="w-24 h-24 object-contain">
                <p class="text-lg">ID: #${String(pokemon.id).padStart(3, '0')}</p>
                <div class="flex space-x-2 mt-2">
                    <span class="px-2 py-1 rounded-full text-xs font-semibold bg-white bg-opacity-30">${type}</span>
                </div>
            </div>
        `;
    }

    // PokeAPIからポケモンデータを取得し、表示するメイン関数
    async function fetchAndDisplayPokemons() {
        displayMessage('ロード中...'); // ローディング表示
        paginationDiv.innerHTML = ''; // ページネーションを一時クリア

        try {
            // ★ メインのリクエスト（async/await）
            const response = await fetch(`https://pokeapi.co/api/v2/pokemon?limit=${currentLimit}&offset=${currentOffset}`);
            
            // ★ エラーハンドリング (try/catch)
            if (!response.ok) {
                // HTTPステータスが200以外の場合
                const errorData = await response.json();
                throw new Error(errorData.detail || `PokeAPIからデータ取得失敗: ${response.status}`);
            }

            const data = await response.json();
            totalPokemonCount = data.count; // 全体数を更新

            // 各ポケモンの詳細データ（タイプなど）も取得
            // Promise.all を使って並列処理。失敗した場合はcatchでまとめて処理。
            const pokemons = await Promise.all(
                data.results.map(async (pokemonResult) => {
                    const urlParts = pokemonResult.url.split('/').filter(Boolean);
                    const pokemonId = parseInt(urlParts[urlParts.length - 1]);
                    const imageUrl = `https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/${pokemonId}.png`;

                    // ★ 各ポケモンの詳細リクエスト（async/await）
                    const detailResponse = await fetch(`https://pokeapi.co/api/v2/pokemon/${pokemonId}`);
                    
                    // ★ 各詳細リクエストのエラーハンドリング
                    if (!detailResponse.ok) {
                        const detailErrorData = await detailResponse.json();
                        // ここでは個別の詳細取得失敗はログに留め、エラーとしない
                        console.warn(`Failed to fetch details for Pokemon ID ${pokemonId}:`, detailErrorData.detail || detailResponse.status);
                        return { // 基本情報のみ返す
                            id: pokemonId,
                            name: pokemonResult.name,
                            image_url: imageUrl,
                            types: ['unknown'], // タイプが不明な場合は'unknown'
                        };
                    }

                    const detailData = await detailResponse.json();
                    return {
                        id: pokemonId,
                        name: pokemonResult.name,
                        image_url: imageUrl,
                        types: detailData.types.map(t => t.type.name),
                    };
                })
            );
            
            // DOMを更新
            if (pokemons.length > 0) {
                pokemonListDiv.innerHTML = pokemons.map(createPokemonCard).join('');
            } else {
                displayMessage('ポケモンが見つかりませんでした。');
            }

            // ページネーションを表示
            displayPagination();

        } catch (error) {
            console.error('ポケモンデータの取得中にエラーが発生しました:', error);
            displayMessage(`ポケモンデータの読み込みに失敗しました: ${error.message}`, true);
            paginationDiv.innerHTML = '';
        }
    }

    // ページネーションリンクを表示する関数
    function displayPagination() {
        paginationDiv.innerHTML = '';
        const totalPages = Math.ceil(totalPokemonCount / currentLimit);
        const currentPage = (currentOffset / currentLimit) + 1;

        const prevButton = document.createElement('button');
        prevButton.textContent = '< 前へ';
        prevButton.classList.add('px-4', 'py-2', 'mx-2', 'bg-blue-500', 'text-white', 'rounded', 'hover:bg-blue-600', 'transition-colors');
        if (currentPage === 1) {
            prevButton.disabled = true;
            prevButton.classList.add('opacity-50', 'cursor-not-allowed');
        }
        prevButton.addEventListener('click', () => {
            const newOffset = currentOffset - currentLimit;
            if (newOffset >= 0) {
                currentOffset = newOffset;
                fetchAndDisplayPokemons(); // データ再取得
            }
        });
        paginationDiv.appendChild(prevButton);

        const pageInfo = document.createElement('span');
        pageInfo.textContent = `ページ ${currentPage} / ${totalPages}`;
        pageInfo.classList.add('mx-4', 'text-gray-700', 'font-semibold');
        paginationDiv.appendChild(pageInfo);

        const nextButton = document.createElement('button');
        nextButton.textContent = '次へ >';
        nextButton.classList.add('px-4', 'py-2', 'mx-2', 'bg-blue-500', 'text-white', 'rounded', 'hover:bg-blue-600', 'transition-colors');
        if (currentPage === totalPages || totalPages === 0) {
            nextButton.disabled = true;
            nextButton.classList.add('opacity-50', 'cursor-not-allowed');
        }
        nextButton.addEventListener('click', () => {
            const newOffset = currentOffset + currentLimit;
            if (newOffset < totalPokemonCount) {
                currentOffset = newOffset;
                fetchAndDisplayPokemons(); // データ再取得
            }
        });
        paginationDiv.appendChild(nextButton);
    }
    
    // イベント委譲でポケモンカードクリックを処理（シンプル化のため）
    pokemonListDiv.addEventListener('click', function(event) {
        const card = event.target.closest('.pokemon-card'); // クリックされた要素から一番近い.pokemon-cardを探す
        if (card) {
            const pokemonId = card.dataset.pokemonId;
            // お気に入りボタンがクリックされた場合は、詳細ページに飛ばさない
            if (!event.target.classList.contains('favorite-toggle-btn')) {
                window.location.href = `/pokemons/${pokemonId}`; // 詳細ページへ遷移
            }
        }
    });

    // ページ読み込み時にポケモンデータを取得して表示
    fetchAndDisplayPokemons();
});