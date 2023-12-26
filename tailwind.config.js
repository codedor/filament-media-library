/** @type {import('tailwindcss').Config} */
module.exports = {
    content: ['./resources/views/**/*.blade.php'],
    darkMode: 'class',
    theme: {},
    safelist: [
      'fi-ta-record',
    ],
    corePlugins: {
        preflight: false,
    },
    plugins: [
      require('@tailwindcss/container-queries'),
    ],
}
