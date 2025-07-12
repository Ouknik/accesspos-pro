/**
 * AccessPos Pro - Performance Optimizations
 * Optimized JavaScript for better performance and UX
 */

class AccessPosPerformance {
    constructor() {
        this.init();
    }

    init() {
        this.setupCriticalLoading();
        this.setupLazyLoading();
        this.setupImageOptimization();
        this.setupIntersectionObserver();
        this.setupPerformanceMonitoring();
        this.setupPreloading();
        this.setupMemoryOptimization();
    }

    /**
     * Critical loading path optimization
     */
    setupCriticalLoading() {
        // Show main loader
        this.showMainLoader();

        // Critical CSS loading
        const criticalCSS = [
            '/build/assets/app.css',
            '/build/assets/custom-sb-admin.css'
        ];

        this.loadCriticalAssets(criticalCSS).then(() => {
            // Hide loader after critical assets loaded
            setTimeout(() => this.hideMainLoader(), 300);
        });

        // Progressive loading
        this.setupProgressiveLoading();
    }

    /**
     * Show main application loader
     */
    showMainLoader() {
        const loader = document.createElement('div');
        loader.id = 'main-loader';
        loader.className = 'main-loader';
        loader.innerHTML = `
            <div class="sb-admin-spinner"></div>
        `;
        document.body.appendChild(loader);
    }

    /**
     * Hide main application loader
     */
    hideMainLoader() {
        const loader = document.getElementById('main-loader');
        if (loader) {
            loader.classList.add('hidden');
            setTimeout(() => loader.remove(), 300);
        }
    }

    /**
     * Load critical assets with priority
     */
    async loadCriticalAssets(assets) {
        const promises = assets.map(asset => {
            return new Promise((resolve, reject) => {
                const link = document.createElement('link');
                link.rel = 'stylesheet';
                link.href = asset;
                link.onload = resolve;
                link.onerror = reject;
                document.head.appendChild(link);
            });
        });

        try {
            await Promise.all(promises);
        } catch (error) {
            console.warn('Error loading critical assets:', error);
        }
    }

    /**
     * Progressive loading implementation
     */
    setupProgressiveLoading() {
        // Load non-critical JS after page load
        window.addEventListener('load', () => {
            this.loadNonCriticalAssets();
        });

        // Load additional resources on user interaction
        const events = ['click', 'scroll', 'keydown'];
        events.forEach(event => {
            document.addEventListener(event, () => {
                this.loadInteractionAssets();
            }, { once: true, passive: true });
        });
    }

    /**
     * Load non-critical assets
     */
    loadNonCriticalAssets() {
        const nonCriticalAssets = [
            'https://cdn.jsdelivr.net/npm/sweetalert2@11',
            'https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js',
            'https://cdn.jsdelivr.net/npm/chart.js'
        ];

        nonCriticalAssets.forEach(asset => {
            this.loadScriptAsync(asset);
        });
    }

    /**
     * Load interaction-based assets
     */
    loadInteractionAssets() {
        // Load additional functionality when user starts interacting
        const interactionAssets = [
            '/build/assets/accesspos-functions.js'
        ];

        interactionAssets.forEach(asset => {
            this.loadScriptAsync(asset);
        });
    }

    /**
     * Async script loading utility
     */
    loadScriptAsync(src) {
        return new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = src;
            script.async = true;
            script.onload = resolve;
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }

