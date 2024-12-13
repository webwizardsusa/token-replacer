import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

const devScripts = [
    'packages/rich-editor/resources/js/FPRichEditor.js',
    'packages/media/resources/js/MediaBrowserApi.js',
    'packages/media/resources/js/RichEditorMediaPlugin.js',
]
const prodScripts = [
    'resources/css/app.css',
    'resources/js/app.js',
    'resources/js/Editor/OEmbed/oembed.js',
    'resources/css/filament/admin/theme.css'
]

export default defineConfig((({ command }) => ({
    plugins: [
        laravel({
            input: command === 'serve' ? devScripts.concat(prodScripts) : prodScripts,
            refresh: [
                'resources/views/**/*.blade.php', // Default Blade files
                'app/View/Components/**/*.php',  // Custom view components
                'custom/views/**/*.blade.php',   // Example custom directory
                'packages/**/views/**/*.blade.php',
                'packages/**/resources/js/*.js',
                'packages/**/resources/css/*.js',
                'packages/**/src/Filament/**/*.php',
                'packages/media/resources/js/**/*.js',
                'packages/media/resources/css/*.css',
                'resources/css/filament/admin/theme.css',
            ],
        }),

    ],
})));
