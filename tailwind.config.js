import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

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
                sans: ['"Source Sans 3"', ...defaultTheme.fontFamily.sans],
                display: ['"Outfit"', ...defaultTheme.fontFamily.sans],
            },
            typography: (theme) => ({
                DEFAULT: {
                    css: {
                        h1: { fontFamily: theme('fontFamily.display').join(', ') },
                        h2: { fontFamily: theme('fontFamily.display').join(', ') },
                        h3: { fontFamily: theme('fontFamily.display').join(', ') },
                        h4: { fontFamily: theme('fontFamily.display').join(', ') },
                    },
                },
            }),
            colors: {
                lake: {
                    950: '#0a1f33',
                    900: '#0f2940',
                    800: '#1a4d6d',
                    700: '#256089',
                    100: '#e8f0f7',
                },
                olive: {
                    800: '#3d4d35',
                    700: '#4a5d3f',
                    600: '#5c6f4f',
                    100: '#f0f3ec',
                },
                gold: {
                    700: '#8a6d1f',
                    600: '#b8860b',
                    500: '#c9a227',
                },
            },
        },
    },

    plugins: [forms, typography],
};
