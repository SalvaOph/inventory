import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/sass/app.scss",
                "resources/js/app.js",
                "resources/img/logo.png",
            ],
            refresh: true,
        }),
    ],
    build: {
        outDir: "public/build",
        assetsDir: "assets",
    },
    server: {
        https: true,
    },
});
