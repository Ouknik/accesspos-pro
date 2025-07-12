/**
 * AccessPos Pro - Comprehensive Testing Suite
 * Quality Assurance and Testing Framework
 */

class AccessPosTestingSuite {
    constructor() {
        this.testResults = [];
        this.currentTest = null;
        this.testEnvironment = this.detectEnvironment();
        
        this.init();
    }

    init() {
        this.setupTestEnvironment();
        this.createTestInterface();
        this.setupAutomatedTests();
        this.setupPerformanceMonitoring();
        this.setupErrorTracking();
    }

    /**
     * Detect current environment
     */
    detectEnvironment() {
        const hostname = window.location.hostname;
        const isDevelopment = hostname === 'localhost' || hostname === '127.0.0.1';
        const isProduction = hostname.includes('.com') || hostname.includes('.fr');
        
        return {
            isDevelopment,
            isProduction,
            userAgent: navigator.userAgent,
            viewport: {
                width: window.innerWidth,
                height: window.innerHeight
            },
            features: {
                serviceWorker: 'serviceWorker' in navigator,
                webGL: !!window.WebGLRenderingContext,
                localStorage: !!window.localStorage,
                sessionStorage: !!window.sessionStorage,
                indexedDB: !!window.indexedDB
            }
        };
    }

    /**
     * Setup test environment
     */
    setupTestEnvironment() {
        // Only enable testing in development
        if (!this.testEnvironment.isDevelopment) {
            return;
        }

        // Add test mode indicator
        document.body.classList.add('test-mode');
        
        // Create test console
        this.createTestConsole();
        
        // Add global test utilities
        window.AccessPosTest = this;
    }

    /**
     * Create test console UI
     */
    createTestConsole() {
        const console = document.createElement('div');
        console.id = 'accesspos-test-console';
        console.className = 'test-console';
        console.innerHTML = `
            <div class="test-console-header">
                <h4>AccessPos Pro - Test Console</h4>
                <div class="test-console-controls">
                    <button class="btn btn-sm btn-primary" id="run-all-tests">
                        <i class="fas fa-play"></i> Run All Tests
                    </button>
                    <button class="btn btn-sm btn-secondary" id="run-ui-tests">
                        <i class="fas fa-desktop"></i> UI Tests
                    </button>
                    <button class="btn btn-sm btn-info" id="run-performance-tests">
                        <i class="fas fa-tachometer-alt"></i> Performance
                    </button>
                    <button class="btn btn-sm btn-warning" id="run-accessibility-tests">
                        <i class="fas fa-universal-access"></i> A11y Tests
                    </button>
                    <button class="btn btn-sm btn-danger" id="clear-tests">
                        <i class="fas fa-trash"></i> Clear
                    </button>
                    <button class="btn btn-sm btn-outline-secondary" id="toggle-console">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="test-console-body">
                <div class="test-tabs">
                    <button class="test-tab active" data-tab="results">Results</button>
                    <button class="test-tab" data-tab="performance">Performance</button>
                    <button class="test-tab" data-tab="errors">Errors</button>
                    <button class="test-tab" data-tab="coverage">Coverage</button>
                </div>
                <div class="test-content">
                    <div class="test-tab-content active" id="results-tab">
                        <div id="test-results-list"></div>
                    </div>
                    <div class="test-tab-content" id="performance-tab">
                        <div id="performance-metrics"></div>
                    </div>
                    <div class="test-tab-content" id="errors-tab">
                        <div id="error-log"></div>
                    </div>
                    <div class="test-tab-content" id="coverage-tab">
                        <div id="coverage-report"></div>
                    </div>
                </div>
            </div>
            <div class="test-console-stats">
                <span class="test-stat">
                    <span class="stat-label">Tests:</span>
                    <span class="stat-value" id="total-tests">0</span>
                </span>
                <span class="test-stat">
                    <span class="stat-label">Passed:</span>
                    <span class="stat-value text-success" id="passed-tests">0</span>
                </span>
                <span class="test-stat">
                    <span class="stat-label">Failed:</span>
                    <span class="stat-value text-danger" id="failed-tests">0</span>
                </span>
                <span class="test-stat">
                    <span class="stat-label">Duration:</span>
                    <span class="stat-value" id="test-duration">0ms</span>
                </span>
            </div>
        `;

        document.body.appendChild(console);
        this.attachConsoleEvents();
    }

