/** @type {import('tailwindcss').Config} */
module.exports = {
  important: true,
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {},
    fontFamily: {},
  },
  plugins: [require('tailwindcss-primeui')]
}

