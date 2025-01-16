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
        manifest: true, // Asegura que Laravel reconozca los archivos generados
    },
});