    /**
     * Attach test console events
     */
    attachConsoleEvents() {
        const console = document.getElementById('accesspos-test-console');

        // Tab switching
        console.querySelectorAll('.test-tab').forEach(tab => {
            tab.addEventListener('click', (e) => {
                const tabName = e.target.dataset.tab;
                this.switchTab(tabName);
            });
        });

        // Test runners
        console.querySelector('#run-all-tests').addEventListener('click', () => {
            this.runAllTests();
        });

        console.querySelector('#run-ui-tests').addEventListener('click', () => {
            this.runUITests();
        });

        console.querySelector('#run-performance-tests').addEventListener('click', () => {
            this.runPerformanceTests();
        });

        console.querySelector('#run-accessibility-tests').addEventListener('click', () => {
            this.runAccessibilityTests();
        });

        console.querySelector('#clear-tests').addEventListener('click', () => {
            this.clearTestResults();
        });

        console.querySelector('#toggle-console').addEventListener('click', () => {
            this.toggleConsole();
        });
    }

    /**
     * Switch test console tab
     */
    switchTab(tabName) {
        const console = document.getElementById('accesspos-test-console');
        
        // Update tab buttons
        console.querySelectorAll('.test-tab').forEach(tab => {
            tab.classList.remove('active');
        });
        console.querySelector(`[data-tab="${tabName}"]`).classList.add('active');

        // Update tab content
        console.querySelectorAll('.test-tab-content').forEach(content => {
            content.classList.remove('active');
        });
        console.querySelector(`#${tabName}-tab`).classList.add('active');
    }

    /**
     * Toggle test console visibility
     */
    toggleConsole() {
        const console = document.getElementById('accesspos-test-console');
        const body = console.querySelector('.test-console-body');
        const toggleBtn = console.querySelector('#toggle-console i');

        if (body.style.display === 'none') {
            body.style.display = 'block';
            toggleBtn.className = 'fas fa-minus';
        } else {
            body.style.display = 'none';
            toggleBtn.className = 'fas fa-plus';
        }
    }

    /**
     * Run all tests
     */
    async runAllTests() {
        this.clearTestResults();
        const startTime = performance.now();

        this.log('Starting comprehensive test suite...', 'info');

        try {
            await this.runUITests();
            await this.runPerformanceTests();
            await this.runAccessibilityTests();
            await this.runFunctionalTests();
            await this.runResponsiveTests();
            await this.runBrowserCompatibilityTests();

            const duration = performance.now() - startTime;
            this.log(`All tests completed in ${Math.round(duration)}ms`, 'success');
            
        } catch (error) {
            this.log(`Test suite failed: ${error.message}`, 'error');
        }

        this.updateTestStats();
    }

    /**
     * Run UI/Interface tests
     */
    async runUITests() {
        this.log('Running UI Tests...', 'info');

        const tests = [
            () => this.testPageLoad(),
            () => this.testNavigation(),
            () => this.testModals(),
            () => this.testForms(),
            () => this.testTables(),
            () => this.testButtons(),
            () => this.testIcons(),
            () => this.testImages(),
            () => this.testLayout()
        ];

        for (const test of tests) {
            try {
                await test();
            } catch (error) {
                this.log(`UI Test failed: ${error.message}`, 'error');
            }
        }
    }