    /**
     * Lazy loading implementation
     */
    setupLazyLoading() {
        // Lazy load images
        const images = document.querySelectorAll('img[data-src]');
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy-placeholder');
                    observer.unobserve(img);
                }
            });
        });

        images.forEach(img => imageObserver.observe(img));

        // Lazy load components
        this.setupComponentLazyLoading();
    }

    /**
     * Component lazy loading
     */
    setupComponentLazyLoading() {
        const lazyComponents = document.querySelectorAll('[data-lazy-component]');
        
        const componentObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const component = entry.target;
                    const componentType = component.dataset.lazyComponent;
                    this.loadComponent(componentType, component);
                    componentObserver.unobserve(component);
                }
            });
        }, { rootMargin: '50px' });

        lazyComponents.forEach(component => {
            componentObserver.observe(component);
        });
    }

    /**
     * Load specific component
     */
    async loadComponent(type, element) {
        switch (type) {
            case 'datatable':
                await this.loadDataTable(element);
                break;
            case 'chart':
                await this.loadChart(element);
                break;
            case 'modal':
                await this.loadModal(element);
                break;
        }
    }

    /**
     * Image optimization
     */
    setupImageOptimization() {
        // Add loading=lazy to all images
        const images = document.querySelectorAll('img:not([loading])');
        images.forEach(img => {
            img.loading = 'lazy';
            img.decoding = 'async';
        });

        // Implement responsive images
        this.setupResponsiveImages();
    }

    /**
     * Responsive images implementation
     */
    setupResponsiveImages() {
        const images = document.querySelectorAll('img[data-responsive]');
        
        images.forEach(img => {
            const sizes = img.dataset.responsive.split(',');
            let srcset = '';
            
            sizes.forEach(size => {
                const [width, src] = size.trim().split('|');
                srcset += `${src} ${width}w, `;
            });
            
            img.srcset = srcset.slice(0, -2); // Remove last comma
            img.sizes = '(max-width: 768px) 100vw, (max-width: 1200px) 50vw, 33vw';
        });
    }

    /**
     * Intersection Observer setup
     */
    setupIntersectionObserver() {
        // Animate elements on scroll
        const animatedElements = document.querySelectorAll('[data-animate]');
        
        const animationObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const element = entry.target;
                    const animation = element.dataset.animate;
                    element.classList.add(`animate-${animation}`);
                    animationObserver.unobserve(element);
                }
            });
        }, { threshold: 0.1 });

        animatedElements.forEach(el => animationObserver.observe(el));
    }

    /**
     * Performance monitoring
     */
    setupPerformanceMonitoring() {
        // Monitor Core Web Vitals
        this.monitorCoreWebVitals();
        
        // Monitor custom metrics
        this.monitorCustomMetrics();
        
        // Setup error tracking
        this.setupErrorTracking();
    }

    /**
     * Core Web Vitals monitoring
     */
    monitorCoreWebVitals() {
        // First Contentful Paint
        new PerformanceObserver((entryList) => {
            for (const entry of entryList.getEntries()) {
                if (entry.name === 'first-contentful-paint') {
                    console.log('FCP:', entry.startTime);
                }
            }
        }).observe({ entryTypes: ['paint'] });

        // Largest Contentful Paint
        new PerformanceObserver((entryList) => {
            const entries = entryList.getEntries();
            const lastEntry = entries[entries.length - 1];
            console.log('LCP:', lastEntry.startTime);
        }).observe({ entryTypes: ['largest-contentful-paint'] });

        // Cumulative Layout Shift
        let clsValue = 0;
        new PerformanceObserver((entryList) => {
            for (const entry of entryList.getEntries()) {
                if (!entry.hadRecentInput) {
                    clsValue += entry.value;
                }
            }
            console.log('CLS:', clsValue);
        }).observe({ entryTypes: ['layout-shift'] });
    }

    /**
     * Custom metrics monitoring
     */
    monitorCustomMetrics() {
        // Page load time
        window.addEventListener('load', () => {
            const loadTime = performance.now();
            console.log('Page Load Time:', loadTime);
        });

        // DOM ready time
        document.addEventListener('DOMContentLoaded', () => {
            const domTime = performance.now();
            console.log('DOM Ready Time:', domTime);
        });
    }

    /**
     * Error tracking setup
     */
    setupErrorTracking() {
        window.addEventListener('error', (event) => {
            console.error('JavaScript Error:', {
                message: event.message,
                filename: event.filename,
                lineno: event.lineno,
                colno: event.colno,
                error: event.error
            });
        });

        window.addEventListener('unhandledrejection', (event) => {
            console.error('Unhandled Promise Rejection:', event.reason);
        });
    }

    /**
     * Preloading setup
     */
    setupPreloading() {
        // Preload critical resources
        const criticalResources = [
            { href: '/fonts/nunito.woff2', as: 'font', type: 'font/woff2' },
            { href: '/images/logo.png', as: 'image' }
        ];

        criticalResources.forEach(resource => {
            const link = document.createElement('link');
            link.rel = 'preload';
            link.href = resource.href;
            link.as = resource.as;
            if (resource.type) link.type = resource.type;
            if (resource.as === 'font') link.crossOrigin = 'anonymous';
            document.head.appendChild(link);
        });

        // Prefetch next likely resources
        this.setupPrefetching();
    }

    /**
     * Prefetching setup
     */
    setupPrefetching() {
        // Prefetch on hover
        document.addEventListener('mouseover', (event) => {
            const link = event.target.closest('a[href]');
            if (link && !link.dataset.prefetched) {
                this.prefetchResource(link.href);
                link.dataset.prefetched = 'true';
            }
        });
    }

    /**
     * Prefetch resource
     */
    prefetchResource(href) {
        const link = document.createElement('link');
        link.rel = 'prefetch';
        link.href = href;
        document.head.appendChild(link);
    }

    /**
     * Memory optimization
     */
    setupMemoryOptimization() {
        // Clean up event listeners
        this.cleanupEventListeners();
        
        // Implement object pooling
        this.setupObjectPooling();
        
        // Monitor memory usage
        this.monitorMemoryUsage();
    }

    /**
     * Cleanup event listeners
     */
    cleanupEventListeners() {
        const controller = new AbortController();
        const signal = controller.signal;

        // Use AbortController for cleanup
        window.accessPosCleanup = () => {
            controller.abort();
        };

        // Add listeners with cleanup capability
        document.addEventListener('click', this.handleClick.bind(this), { signal });
        document.addEventListener('scroll', this.handleScroll.bind(this), { signal, passive: true });
    }

    /**
     * Object pooling implementation
     */
    setupObjectPooling() {
        // Create pools for frequently used objects
        window.AccessPosObjectPool = {
            modalPool: [],
            chartPool: [],
            getModal() {
                return this.modalPool.pop() || document.createElement('div');
            },
            returnModal(modal) {
                modal.innerHTML = '';
                modal.className = '';
                this.modalPool.push(modal);
            }
        };
    }

    /**
     * Memory usage monitoring
     */
    monitorMemoryUsage() {
        if ('memory' in performance) {
            setInterval(() => {
                const memInfo = performance.memory;
                console.log('Memory Usage:', {
                    used: Math.round(memInfo.usedJSHeapSize / 1024 / 1024) + ' MB',
                    total: Math.round(memInfo.totalJSHeapSize / 1024 / 1024) + ' MB',
                    limit: Math.round(memInfo.jsHeapSizeLimit / 1024 / 1024) + ' MB'
                });
            }, 30000); // Every 30 seconds
        }
    }

    /**
     * Handle click events
     */
    handleClick(event) {
        // Debounce rapid clicks
        if (event.target.dataset.clicking) return;
        event.target.dataset.clicking = 'true';
        setTimeout(() => delete event.target.dataset.clicking, 300);
    }

    /**
     * Handle scroll events
     */
    handleScroll(event) {
        // Throttle scroll events
        if (!this.scrolling) {
            this.scrolling = true;
            requestAnimationFrame(() => {
                // Handle scroll logic here
                this.scrolling = false;
            });
        }
    }

    /**
     * DataTable loading optimization
     */
    async loadDataTable(element) {
        const placeholder = element.querySelector('.lazy-placeholder');
        if (placeholder) {
            placeholder.style.display = 'block';
        }

        try {
            // Ensure DataTables is loaded
            if (!window.jQuery || !window.jQuery.fn.DataTable) {
                await this.loadScriptAsync('https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js');
            }

            // Initialize DataTable
            const table = element.querySelector('table');
            if (table) {
                $(table).DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: table.dataset.url,
                    deferRender: true,
                    scrollCollapse: true,
                    language: {
                        url: '/js/datatables-french.json'
                    }
                });
            }
        } catch (error) {
            console.error('Error loading DataTable:', error);
        } finally {
            if (placeholder) {
                placeholder.style.display = 'none';
            }
        }
    }

    /**
     * Chart loading optimization
     */
    async loadChart(element) {
        const placeholder = element.querySelector('.lazy-placeholder');
        if (placeholder) {
            placeholder.style.display = 'block';
        }

        try {
            // Ensure Chart.js is loaded
            if (!window.Chart) {
                await this.loadScriptAsync('https://cdn.jsdelivr.net/npm/chart.js');
            }

            // Initialize Chart
            const canvas = element.querySelector('canvas');
            if (canvas) {
                const config = JSON.parse(element.dataset.config);
                new Chart(canvas, config);
            }
        } catch (error) {
            console.error('Error loading Chart:', error);
        } finally {
            if (placeholder) {
                placeholder.style.display = 'none';
            }
        }
    }

    /**
     * Modal loading optimization
     */
    async loadModal(element) {
        try {
            // Ensure Bootstrap is loaded
            if (!window.bootstrap) {
                await this.loadScriptAsync('https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js');
            }

            // Initialize modal
            new bootstrap.Modal(element);
        } catch (error) {
            console.error('Error loading Modal:', error);
        }
    }
}

// Initialize performance optimizations
document.addEventListener('DOMContentLoaded', () => {
    window.accessPosPerformance = new AccessPosPerformance();
});

// Service Worker registration for caching
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
            .then(registration => {
                console.log('SW registered: ', registration);
            })
            .catch(registrationError => {
                console.log('SW registration failed: ', registrationError);
            });
    });
}
