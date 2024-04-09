import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'

export default defineConfig({
  plugins: [
    laravel({
      publicDirectory: 'resources',
      buildDirectory: 'dist',
      input: ['resources/sass/picture.scss']
    })
  ],
  build: {
    rollupOptions: {
      output: {
        assetFileNames: `assets/[name].[ext]`
      }
    }
  }
})
