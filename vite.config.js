// vite.config.js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/pokemon_index.js',
                'resources/js/favorite_pokemons_index.js', // ★この行があるか再確認
            ],
            refresh: true,
        }),
    ],
});