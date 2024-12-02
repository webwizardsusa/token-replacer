import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

const devScripts = ['packages/rich-editor/resources/js/FPRichEditor.js']
const prodScripts = [
    'resources/css/app.css',
    'resources/js/app.js',
    'resources/js/Editor/OEmbed/oembed.js',
    'resources/js/Editor/Image/image.js',
    'resources/css/filament/admin/theme.css'
]

export default defineConfig((({ command }) => ({
    plugins: [
        laravel({
            input: command === 'serve' ? devScripts.concat(prodScripts) : prodScripts,
            refresh: true,
        }),
    ],
})));
