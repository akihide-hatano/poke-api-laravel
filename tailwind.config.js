import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            // ★★★ ここにcolorsプロパティを追加します ★★★
            colors: {
                normal: '#A8A77A',
                fire: '#EE8130',
                water: '#6390F0',
                electric: '#F7D02C',
                grass: '#7AC74C',
                ice: '#96D9D6',
                fighting: '#C22E28',
                poison: '#A33EA1',
                ground: '#E2BF65',
                flying: '#A98FF3',
                psychic: '#F95587',
                bug: '#A6B91A',
                rock: '#B6A136',
                ghost: '#735797',
                dragon: '#6F35FC',
                steel: '#B7B7CE',
                fairy: '#D685AD',
                dark: '#705746',
                unknown: '#68A090', // タイプ不明な場合など
            },
            // 他のextend設定があればここに追加
        },
    },

    plugins: [forms],

    // ★★★ そして、pluginsの下にsafelistを追加します ★★★
    safelist: [
        'bg-normal',
        'bg-fire',
        'bg-water',
        'bg-electric',
        'bg-grass',
        'bg-ice',
        'bg-fighting',
        'bg-poison',
        'bg-ground',
        'bg-flying',
        'bg-psychic',
        'bg-bug',
        'bg-rock',
        'bg-ghost',
        'bg-dragon',
        'bg-steel',
        'bg-fairy',
        'bg-dark',
        'bg-unknown',
    ]
};