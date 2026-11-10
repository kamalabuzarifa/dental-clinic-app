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
                sans: ['Tajawal', ...defaultTheme.fontFamily.sans],
                tajawal: ['Tajawal', 'sans-serif'],
                cairo: ['Cairo', 'sans-serif'],
            },
            colors: {
                'brand-blue': {
                    DEFAULT: '#1E40AF',
                    50: '#eff6ff',
                    100: '#dbeafe',
                    200: '#bfdbfe',
                    300: '#93c5fd',
                    400: '#60a5fa',
                    500: '#3b82f6',
                    600: '#2563eb',
                    700: '#1d4ed8',
                    800: '#1e40af',
                    900: '#1e3a8a',
                },
                'brand-sage': {
                    DEFAULT: '#87A96B',
                    50: '#f4f8f3',
                    100: '#e5eee3',
                    200: '#cdddca',
                    300: '#adc6a7',
                    400: '#87a96b',
                    500: '#739556',
                    600: '#5c7844',
                    700: '#485e36',
                    800: '#3a4b2c',
                    900: '#303f26',
                },
                primary: {
                    DEFAULT: '#1E40AF',
                    50: '#eff6ff',
                    100: '#dbeafe',
                    600: '#2563eb',
                    700: '#1d4ed8',
                    800: '#1e40af',
                    900: '#1e3a8a',
                },
                accent: {
                    DEFAULT: '#87A96B',
                    light: '#F4F8F3',
                    dark: '#5C7844',
                    50: '#f4f8f3',
                    100: '#e5eee3',
                    500: '#87A96B',
                    600: '#739556',
                },
                surface: '#F8FAFC',
                sage: {
                    DEFAULT: '#87A96B',
                    50: '#f4f8f3',
                    100: '#e5eee3',
                    500: '#87A96B',
                    600: '#739556',
                },
            },
            boxShadow: {
                'soft-shadow': '0 10px 30px -5px rgba(30, 64, 175, 0.08), 0 4px 12px -2px rgba(0, 0, 0, 0.03)',
                'soft-shadow-hover': '0 20px 40px -8px rgba(30, 64, 175, 0.14), 0 8px 20px -4px rgba(0, 0, 0, 0.05)',
                'luxury': '0 10px 30px -5px rgba(30, 64, 175, 0.08), 0 4px 12px -2px rgba(0, 0, 0, 0.03)',
                'luxury-hover': '0 20px 40px -8px rgba(30, 64, 175, 0.14), 0 8px 20px -4px rgba(0, 0, 0, 0.05)',
                'blue-glow': '0 10px 25px -3px rgba(30, 64, 175, 0.25)',
                'sage-glow': '0 10px 25px -3px rgba(135, 169, 107, 0.3)',
            },
            keyframes: {
                'fade-in-up': {
                    '0%': { opacity: '0', transform: 'translateY(24px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                'float': {
                    '0%, 100%': { transform: 'translateY(0px)' },
                    '50%': { transform: 'translateY(-10px)' },
                },
            },
            animation: {
                'fade-in-up': 'fade-in-up 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards',
                'float': 'float 4s ease-in-out infinite',
            },
        },
    },

    plugins: [forms],
};
