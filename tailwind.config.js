import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './app/Http/Controllers/**/*.php',
        './app/Support/**/*.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['"Inter Variable"', 'Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Brand blue sampled from the Viva logo (28, 156, 216).
                brand: {
                    50: '#eef9fe',
                    100: '#d6f0fb',
                    200: '#b0e2f7',
                    300: '#77cdf0',
                    400: '#3cb4e5',
                    500: '#1c9cd8',
                    600: '#137db5',
                    700: '#146492',
                    800: '#165377',
                    900: '#17445f',
                },
                // Deep navy used by the sidebar and the login panel.
                ink: {
                    700: '#143049',
                    800: '#0e2438',
                    900: '#0a1b2a',
                    950: '#06121d',
                },
                // Team colours from the client workflow document.
                team: {
                    sales: '#2563eb',
                    director: '#f97316',
                    branding: '#ec4899',
                    design: '#9333ea',
                    purchase: '#7c3aed',
                    accounts: '#16a34a',
                    logistics: '#1e40af',
                    dispatch: '#0284c7',
                    done: '#16a34a',
                },
            },
            borderRadius: {
                xl: '0.875rem',
                '2xl': '1.125rem',
            },
            boxShadow: {
                card: '0 1px 2px rgba(10, 27, 42, 0.05), 0 4px 16px -6px rgba(10, 27, 42, 0.08)',
                lift: '0 2px 4px rgba(10, 27, 42, 0.05), 0 16px 32px -12px rgba(10, 27, 42, 0.18)',
                glow: '0 0 0 1px rgba(28, 156, 216, 0.25), 0 10px 40px -8px rgba(28, 156, 216, 0.55)',
            },
            keyframes: {
                'fade-up': {
                    '0%': { opacity: '0', transform: 'translateY(10px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                'fade-in': {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                'float-slow': {
                    '0%, 100%': { transform: 'translate3d(0, 0, 0) scale(1)' },
                    '50%': { transform: 'translate3d(30px, -40px, 0) scale(1.08)' },
                },
                'float-slower': {
                    '0%, 100%': { transform: 'translate3d(0, 0, 0) scale(1)' },
                    '50%': { transform: 'translate3d(-40px, 30px, 0) scale(1.12)' },
                },
                'gradient-pan': {
                    '0%': { backgroundPosition: '0% 50%' },
                    '50%': { backgroundPosition: '100% 50%' },
                    '100%': { backgroundPosition: '0% 50%' },
                },
                'pulse-ring': {
                    '0%': { transform: 'scale(0.9)', opacity: '0.7' },
                    '100%': { transform: 'scale(2.1)', opacity: '0' },
                },
                'travel': {
                    '0%': { left: '0%', opacity: '0' },
                    '10%': { opacity: '1' },
                    '90%': { opacity: '1' },
                    '100%': { left: '100%', opacity: '0' },
                },
                'shimmer': {
                    '0%': { backgroundPosition: '-200% 0' },
                    '100%': { backgroundPosition: '200% 0' },
                },
                'bar-grow': {
                    '0%': { transform: 'scaleX(0)' },
                    '100%': { transform: 'scaleX(1)' },
                },
                'pop': {
                    '0%': { opacity: '0', transform: 'scale(0.85)' },
                    '100%': { opacity: '1', transform: 'scale(1)' },
                },
            },
            animation: {
                'fade-up': 'fade-up 0.45s cubic-bezier(0.22, 1, 0.36, 1) both',
                'fade-in': 'fade-in 0.4s ease-out both',
                'float-slow': 'float-slow 14s ease-in-out infinite',
                'float-slower': 'float-slower 18s ease-in-out infinite',
                'gradient-pan': 'gradient-pan 16s ease infinite',
                'pulse-ring': 'pulse-ring 2.4s cubic-bezier(0.2, 0.6, 0.3, 1) infinite',
                'travel': 'travel 5s linear infinite',
                'shimmer': 'shimmer 1.6s linear infinite',
                'bar-grow': 'bar-grow 0.9s cubic-bezier(0.22, 1, 0.36, 1) both',
                'pop': 'pop 0.35s cubic-bezier(0.22, 1, 0.36, 1) both',
            },
        },
    },

    plugins: [forms],
};