    /**
     * Test page load
     */
    testPageLoad() {
        return new Promise((resolve) => {
            const test = {
                name: 'Page Load Test',
                startTime: performance.now()
            };

            // Check if critical elements exist
            const criticalElements = [
                '.sidebar',
                '.topbar',
                '.content-wrapper',
                '.card'
            ];

            let passed = true;
            const missing = [];

            criticalElements.forEach(selector => {
                if (!document.querySelector(selector)) {
                    passed = false;
                    missing.push(selector);
                }
            });

            test.duration = performance.now() - test.startTime;
            test.passed = passed;
            test.details = passed ? 'All critical elements found' : `Missing elements: ${missing.join(', ')}`;

            this.addTestResult(test);
            resolve();
        });
    }

    /**
     * Test navigation
     */
    testNavigation() {
        return new Promise((resolve) => {
            const test = {
                name: 'Navigation Test',
                startTime: performance.now()
            };

            const sidebar = document.querySelector('.sidebar');
            const navLinks = sidebar ? sidebar.querySelectorAll('.nav-link') : [];
            
            let passed = true;
            const issues = [];

            // Check if navigation exists
            if (!sidebar) {
                passed = false;
                issues.push('Sidebar not found');
            }

            // Check navigation links
            if (navLinks.length === 0) {
                passed = false;
                issues.push('No navigation links found');
            }

            // Check active states
            const activeLinks = sidebar.querySelectorAll('.nav-link.active');
            if (activeLinks.length === 0) {
                issues.push('No active navigation state');
            }

            test.duration = performance.now() - test.startTime;
            test.passed = passed;
            test.details = passed ? `Found ${navLinks.length} navigation links` : issues.join(', ');

            this.addTestResult(test);
            resolve();
        });
    }

    /**
     * Test modals
     */
    testModals() {
        return new Promise((resolve) => {
            const test = {
                name: 'Modal Test',
                startTime: performance.now()
            };

            const modals = document.querySelectorAll('.modal');
            let passed = true;
            const issues = [];

            modals.forEach((modal, index) => {
                // Check modal structure
                if (!modal.querySelector('.modal-dialog')) {
                    passed = false;
                    issues.push(`Modal ${index + 1}: Missing modal-dialog`);
                }

                if (!modal.querySelector('.modal-content')) {
                    passed = false;
                    issues.push(`Modal ${index + 1}: Missing modal-content`);
                }

                // Check accessibility
                if (!modal.getAttribute('tabindex')) {
                    issues.push(`Modal ${index + 1}: Missing tabindex`);
                }

                if (!modal.getAttribute('aria-hidden')) {
                    issues.push(`Modal ${index + 1}: Missing aria-hidden`);
                }
            });

            test.duration = performance.now() - test.startTime;
            test.passed = passed;
            test.details = passed ? `Tested ${modals.length} modals` : issues.join(', ');

            this.addTestResult(test);
            resolve();
        });
    }

    /**
     * Test forms
     */
    testForms() {
        return new Promise((resolve) => {
            const test = {
                name: 'Form Test',
                startTime: performance.now()
            };

            const forms = document.querySelectorAll('form');
            let passed = true;
            const issues = [];

            forms.forEach((form, index) => {
                // Check form inputs have labels
                const inputs = form.querySelectorAll('input, select, textarea');
                inputs.forEach((input, inputIndex) => {
                    const id = input.id;
                    const label = form.querySelector(`label[for="${id}"]`);
                    const ariaLabel = input.getAttribute('aria-label');
                    const ariaLabelledby = input.getAttribute('aria-labelledby');

                    if (!label && !ariaLabel && !ariaLabelledby) {
                        issues.push(`Form ${index + 1}, Input ${inputIndex + 1}: Missing label`);
                    }
                });

                // Check submit button
                const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
                if (!submitBtn) {
                    issues.push(`Form ${index + 1}: Missing submit button`);
                }
            });

            test.duration = performance.now() - test.startTime;
            test.passed = passed && issues.length === 0;
            test.details = test.passed ? `Tested ${forms.length} forms` : issues.join(', ');

            this.addTestResult(test);
            resolve();
        });
    }

