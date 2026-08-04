import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 'resources/js/app.js', 'resources/js/calendar.js', 'resources/js/reports.js',
                'resources/js/map-picker.js', 'resources/js/tour-map.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
