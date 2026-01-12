import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    server: {
        allowedHosts: [
            'proscience-unimposingly-nieves.ngrok-free.dev',
            '.ngrok-free.dev'
        ],
        host: '0.0.0.0',
        hmr: {
            host: 'proscience-unimposingly-nieves.ngrok-free.dev',
        },
    },
});