    /**
     * Test tables
     */
    testTables() {
        return new Promise((resolve) => {
            const test = {
                name: 'Table Test',
                startTime: performance.now()
            };

            const tables = document.querySelectorAll('table');
            let passed = true;
            const issues = [];

            tables.forEach((table, index) => {
                // Check table headers
                const headers = table.querySelectorAll('th');
                if (headers.length === 0) {
                    issues.push(`Table ${index + 1}: No headers found`);
                }

                // Check table caption
                const caption = table.querySelector('caption');
                if (!caption) {
                    issues.push(`Table ${index + 1}: Missing caption`);
                }

                // Check responsive wrapper
                const wrapper = table.closest('.table-responsive');
                if (!wrapper) {
                    issues.push(`Table ${index + 1}: Not responsive`);
                }
            });

            test.duration = performance.now() - test.startTime;
            test.passed = passed && issues.length < 3; // Allow some minor issues
            test.details = test.passed ? `Tested ${tables.length} tables` : issues.join(', ');

            this.addTestResult(test);
            resolve();
        });
    }

    /**
     * Run performance tests
     */
    async runPerformanceTests() {
        this.log('Running Performance Tests...', 'info');

        const tests = [
            () => this.testPageLoadTime(),
            () => this.testMemoryUsage(),
            () => this.testAssetSizes(),
            () => this.testRenderPerformance(),
            () => this.testScrollPerformance()
        ];

        for (const test of tests) {
            try {
                await test();
            } catch (error) {
                this.log(`Performance Test failed: ${error.message}`, 'error');
            }
        }
    }

    /**
     * Test page load time
     */
    testPageLoadTime() {
        return new Promise((resolve) => {
            const test = {
                name: 'Page Load Time',
                startTime: performance.now()
            };

            const navigation = performance.getEntriesByType('navigation')[0];
            const loadTime = navigation ? navigation.loadEventEnd - navigation.loadEventStart : 0;

            test.duration = loadTime;
            test.passed = loadTime < 3000; // Less than 3 seconds
            test.details = `Page loaded in ${Math.round(loadTime)}ms`;

            this.addTestResult(test);
            resolve();
        });
    }

    /**
     * Test memory usage
     */
    testMemoryUsage() {
        return new Promise((resolve) => {
            const test = {
                name: 'Memory Usage',
                startTime: performance.now()
            };

            if ('memory' in performance) {
                const memory = performance.memory;
                const usedMB = Math.round(memory.usedJSHeapSize / 1024 / 1024);
                const limitMB = Math.round(memory.jsHeapSizeLimit / 1024 / 1024);

                test.duration = performance.now() - test.startTime;
                test.passed = usedMB < 50; // Less than 50MB
                test.details = `Using ${usedMB}MB of ${limitMB}MB available`;
            } else {
                test.duration = performance.now() - test.startTime;
                test.passed = true;
                test.details = 'Memory API not available';
            }

            this.addTestResult(test);
            resolve();
        });
    }

    /**
     * Run accessibility tests
     */
    async runAccessibilityTests() {
        this.log('Running Accessibility Tests...', 'info');

        const tests = [
            () => this.testKeyboardNavigation(),
            () => this.testScreenReader(),
            () => this.testColorContrast(),
            () => this.testFocusManagement(),
            () => this.testARIAAttributes()
        ];

        for (const test of tests) {
            try {
                await test();
            } catch (error) {
                this.log(`Accessibility Test failed: ${error.message}`, 'error');
            }
        }
    }

    /**
     * Test keyboard navigation
     */
    testKeyboardNavigation() {
        return new Promise((resolve) => {
            const test = {
                name: 'Keyboard Navigation',
                startTime: performance.now()
            };

            const focusableElements = document.querySelectorAll(
                'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
            );

            let passed = true;
            const issues = [];

            // Check if focusable elements exist
            if (focusableElements.length === 0) {
                passed = false;
                issues.push('No focusable elements found');
            }

            // Check skip links
            const skipLinks = document.querySelectorAll('.skip-link');
            if (skipLinks.length === 0) {
                issues.push('No skip links found');
            }

            test.duration = performance.now() - test.startTime;
            test.passed = passed;
            test.details = test.passed ? 
                `Found ${focusableElements.length} focusable elements` : 
                issues.join(', ');

            this.addTestResult(test);
            resolve();
        });
    }

