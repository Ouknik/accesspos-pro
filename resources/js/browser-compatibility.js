/**
 * AccessPos Pro - Browser Compatibility Testing
 * Cross-browser testing and compatibility checks
 */

class BrowserCompatibilityTester {
    constructor() {
        this.browserInfo = this.detectBrowser();
        this.features = this.detectFeatures();
        this.issues = [];
        
        this.init();
    }

    init() {
        this.runCompatibilityTests();
        this.setupPolyfills();
        this.reportCompatibility();
    }

    /**
     * Detect browser information
     */
    detectBrowser() {
        const userAgent = navigator.userAgent;
        const vendor = navigator.vendor;
        
        let browser = 'Unknown';
        let version = 'Unknown';
        let engine = 'Unknown';

        // Chrome
        if (userAgent.includes('Chrome') && vendor.includes('Google')) {
            browser = 'Chrome';
            version = userAgent.match(/Chrome\/(\d+)/)?.[1] || 'Unknown';
            engine = 'Blink';
        }
        // Firefox
        else if (userAgent.includes('Firefox')) {
            browser = 'Firefox';
            version = userAgent.match(/Firefox\/(\d+)/)?.[1] || 'Unknown';
            engine = 'Gecko';
        }
        // Safari
        else if (userAgent.includes('Safari') && vendor.includes('Apple')) {
            browser = 'Safari';
            version = userAgent.match(/Version\/(\d+)/)?.[1] || 'Unknown';
            engine = 'WebKit';
        }
        // Edge
        else if (userAgent.includes('Edg')) {
            browser = 'Edge';
            version = userAgent.match(/Edg\/(\d+)/)?.[1] || 'Unknown';
            engine = 'Blink';
        }
        // Internet Explorer
        else if (userAgent.includes('Trident') || userAgent.includes('MSIE')) {
            browser = 'Internet Explorer';
            version = userAgent.match(/(?:MSIE |rv:)(\d+)/)?.[1] || 'Unknown';
            engine = 'Trident';
        }

        return {
            name: browser,
            version: parseInt(version),
            fullVersion: version,
            engine,
            userAgent,
            vendor,
            isMobile: /Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(userAgent),
            isTablet: /iPad|Android(?!.*Mobile)/i.test(userAgent)
        };
    }

    /**
     * Detect browser features
     */
    detectFeatures() {
        return {
            // ES6+ Features
            es6: this.supportsES6(),
            es2017: this.supportsES2017(),
            es2018: this.supportsES2018(),
            
            // CSS Features
            cssGrid: this.supportsCSSGrid(),
            cssFlexbox: this.supportsCSSFlexbox(),
            cssCustomProperties: this.supportsCSSCustomProperties(),
            cssCalc: this.supportsCSSCalc(),
            
            // JavaScript APIs
            fetch: 'fetch' in window,
            promise: 'Promise' in window,
            webComponents: 'customElements' in window,
            intersectionObserver: 'IntersectionObserver' in window,
            mutationObserver: 'MutationObserver' in window,
            
            // Storage
            localStorage: this.supportsLocalStorage(),
            sessionStorage: this.supportsSessionStorage(),
            indexedDB: 'indexedDB' in window,
            
            // Media
            webP: this.supportsWebP(),
            webM: this.supportsWebM(),
            
            // Performance
            performanceObserver: 'PerformanceObserver' in window,
            performanceNavigation: 'performance' in window && 'navigation' in performance,
            
            // Security
            https: location.protocol === 'https:',
            
            // Accessibility
            ariaLive: this.supportsARIALive(),
            focusVisible: this.supportsFocusVisible()
        };
    }

    /**
     * Check ES6 support
     */
    supportsES6() {
        try {
            return typeof Symbol !== 'undefined' && 
                   eval('class Foo {}; typeof Foo === "function"') &&
                   eval('(x => x)') instanceof Function;
        } catch {
            return false;
        }
    }

    /**
     * Check ES2017 support (async/await)
     */
    supportsES2017() {
        try {
            return eval('(async () => {})') instanceof Function;
        } catch {
            return false;
        }
    }

    /**
     * Check ES2018 support
     */
    supportsES2018() {
        try {
            return eval('({...{}})') !== undefined;
        } catch {
            return false;
        }
    }

    /**
     * Check CSS Grid support
     */
    supportsCSSGrid() {
        return CSS.supports('display', 'grid');
    }

