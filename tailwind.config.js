import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms'
import typography from '@tailwindcss/typography'
import colors from 'tailwindcss/colors'
import {fontFamily} from 'tailwindcss/defaultTheme.js'
/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['var(--font-space-grotesk)', ...fontFamily.sans],
            },
            colors: {
                primary: colors.pink,
                gray: colors.gray,
            },
            zIndex: {
                60: '60',
                70: '70',
                80: '80',
            },
        },
    },

    plugins: [forms, typography],
};