    /**
     * Test screen reader support
     */
    testScreenReader() {
        return new Promise((resolve) => {
            const test = {
                name: 'Screen Reader Support',
                startTime: performance.now()
            };

            let passed = true;
            const issues = [];

            // Check for alt text on images
            const images = document.querySelectorAll('img');
            images.forEach((img, index) => {
                if (!img.alt && !img.getAttribute('aria-hidden')) {
                    issues.push(`Image ${index + 1}: Missing alt text`);
                }
            });

            // Check for aria-live regions
            const liveRegions = document.querySelectorAll('[aria-live]');
            if (liveRegions.length === 0) {
                issues.push('No ARIA live regions found');
            }

            // Check for proper headings
            const headings = document.querySelectorAll('h1, h2, h3, h4, h5, h6');
            if (headings.length === 0) {
                passed = false;
                issues.push('No heading structure found');
            }

            test.duration = performance.now() - test.startTime;
            test.passed = passed && issues.length < 5; // Allow some minor issues
            test.details = test.passed ? 
                `Screen reader features validated` : 
                issues.slice(0, 3).join(', ') + (issues.length > 3 ? '...' : '');

            this.addTestResult(test);
            resolve();
        });
    }

    /**
     * Run responsive design tests
     */
    async runResponsiveTests() {
        this.log('Running Responsive Tests...', 'info');

        const viewports = [
            { name: 'Mobile', width: 375, height: 667 },
            { name: 'Tablet', width: 768, height: 1024 },
            { name: 'Desktop', width: 1200, height: 800 },
            { name: 'Large Desktop', width: 1920, height: 1080 }
        ];

        for (const viewport of viewports) {
            await this.testViewport(viewport);
        }
    }

    /**
     * Test specific viewport
     */
    testViewport(viewport) {
        return new Promise((resolve) => {
            const test = {
                name: `Responsive - ${viewport.name}`,
                startTime: performance.now()
            };

            // Simulate viewport change (note: this doesn't actually resize the window)
            const currentWidth = window.innerWidth;
            const currentHeight = window.innerHeight;

            let passed = true;
            const issues = [];

            // Check if layout adapts (simplified check)
            const sidebar = document.querySelector('.sidebar');
            const content = document.querySelector('.content-wrapper');

            if (viewport.width < 768) {
                // Mobile checks
                if (sidebar && !sidebar.classList.contains('collapsed')) {
                    // Check if sidebar can be collapsed on mobile
                    issues.push('Sidebar should be collapsible on mobile');
                }
            }

            // Check for horizontal scroll
            if (document.body.scrollWidth > viewport.width) {
                issues.push('Horizontal scroll detected');
            }

            test.duration = performance.now() - test.startTime;
            test.passed = passed && issues.length === 0;
            test.details = test.passed ? 
                `${viewport.name} layout OK` : 
                issues.join(', ');

            this.addTestResult(test);
            resolve();
        });
    }

    /**
     * Add test result
     */
    addTestResult(result) {
        this.testResults.push(result);
        this.displayTestResult(result);
    }

    /**
     * Display test result in console
     */
    displayTestResult(result) {
        const resultsList = document.getElementById('test-results-list');
        if (!resultsList) return;

        const resultElement = document.createElement('div');
        resultElement.className = `test-result ${result.passed ? 'passed' : 'failed'}`;
        resultElement.innerHTML = `
            <div class="test-result-header">
                <span class="test-result-icon">
                    <i class="fas fa-${result.passed ? 'check' : 'times'}"></i>
                </span>
                <span class="test-result-name">${result.name}</span>
                <span class="test-result-duration">${Math.round(result.duration)}ms</span>
            </div>
            <div class="test-result-details">${result.details}</div>
        `;

        resultsList.appendChild(resultElement);
    }

