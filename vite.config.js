import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/sass/app.scss',
                'resources/js/app.js',
                'resources/sass/admin/admin.scss',
                'resources/js/admin/admin.js',
                'resources/sass/admin/login.scss',
                'resources/js/admin/login.js',
                'resources/sass/front/front.scss',
                'resources/js/front/front.js',
            ],
            refresh: true,
        }),
    ],
});