    /**
     * Check CSS Flexbox support
     */
    supportsCSSFlexbox() {
        return CSS.supports('display', 'flex');
    }

    /**
     * Check CSS Custom Properties support
     */
    supportsCSSCustomProperties() {
        return CSS.supports('--custom', 'property');
    }

    /**
     * Check CSS calc() support
     */
    supportsCSSCalc() {
        return CSS.supports('width', 'calc(100% - 10px)');
    }

    /**
     * Check localStorage support
     */
    supportsLocalStorage() {
        try {
            const test = 'test';
            localStorage.setItem(test, test);
            localStorage.removeItem(test);
            return true;
        } catch {
            return false;
        }
    }

    /**
     * Check sessionStorage support
     */
    supportsSessionStorage() {
        try {
            const test = 'test';
            sessionStorage.setItem(test, test);
            sessionStorage.removeItem(test);
            return true;
        } catch {
            return false;
        }
    }

    /**
     * Check WebP support
     */
    supportsWebP() {
        return new Promise((resolve) => {
            const img = new Image();
            img.onload = () => resolve(true);
            img.onerror = () => resolve(false);
            img.src = 'data:image/webp;base64,UklGRhoAAABXRUJQVlA4TA0AAAAvAAAAEAcQERGIiP4HAA==';
        });
    }

    /**
     * Check WebM support
     */
    supportsWebM() {
        const video = document.createElement('video');
        return video.canPlayType('video/webm; codecs="vp8, vorbis"') !== '';
    }

    /**
     * Check ARIA Live support
     */
    supportsARIALive() {
        const div = document.createElement('div');
        div.setAttribute('aria-live', 'polite');
        return div.getAttribute('aria-live') === 'polite';
    }

    /**
     * Check :focus-visible support
     */
    supportsFocusVisible() {
        try {
            document.querySelector(':focus-visible');
            return true;
        } catch {
            return false;
        }
    }

    /**
     * Run compatibility tests
     */
    runCompatibilityTests() {
        // Check minimum browser versions
        this.checkMinimumVersions();
        
        // Check critical features
        this.checkCriticalFeatures();
        
        // Check performance features
        this.checkPerformanceFeatures();
        
        // Check accessibility features
        this.checkAccessibilityFeatures();
        
        // Check security features
        this.checkSecurityFeatures();
    }

    /**
     * Check minimum browser versions
     */
    checkMinimumVersions() {
        const minimumVersions = {
            'Chrome': 70,
            'Firefox': 65,
            'Safari': 12,
            'Edge': 79,
            'Internet Explorer': 0 // Not supported
        };

        const browser = this.browserInfo.name;
        const version = this.browserInfo.version;
        const minimum = minimumVersions[browser];

        if (browser === 'Internet Explorer') {
            this.issues.push({
                type: 'critical',
                category: 'browser',
                message: 'Internet Explorer is not supported',
                impact: 'high',
                recommendation: 'Please upgrade to a modern browser'
            });
        } else if (minimum && version < minimum) {
            this.issues.push({
                type: 'warning',
                category: 'browser',
                message: `${browser} ${version} is below minimum version ${minimum}`,
                impact: 'medium',
                recommendation: `Please upgrade to ${browser} ${minimum} or later`
            });
        }
    }

    /**
     * Check critical features
     */
    checkCriticalFeatures() {
        const criticalFeatures = [
            { name: 'es6', feature: 'ES6 JavaScript' },
            { name: 'fetch', feature: 'Fetch API' },
            { name: 'promise', feature: 'Promises' },
            { name: 'cssFlexbox', feature: 'CSS Flexbox' },
            { name: 'localStorage', feature: 'Local Storage' }
        ];

        criticalFeatures.forEach(({ name, feature }) => {
            if (!this.features[name]) {
                this.issues.push({
                    type: 'critical',
                    category: 'feature',
                    message: `${feature} is not supported`,
                    impact: 'high',
                    recommendation: 'Some features may not work properly'
                });
            }
        });
    }

    /**
     * Check performance features
     */
    checkPerformanceFeatures() {
        const performanceFeatures = [
            { name: 'performanceObserver', feature: 'Performance Observer' },
            { name: 'intersectionObserver', feature: 'Intersection Observer' },
            { name: 'mutationObserver', feature: 'Mutation Observer' }
        ];

        performanceFeatures.forEach(({ name, feature }) => {
            if (!this.features[name]) {
                this.issues.push({
                    type: 'warning',
                    category: 'performance',
                    message: `${feature} is not supported`,
                    impact: 'low',
                    recommendation: 'Performance monitoring may be limited'
                });
            }
        });
    }

