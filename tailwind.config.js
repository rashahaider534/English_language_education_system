import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                tajawal: ['Tajawal', ...defaultTheme.fontFamily.sans],
                poppins: ['Poppins', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                fluent: {
                    sky: '#A8E8F9',
                    primary: '#00537A',
                    dark: '#013C58',
                    orange: '#F5A201',
                    lightOrange: '#FFBA42',
                    yellow: '#FFD35B',
                },
            },
        },
    },

    plugins: [forms],
};
