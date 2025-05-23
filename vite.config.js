<<<<<<< HEAD
import {
    defineConfig
} from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from "@tailwindcss/vite";
=======
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
>>>>>>> 3ae4eef (movidas de alexis)

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
<<<<<<< HEAD
    server: {
        cors: true,
    },
});
=======
});
>>>>>>> 3ae4eef (movidas de alexis)