    /**
     * Check accessibility features
     */
    checkAccessibilityFeatures() {
        const a11yFeatures = [
            { name: 'ariaLive', feature: 'ARIA Live Regions' },
            { name: 'focusVisible', feature: ':focus-visible pseudo-class' }
        ];

        a11yFeatures.forEach(({ name, feature }) => {
            if (!this.features[name]) {
                this.issues.push({
                    type: 'info',
                    category: 'accessibility',
                    message: `${feature} is not supported`,
                    impact: 'low',
                    recommendation: 'Accessibility features may be limited'
                });
            }
        });
    }

    /**
     * Check security features
     */
    checkSecurityFeatures() {
        if (!this.features.https && location.hostname !== 'localhost') {
            this.issues.push({
                type: 'warning',
                category: 'security',
                message: 'Site is not served over HTTPS',
                impact: 'medium',
                recommendation: 'Use HTTPS for better security'
            });
        }
    }

    /**
     * Setup polyfills for missing features
     */
    setupPolyfills() {
        // Promise polyfill
        if (!this.features.promise) {
            this.loadPolyfill('https://cdn.jsdelivr.net/npm/es6-promise@4/dist/es6-promise.auto.min.js');
        }

        // Fetch polyfill
        if (!this.features.fetch) {
            this.loadPolyfill('https://cdn.jsdelivr.net/npm/whatwg-fetch@3/dist/fetch.umd.js');
        }

        // Intersection Observer polyfill
        if (!this.features.intersectionObserver) {
            this.loadPolyfill('https://cdn.jsdelivr.net/npm/intersection-observer@0.12.0/intersection-observer.js');
        }

        // CSS Custom Properties polyfill for IE
        if (!this.features.cssCustomProperties && this.browserInfo.name === 'Internet Explorer') {
            this.loadPolyfill('https://cdn.jsdelivr.net/npm/css-vars-ponyfill@2/dist/css-vars-ponyfill.min.js');
        }

        // Focus-visible polyfill
        if (!this.features.focusVisible) {
            this.loadPolyfill('https://cdn.jsdelivr.net/npm/focus-visible@5/dist/focus-visible.min.js');
        }
    }

    /**
     * Load polyfill script
     */
    loadPolyfill(url) {
        return new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = url;
            script.onload = resolve;
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }

    /**
     * Report compatibility status
     */
    reportCompatibility() {
        const report = {
            browser: this.browserInfo,
            features: this.features,
            issues: this.issues,
            compatibility: this.getCompatibilityScore()
        };

        // Log to console in development
        if (window.location.hostname === 'localhost') {
            console.group('Browser Compatibility Report');
            console.log('Browser:', this.browserInfo);
            console.log('Features:', this.features);
            console.log('Issues:', this.issues);
            console.log('Compatibility Score:', report.compatibility);
            console.groupEnd();
        }

        // Store report globally
        window.AccessPosBrowserReport = report;

        // Display warnings if needed
        this.displayWarnings();
    }

    /**
     * Calculate compatibility score
     */
    getCompatibilityScore() {
        const totalFeatures = Object.keys(this.features).length;
        const supportedFeatures = Object.values(this.features).filter(Boolean).length;
        const score = Math.round((supportedFeatures / totalFeatures) * 100);

        let grade = 'A';
        if (score < 90) grade = 'B';
        if (score < 80) grade = 'C';
        if (score < 70) grade = 'D';
        if (score < 60) grade = 'F';

        return {
            score,
            grade,
            supported: supportedFeatures,
            total: totalFeatures
        };
    }

    /**
     * Display compatibility warnings
     */
    displayWarnings() {
        const criticalIssues = this.issues.filter(issue => issue.type === 'critical');
        
        if (criticalIssues.length > 0) {
            this.showCompatibilityModal(criticalIssues);
        } else {
            const warnings = this.issues.filter(issue => issue.type === 'warning');
            if (warnings.length > 0 && this.shouldShowWarnings()) {
                this.showCompatibilityNotification(warnings);
            }
        }
    }

