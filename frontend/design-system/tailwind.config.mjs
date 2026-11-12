/** @type {import('tailwindcss').Config} */
export default {
  content: [],
  theme: {
    extend: {
      colors: {
        burgundy: {
          deep: '#350E1C',
          dark: '#501629',
          wine: '#702B43',
        },
        rose: {
          muted: '#A56D7D',
        },
        champagne: '#D8BC9A',
        ivory: {
          soft: '#EDE3D8',
        },
        charcoal: '#171316',
        grey: {
          muted: '#9C9295',
        },
      },
      fontFamily: {
        serif: ['var(--font-serif)', 'serif'],
        sans: ['var(--font-sans)', 'sans-serif'],
      },
      typography: {
        burgundy: {
          css: {
            color: '#350E1C',
            a: { color: '#702B43' },
          },
        },
      },
    },
  },
  plugins: [],
}