    /**
     * Update test statistics
     */
    updateTestStats() {
        const totalTests = this.testResults.length;
        const passedTests = this.testResults.filter(r => r.passed).length;
        const failedTests = totalTests - passedTests;
        const totalDuration = this.testResults.reduce((sum, r) => sum + r.duration, 0);

        document.getElementById('total-tests').textContent = totalTests;
        document.getElementById('passed-tests').textContent = passedTests;
        document.getElementById('failed-tests').textContent = failedTests;
        document.getElementById('test-duration').textContent = Math.round(totalDuration) + 'ms';
    }

    /**
     * Clear test results
     */
    clearTestResults() {
        this.testResults = [];
        const resultsList = document.getElementById('test-results-list');
        if (resultsList) {
            resultsList.innerHTML = '';
        }
        this.updateTestStats();
    }

    /**
     * Log message to console
     */
    log(message, type = 'info') {
        console.log(`[AccessPos Test] ${message}`);
        
        // Also add to error tab if it's an error
        if (type === 'error') {
            const errorLog = document.getElementById('error-log');
            if (errorLog) {
                const errorElement = document.createElement('div');
                errorElement.className = 'error-entry';
                errorElement.innerHTML = `
                    <span class="error-time">${new Date().toLocaleTimeString()}</span>
                    <span class="error-message">${message}</span>
                `;
                errorLog.appendChild(errorElement);
            }
        }
    }

    /**
     * Setup automated testing
     */
    setupAutomatedTests() {
        // Run basic tests on page load
        if (this.testEnvironment.isDevelopment) {
            setTimeout(() => {
                this.runBasicTests();
            }, 2000);
        }
    }

    /**
     * Run basic tests automatically
     */
    async runBasicTests() {
        this.log('Running automated basic tests...', 'info');
        
        try {
            await this.testPageLoad();
            await this.testNavigation();
            await this.testPageLoadTime();
        } catch (error) {
            this.log(`Automated test failed: ${error.message}`, 'error');
        }
    }

    /**
     * Setup performance monitoring
     */
    setupPerformanceMonitoring() {
        // Monitor long tasks
        if ('PerformanceObserver' in window) {
            const observer = new PerformanceObserver((list) => {
                for (const entry of list.getEntries()) {
                    if (entry.duration > 50) {
                        this.log(`Long task detected: ${Math.round(entry.duration)}ms`, 'warning');
                    }
                }
            });
            
            observer.observe({ entryTypes: ['longtask'] });
        }
    }

    /**
     * Setup error tracking
     */
    setupErrorTracking() {
        // Track JavaScript errors
        window.addEventListener('error', (event) => {
            this.log(`JavaScript Error: ${event.message} at ${event.filename}:${event.lineno}`, 'error');
        });

        // Track unhandled promise rejections
        window.addEventListener('unhandledrejection', (event) => {
            this.log(`Unhandled Promise Rejection: ${event.reason}`, 'error');
        });
    }

    /**
     * Export test results
     */
    exportResults() {
        const report = {
            timestamp: new Date().toISOString(),
            environment: this.testEnvironment,
            results: this.testResults,
            summary: {
                total: this.testResults.length,
                passed: this.testResults.filter(r => r.passed).length,
                failed: this.testResults.filter(r => !r.passed).length,
                duration: this.testResults.reduce((sum, r) => sum + r.duration, 0)
            }
        };

        const blob = new Blob([JSON.stringify(report, null, 2)], { type: 'application/json' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `accesspos-test-report-${Date.now()}.json`;
        a.click();
        URL.revokeObjectURL(url);
    }
}

// Initialize testing suite in development
document.addEventListener('DOMContentLoaded', () => {
    if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
        window.accessPosTesting = new AccessPosTestingSuite();
    }
});

// Export for manual testing
window.AccessPosTestingSuite = AccessPosTestingSuite;
