import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/custom-sb-admin.css',
                'resources/css/performance-optimizations.css',
                'resources/css/mobile-responsive.css',
                'resources/css/accessibility.css',
                'resources/css/testing-suite.css',
                'resources/js/app.js',
                'resources/js/accesspos-functions.js',
                'resources/js/performance-optimizations.js',
                'resources/js/loading-indicators.js',
                'resources/js/keyboard-shortcuts.js',
                'resources/js/accessibility.js',
                'resources/js/testing-suite.js',
                'resources/js/browser-compatibility.js'
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    build: {
        outDir: 'public/build',
        manifest: true,
        sourcemap: false, // تعطيل source maps في production
        minify: 'terser', // استخدام terser للتصغير
        terserOptions: {
            compress: {
                drop_console: true, // إزالة console.log في production
                drop_debugger: true // إزالة debugger في production
            }
        },
        rollupOptions: {
            output: {
                manualChunks: {
                    // Vendor chunks for better caching
                    vendor: ['axios'],
                    bootstrap: ['bootstrap'],
                    jquery: ['jquery'],
                    
                    // AccessPos specific chunks
                    accesspos: [
                        './resources/js/accesspos-functions.js',
                        './resources/js/performance-optimizations.js',
                        './resources/js/loading-indicators.js',
                        './resources/js/keyboard-shortcuts.js',
                        './resources/js/accessibility.js'
                    ],
                    
                    // CSS chunks
                    styles: [
                        './resources/css/custom-sb-admin.css',
                        './resources/css/performance-optimizations.css',
                        './resources/css/mobile-responsive.css',
                        './resources/css/accessibility.css'
                    ]
                },
                // تحسين أسماء الملفات للـ caching
                entryFileNames: (chunkInfo) => {
                    return 'js/[name]-[hash].js';
                },
                chunkFileNames: (chunkInfo) => {
                    return 'js/[name]-[hash].js';
                },
                assetFileNames: (assetInfo) => {
                    const info = assetInfo.name.split('.');
                    const extType = info[info.length - 1];
                    if (/\.(css)$/.test(assetInfo.name)) {
                        return 'css/[name]-[hash].[ext]';
                    }
                    if (/\.(png|jpe?g|svg|gif|tiff|bmp|ico)$/.test(assetInfo.name)) {
                        return 'images/[name]-[hash].[ext]';
                    }
                    return 'assets/[name]-[hash].[ext]';
                }
            }
        },
        // تحسينات إضافية للـ performance
        chunkSizeWarningLimit: 1000,
        assetsDir: 'assets',
    },
    server: {
        host: 'localhost',
        port: 5173,
        hmr: {
            host: 'localhost'
        }
    },
    // تحسينات للـ production
    esbuild: {
        legalComments: 'none', // إزالة التعليقات القانونية
    },
    // Optimizations للـ dependencies
    optimizeDeps: {
        include: ['axios', 'bootstrap', 'jquery'],
        exclude: ['@vitejs/plugin-react']
    }
});
