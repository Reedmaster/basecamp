module.exports = {
  purge: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
  ],
  darkMode: false, // or 'media' or 'class'
  theme: {
    extend: {},
    minHeight:{
      '200px': '200px',
      '300px': '300px',
    },
  },
  variants: {
    extend: {},
  },
  plugins: [],
}
