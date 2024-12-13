import * as esbuild from 'esbuild'
import { dirname, basename } from 'node:path';
import { fileURLToPath } from 'node:url';
import { existsSync, copyFile } from 'node:fs';
import { join } from 'node:path';

const __dirname = dirname(fileURLToPath(import.meta.url));

const isDev = process.argv.includes('--dev')


function findParentDirWithDirs(startDir, requiredDirs) {
    let currentDir = startDir;

    while (currentDir !== dirname(currentDir)) {
        // Check if all required directories exist in the current directory
        const hasAllDirs = requiredDirs.every((dir) => existsSync(join(currentDir, dir)));
        if (hasAllDirs) {
            return currentDir;
        }

        // Move up to the parent directory
        currentDir = dirname(currentDir);
    }

    return null; // Return null if no matching parent directory is found
}

let projectRoot = findParentDirWithDirs(__dirname, ['public', 'composer.lock']);

let publicPath = null;
if (projectRoot) {
    publicPath = join(projectRoot, 'public/js/filapress/media/');
}

async function compile(options) {

    if (true) {
        const onEndPlugin = {
            name: 'on-end',
            setup(build) {
                build.onEnd((result) => {
                    console.log(`build ended with ${result.errors.length} errors`);
                    if (publicPath) {
                        let destination = join(publicPath, basename(options.outfile));
                        copyFile(options.outfile, destination, (err) => {
                        })
                        console.log(destination);
                    }
                });
            },
        };
        let plugins = options.plugins;
        if (!plugins) {
            plugins = [];
        }
        plugins.push(onEndPlugin);
        options.plugins = plugins;
    }

    const context = await esbuild.context(options)

    if (isDev) {
        await context.watch()
    } else {
        await context.rebuild()
        await context.dispose()
    }
}

const defaultOptions = {
    define: {
        'process.env.NODE_ENV': isDev ? `'development'` : `'production'`,
    },
    bundle: true,
    mainFields: ['module', 'main'],
    platform: 'neutral',
    sourcemap: isDev ? 'inline' : false,
    sourcesContent: isDev,
    treeShaking: true,
    target: ['es2020'],
    minify: !isDev,
    plugins: [{
        name: 'watchPlugin',
        setup: function (build) {
            build.onStart(() => {
                console.log(`Build started at ${new Date(Date.now()).toLocaleTimeString()}: ${build.initialOptions.outfile}`)
            })

            build.onEnd((result) => {
                if (result.errors.length > 0) {
                    console.log(`Build failed at ${new Date(Date.now()).toLocaleTimeString()}: ${build.initialOptions.outfile}`, result.errors)
                } else {
                    console.log(`Build finished at ${new Date(Date.now()).toLocaleTimeString()}: ${build.initialOptions.outfile}`)
                }
            })
        }
    }],
}

compile({
    ...defaultOptions,
    entryPoints: ['./resources/js/MediaBrowserApi.js'],
    outfile: './dist/media-api.js',
}).then(() => {
    compile({
        ...defaultOptions,
        entryPoints: ['./resources/js/RichEditorMediaPlugin.js'],
        outfile: './dist/media-editor-plugin.js',
    }).then(() => {console.log('done')})
})



