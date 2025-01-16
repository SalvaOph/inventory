import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
            ],
            server: {
                https: true, // Fuerza HTTPS en el entorno de desarrollo
            },
            refresh: true,
        }),
    ],
    build: {
        outDir: 'public/build', // Genera los archivos en `public/build`
        manifest: true,         // Activa la generación de `manifest.json`
        rollupOptions: {
            input: 'resources/js/app.js',
        },
    },
});