    /**
     * Show compatibility modal for critical issues
     */
    showCompatibilityModal(issues) {
        const modal = document.createElement('div');
        modal.className = 'modal fade';
        modal.id = 'compatibility-modal';
        modal.innerHTML = `
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Problèmes de Compatibilité Détectés
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-3">
                            Votre navigateur présente des problèmes de compatibilité qui peuvent affecter 
                            le fonctionnement d'AccessPos Pro.
                        </p>
                        <div class="compatibility-issues">
                            ${issues.map(issue => `
                                <div class="alert alert-danger">
                                    <strong>${issue.message}</strong><br>
                                    <small>${issue.recommendation}</small>
                                </div>
                            `).join('')}
                        </div>
                        <div class="browser-recommendations">
                            <h6>Navigateurs Recommandés :</h6>
                            <ul>
                                <li>Google Chrome 70+</li>
                                <li>Mozilla Firefox 65+</li>
                                <li>Safari 12+</li>
                                <li>Microsoft Edge 79+</li>
                            </ul>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Continuer malgré tout
                        </button>
                        <a href="https://browsehappy.com/" target="_blank" class="btn btn-primary">
                            Mettre à jour le navigateur
                        </a>
                    </div>
                </div>
            </div>
        `;

        document.body.appendChild(modal);
        const bootstrapModal = new bootstrap.Modal(modal);
        bootstrapModal.show();

        // Remove modal after hide
        modal.addEventListener('hidden.bs.modal', () => {
            modal.remove();
        });
    }

    /**
     * Show compatibility notification for warnings
     */
    showCompatibilityNotification(warnings) {
        const notification = document.createElement('div');
        notification.className = 'alert alert-warning alert-dismissible compatibility-notification';
        notification.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <div class="flex-grow-1">
                    <strong>Attention :</strong> 
                    Certaines fonctionnalités peuvent être limitées sur votre navigateur.
                    <a href="#" class="alert-link ms-2" onclick="this.closest('.alert').querySelector('.compatibility-details').style.display='block'; return false;">
                        Voir les détails
                    </a>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <div class="compatibility-details mt-2" style="display: none;">
                ${warnings.map(warning => `
                    <small class="d-block">${warning.message}</small>
                `).join('')}
            </div>
        `;

        // Insert at top of content
        const content = document.querySelector('.content-wrapper') || document.body;
        content.insertBefore(notification, content.firstChild);

        // Auto-hide after 10 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 10000);
    }

    /**
     * Check if warnings should be shown
     */
    shouldShowWarnings() {
        const lastWarning = localStorage.getItem('accesspos-compatibility-warning');
        const today = new Date().toDateString();
        
        // Show warnings once per day
        if (lastWarning !== today) {
            localStorage.setItem('accesspos-compatibility-warning', today);
            return true;
        }
        
        return false;
    }

    /**
     * Get browser report
     */
    getReport() {
        return {
            browser: this.browserInfo,
            features: this.features,
            issues: this.issues,
            compatibility: this.getCompatibilityScore()
        };
    }

    /**
     * Test specific feature
     */
    testFeature(featureName) {
        return this.features[featureName] || false;
    }

    /**
     * Add compatibility info to test console
     */
    addToTestConsole() {
        if (window.accessPosTesting) {
            const report = this.getReport();
            
            // Add to performance tab
            const performanceTab = document.getElementById('performance-tab');
            if (performanceTab) {
                const compatibilitySection = document.createElement('div');
                compatibilitySection.innerHTML = `
                    <div class="performance-metric">
                        <div class="performance-metric-title">Browser Compatibility</div>
                        <div class="performance-metric-value">
                            ${report.compatibility.score}%
                            <span class="performance-metric-unit">Grade ${report.compatibility.grade}</span>
                        </div>
                        <div class="performance-metric-status ${report.compatibility.score >= 80 ? 'good' : 'warning'}">
                            ${report.browser.name} ${report.browser.fullVersion}
                        </div>
                    </div>
                `;
                performanceTab.appendChild(compatibilitySection);
            }
        }
    }
}

// Initialize browser compatibility testing
document.addEventListener('DOMContentLoaded', () => {
    const tester = new BrowserCompatibilityTester();
    
    // Add to test console if available
    setTimeout(() => {
        tester.addToTestConsole();
    }, 1000);
    
    // Make available globally
    window.accessPosBrowserTester = tester;
});

// Export for manual testing
window.BrowserCompatibilityTester = BrowserCompatibilityTester;
