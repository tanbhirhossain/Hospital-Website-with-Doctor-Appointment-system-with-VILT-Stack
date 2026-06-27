import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import path from 'path';
import tailwindcss from '@tailwindcss/vite';
import basicSsl from '@vitejs/plugin-basic-ssl'; // 1. Import the plugin
import { defineConfig } from 'vite';

export default defineConfig({
    plugins: [
        basicSsl(), // 2. Add it here
        tailwindcss(),
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
    server: {
        host: '0.0.0.0', 
        port: 8009,
        https: true, // 3. Force HTTPS protocol
        hmr: {
            host: '192.168.10.215',
        },
    },
     cors: {
        origin: 'https://amzhospitalbd.com', // Ensure this matches your HTTPS ERP URL
        credentials: true,
    },
    headers: {
        'Access-Control-Allow-Private-Network': 'true', 
    },
});
