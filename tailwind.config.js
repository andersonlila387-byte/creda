/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./**/*.php",
    "./**/*.html",
    "./assets/js/**/*.js",
    "./assets/css/**/*.css"
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ["Space Grotesk", "Satoshi", "Inter", "Outfit", "Plus Jakarta Sans", "sans-serif"],
        heading: ["Cabinet Grotesk", "Clash Display", "Space Grotesk", "sans-serif"],
        display: ["Clash Display", "Syne", "sans-serif"],
      },
      colors: {
        brand: {
          blue: '#1952E1',
          navy: '#0A2342',
          dark: '#0f172a',
        },
        brandBlue: '#1952E1',
        brandBlueDark: '#123BB0',
        brandDark: '#0E131F',
        canvas: '#EFF2F7',
        cardBg: '#FFFFFF',
      },
      boxShadow: {
        'subtle': '0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px -1px rgba(0, 0, 0, 0.05)',
        'card': '0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05)',
        'hover': '0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -4px rgba(0, 0, 0, 0.04)',
      }
    },
  },
  plugins: [],
}
