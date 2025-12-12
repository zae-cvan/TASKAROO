import defaultTheme from 'tailwindcss/defaultTheme'
import forms from '@tailwindcss/forms'

/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],

  theme: {
    extend: {
      fontFamily: {
        sans: ['Figtree', ...defaultTheme.fontFamily.sans],
      },
      colors: {
        // Brand palette: focused on orange + gray only
        primary: {
          50:  '#FFFCF8',
          100: '#FFF5F0',
          200: '#FFE6D5',
          300: '#FFD4B8',
          400: '#FFC19A',
          500: '#FF9E4A',
          600: '#F07A2A',
          700: '#D8611F',
          800: '#B84E18',
          900: '#8F3912',
        },
        accent: {
          light: '#FFF0E6',
          main:  '#F07A2A',
          dark:  '#B84E18',
        },

        // Ensure any use of pink/violet/purple maps to our orange theme
        pink: {
          50:  '#FFF5F0',
          100: '#FFE6D5',
          200: '#FFD4B8',
          300: '#FFC19A',
          400: '#FFAA78',
          500: '#FF9E4A',
          600: '#F07A2A',
          700: '#D8611F',
          800: '#B84E18',
          900: '#8F3912',
        },
        violet: {
          50:  '#FFF5F0',
          100: '#FFE6D5',
          200: '#FFD4B8',
          300: '#FFC19A',
          400: '#FFAA78',
          500: '#FF9E4A',
          600: '#F07A2A',
          700: '#D8611F',
          800: '#B84E18',
          900: '#8F3912',
        },
        purple: {
          50:  '#FFF5F0',
          100: '#FFE6D5',
          200: '#FFD4B8',
          300: '#FFC19A',
          400: '#FFAA78',
          500: '#FF9E4A',
          600: '#F07A2A',
          700: '#D8611F',
          800: '#B84E18',
          900: '#8F3912',
        },

        // Keep a clear gray scale for neutral UI elements
        gray: {
          50:  '#F9FAFB',
          100: '#F3F4F6',
          200: '#E5E7EB',
          300: '#D1D5DB',
          400: '#9CA3AF',
          500: '#6B7280',
          600: '#4B5563',
          700: '#374151',
          800: '#1F2937',
          900: '#111827',
        },
      },
    },
  },

  plugins: [forms],
}
