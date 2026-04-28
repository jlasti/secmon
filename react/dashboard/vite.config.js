import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'
import path from 'path'
import { fileURLToPath } from 'url'

const __filename = fileURLToPath(import.meta.url)
const __dirname = path.dirname(__filename)

export default defineConfig({
  plugins: [react()],
  build: {
      outDir: path.resolve(__dirname, '../../web/js/dist'),
      emptyOutDir: true,
      rollupOptions: {
        input: path.resolve(__dirname, './index.html'),
        output: {
          entryFileNames: 'dashboard-bundle.js',
          chunkFileNames: '[name]-[hash].js',
          assetFileNames: (assetInfo) => {
            // Keep CSS filename consistent for easy inclusion
            if (assetInfo.name && assetInfo.name.endsWith('.css')) {
              return 'dashboard-bundle.css';
            }
            return '[name]-[hash].[ext]';
          }
        }
      },
      sourcemap: true
    }
})