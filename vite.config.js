import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/colors.css',
                'resources/css/style.css',
                'resources/css/media.css',
                'resources/css/pages/home.css',
                'resources/js/jquery.autocomplete.min.js',
                'resources/js/main.js',
                'resources/js/cart.js',
                'resources/js/modification.js',
                'resources/js/shop-group.js',
                
            ],
            refresh: true,
        }),
    ],
});
