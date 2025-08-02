import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                // Laravel Auth Starter assets
                'resources/js/auth-starter/app.js',
                'resources/css/auth-starter/app.css',
            ],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    resolve: {
        alias: {
            '@auth-starter': '/resources/js/auth-starter',
        },
    },
    css: {
        preprocessorOptions: {
            scss: {
                additionalData: `@import "admin-lte/dist/css/adminlte.min.css";`
            }
        }
    }
});
