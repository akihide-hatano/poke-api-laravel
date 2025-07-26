// resources/js/pokemon_index.js

document.addEventListener('DOMContentLoaded', async function() {
    const pokemonListDiv = document.getElementById('pokemonList');
    const paginationDiv = document.getElementById('pagination');

    // Bladeから渡されたお気に入りポケモンIDのリストを取得
    const favoriteIdsElement = document.getElementById('userFavoritePokemonIds');
    let userFavoritePokemonIds = favoriteIdsElement ? JSON.parse(favoriteIdsElement.value) : [];

    // Bladeから渡されたページネーション情報
    const totalPokemonCount = parseInt(document.getElementById('totalPokemonCount').value);
    const pokemonsPerPage = parseInt(document.getElementById('currentLimit').value);
    let currentOffset = parseInt(document.getElementById('currentOffset').value);
    let currentPage = (currentOffset / pokemonsPerPage) + 1;


    // ページネーションリンクを表示する関数
    function displayPagination() {
        paginationDiv.innerHTML = ''; // 既存のページネーションをクリア
        const totalPages = Math.ceil(totalPokemonCount / pokemonsPerPage);

        // 前へボタン
        const prevButton = document.createElement('button');
        prevButton.textContent = '< 前へ';
        prevButton.classList.add('px-4', 'py-2', 'mx-2', 'bg-blue-500', 'text-white', 'rounded', 'hover:bg-blue-600', 'transition-colors');
        if (currentPage === 1) {
            prevButton.disabled = true;
            prevButton.classList.add('opacity-50', 'cursor-not-allowed');
        }
        prevButton.addEventListener('click', () => {
            const newOffset = currentOffset - pokemonsPerPage;
            if (newOffset >= 0) {
                window.location.href = `/pokemons?limit=${pokemonsPerPage}&offset=${newOffset}`;
            }
        });
        paginationDiv.appendChild(prevButton);

        // 現在のページ数を表示
        const pageInfo = document.createElement('span');
        pageInfo.textContent = `ページ ${currentPage} / ${totalPages}`;
        pageInfo.classList.add('mx-4', 'text-gray-700', 'font-semibold');
        paginationDiv.appendChild(pageInfo);

        // 次へボタン
        const nextButton = document.createElement('button');
        nextButton.textContent = '次へ >';
        nextButton.classList.add('px-4', 'py-2', 'mx-2', 'bg-blue-500', 'text-white', 'rounded', 'hover:bg-blue-600', 'transition-colors');
        if (currentPage === totalPages || totalPages === 0) {
            nextButton.disabled = true;
            nextButton.classList.add('opacity-50', 'cursor-not-allowed');
        }
        nextButton.addEventListener('click', () => {
            const newOffset = currentOffset + pokemonsPerPage;
            if (newOffset < totalPokemonCount) {
                window.location.href = `/pokemons?limit=${pokemonsPerPage}&offset=${newOffset}`;
            }
        });
        paginationDiv.appendChild(nextButton);
    }

    // お気に入りボタンのイベントリスナーを設定（ページロード時に一度だけ）
    document.querySelectorAll('.favorite-toggle-btn').forEach(button => {
        button.addEventListener('click', async function(event) {
            event.stopPropagation(); // 親要素のクリックイベントが発火しないようにする
            const pokemonId = this.dataset.pokemonId;
            const pokemonName = this.dataset.pokemonName;
            const pokemonImage = this.dataset.pokemonImage;
            
            if (this.classList.contains('text-red-500')) {
                // お気に入り解除
                await removeFavoritePokemon(pokemonId, this);
            } else {
                // お気に入り追加
                await addFavoritePokemon(pokemonId, pokemonName, pokemonImage, this);
            }
        });
    });

    // ポケモンカードのクリックイベントリスナーを設定 (詳細表示用)
    document.querySelectorAll('.pokemon-card').forEach(card => {
        card.addEventListener('click', function() {
            const pokemonId = this.dataset.pokemonId;
            window.location.href = `/pokemons/${pokemonId}`; // 詳細ページへの遷移
        });
    });


    // お気に入り追加関数（async/awaitとtry/catchを使用）
    async function addFavoritePokemon(pokemonId, pokemonName, pokemonImage, buttonElement) {
        try {
            const response = await fetch('/api/favorite-pokemons', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    pokemon_pokeapi_id: pokemonId,
                    pokemon_name: pokemonName,
                    pokemon_image_url: pokemonImage,
                })
            });

            const data = await response.json();

            if (!response.ok) {
                // HTTPステータスコードが2xx以外の場合
                if (response.status === 409) { // Conflict (既にお気に入り)
                    alert(data.message || 'このポケモンは既にお気に入りに追加されています。');
                } else {
                    throw new Error(data.message || `お気に入り追加に失敗しました。ステータス: ${response.status}`);
                }
            } else {
                // 成功の場合
                alert(data.message || 'ポケモンがお気に入りに追加されました！');
                buttonElement.classList.remove('text-gray-300', 'hover:text-red-400');
                buttonElement.classList.add('text-red-500', 'hover:text-red-600');
                buttonElement.innerHTML = '♥'; // ハートを塗りつぶす
                userFavoritePokemonIds.push(parseInt(pokemonId)); // JavaScript内のリストを更新
            }
        } catch (error) {
            console.error('Error adding favorite pokemon:', error);
            alert('お気に入り追加中にエラーが発生しました。');
        }
    }

    // お気に入り解除関数（async/awaitとtry/catchを使用）
    async function removeFavoritePokemon(pokemonId, buttonElement) {
        try {
            const response = await fetch(`/api/favorite-pokemons/${pokemonId}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || `お気に入り解除に失敗しました。ステータス: ${response.status}`);
            } else {
                alert(data.message || 'お気に入りからポケモンを削除しました。');
                buttonElement.classList.remove('text-red-500', 'hover:text-red-600');
                buttonElement.classList.add('text-gray-300', 'hover:text-red-400');
                buttonElement.innerHTML = '♡'; // ハートを空にする
                // JavaScript内のリストからIDを削除
                const index = userFavoritePokemonIds.indexOf(parseInt(pokemonId));
                if (index > -1) {
                    userFavoritePokemonIds.splice(index, 1);
                }
            }
        } catch (error) {
            console.error('Error removing favorite pokemon:', error);
            alert('お気に入り解除中にエラーが発生しました。');
        }
    }

    // ページ読み込み時またはスクリプト実行時にページネーションを表示
    displayPagination();
});