import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import path from 'path';
import tailwindcss from '@tailwindcss/vite'; // ◄--- Ensure import points here
import { defineConfig } from 'vite';

export default defineConfig({
    plugins: [
        tailwindcss(), // ◄--- Order matters: Place it first in the array!
        laravel({
            input: ['resources/js/app.ts'],
            refresh: [
                'resources/routes/**',
                'resources/views/**',
                'modules/**/routes/**',
                'modules/**/Resources/views/**',
                'modules/**/Resources/js/**/*.{vue,js}',
            ],
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
            '@': path.resolve(__dirname, './resources/js'),
        },
    },
    //  server: {
    //     host: '0.0.0.0', // This allows the server to be reached via any local IP/domain
    //     port: 5173,
    //     strictPort: true,
    //     hmr: {
    //         host: '103.189.5.82', // This tells the browser WHERE to find Vite
    //     },
    // },
});