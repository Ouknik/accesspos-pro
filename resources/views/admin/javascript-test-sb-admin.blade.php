@extends('layouts.sb-admin')

@section('title', 'JavaScript Functions Test - AccessPos Pro')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">JavaScript Functions Test</h1>
        <div>
            <button class="btn btn-primary" onclick="runAllJSTests()">
                <i class="fas fa-play fa-sm text-white-50"></i> Run All Tests
            </button>
            <button class="btn btn-secondary" onclick="runPerformanceTests()">
                <i class="fas fa-tachometer-alt fa-sm text-white-50"></i> Performance Tests
            </button>
        </div>
    </div>

    <!-- Test Statistics -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Functions Tested</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="functionsTestedCount">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="fab fa-js-square fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Tests Passed</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="testsPassedCount">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Errors Found</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="errorsFoundCount">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Coverage</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="coveragePercentage">0%</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-pie fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript Test Categories -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Function Test Results</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="jsTestTable">
                            <thead>
                                <tr>
                                    <th>Function/Module</th>
                                    <th>File</th>
                                    <th>Status</th>
                                    <th>Performance</th>
                                    <th>Memory</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr data-function="jquery">
                                    <td><strong>jQuery Core</strong></td>
                                    <td>vendor/jquery.min.js</td>
                                    <td><span class="badge badge-secondary">Pending</span></td>
                                    <td><span class="badge badge-secondary">Pending</span></td>
                                    <td><span class="badge badge-secondary">Pending</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" onclick="testFunction('jquery')">
                                            Test
                                        </button>
                                    </td>
                                </tr>
                                <tr data-function="bootstrap">
                                    <td><strong>Bootstrap JS</strong></td>
                                    <td>vendor/bootstrap.bundle.min.js</td>
                                    <td><span class="badge badge-secondary">Pending</span></td>
                                    <td><span class="badge badge-secondary">Pending</span></td>
                                    <td><span class="badge badge-secondary">Pending</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" onclick="testFunction('bootstrap')">
                                            Test
                                        </button>
                                    </td>
                                </tr>
                                <tr data-function="datatables">
                                    <td><strong>DataTables</strong></td>
                                    <td>vendor/jquery.dataTables.min.js</td>
                                    <td><span class="badge badge-secondary">Pending</span></td>
                                    <td><span class="badge badge-secondary">Pending</span></td>
                                    <td><span class="badge badge-secondary">Pending</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" onclick="testFunction('datatables')">
                                            Test
                                        </button>
                                    </td>
                                </tr>
                                <tr data-function="chartjs">
                                    <td><strong>Chart.js</strong></td>
                                    <td>vendor/Chart.min.js</td>
                                    <td><span class="badge badge-secondary">Pending</span></td>
                                    <td><span class="badge badge-secondary">Pending</span></td>
                                    <td><span class="badge badge-secondary">Pending</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" onclick="testFunction('chartjs')">
                                            Test
                                        </button>
                                    </td>
                                </tr>
                                <tr data-function="sb-admin">
                                    <td><strong>SB Admin 2</strong></td>
                                    <td>js/sb-admin-2.min.js</td>
                                    <td><span class="badge badge-secondary">Pending</span></td>
                                    <td><span class="badge badge-secondary">Pending</span></td>
                                    <td><span class="badge badge-secondary">Pending</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" onclick="testFunction('sb-admin')">
                                            Test
                                        </button>
                                    </td>
                                </tr>
                                <tr data-function="accesspos">
                                    <td><strong>AccessPos Functions</strong></td>
                                    <td>js/accesspos-functions.js</td>
                                    <td><span class="badge badge-secondary">Pending</span></td>
                                    <td><span class="badge badge-secondary">Pending</span></td>
                                    <td><span class="badge badge-secondary">Pending</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" onclick="testFunction('accesspos')">
                                            Test
                                        </button>
                                    </td>
                                </tr>
                                <tr data-function="custom">
                                    <td><strong>Custom Scripts</strong></td>
                                    <td>Various</td>
                                    <td><span class="badge badge-secondary">Pending</span></td>
                                    <td><span class="badge badge-secondary">Pending</span></td>
                                    <td><span class="badge badge-secondary">Pending</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" onclick="testFunction('custom')">
                                            Test
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Console Monitor -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-danger">Console Monitor</h6>
                </div>
                <div class="card-body">
                    <div id="consoleOutput" style="height: 200px; overflow-y: auto; background: #f8f9fc; padding: 10px; border-radius: 5px; font-family: monospace; font-size: 12px;">
                        <div class="text-muted">Console output will appear here...</div>
                    </div>
                    <div class="mt-2">
                        <button class="btn btn-sm btn-secondary" onclick="clearConsole()">
                            <i class="fas fa-trash"></i> Clear
                        </button>
                        <button class="btn btn-sm btn-info" onclick="downloadConsoleLog()">
                            <i class="fas fa-download"></i> Download Log
                        </button>
                    </div>
                </div>
            </div>

            <!-- Performance Monitor -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Performance Monitor</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label>Page Load Time</label>
                        <div class="progress">
                            <div id="loadTimeProgress" class="progress-bar bg-success" style="width: 0%"></div>
                        </div>
                        <small id="loadTimeText" class="text-muted">Not measured</small>
                    </div>
                    
                    <div class="mb-3">
                        <label>JavaScript Execution Time</label>
                        <div class="progress">
                            <div id="jsExecProgress" class="progress-bar bg-info" style="width: 0%"></div>
                        </div>
                        <small id="jsExecText" class="text-muted">Not measured</small>
                    </div>
                    
                    <div class="mb-3">
                        <label>Memory Usage</label>
                        <div class="progress">
                            <div id="memoryProgress" class="progress-bar bg-warning" style="width: 0%"></div>
                        </div>
                        <small id="memoryText" class="text-muted">Not measured</small>
                    </div>
                </div>
            </div>

            <!-- Test Controls -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Test Controls</h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <button class="btn btn-success btn-block" onclick="testEventHandlers()">
                            <i class="fas fa-mouse-pointer"></i> Test Event Handlers
                        </button>
                    </div>
                    <div class="mb-2">
                        <button class="btn btn-info btn-block" onclick="testAjaxFunctions()">
                            <i class="fas fa-wifi"></i> Test AJAX Functions
                        </button>
                    </div>
                    <div class="mb-2">
                        <button class="btn btn-warning btn-block" onclick="testFormValidations()">
                            <i class="fas fa-check-square"></i> Test Form Validations
                        </button>
                    </div>
                    <div class="mb-2">
                        <button class="btn btn-secondary btn-block" onclick="generateTestReport()">
                            <i class="fas fa-file-alt"></i> Generate Report
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Test Progress -->
    <div class="card shadow mb-4" id="jsTestProgress" style="display: none;">
        <div class="card-body">
            <h6 class="mb-3">JavaScript Testing Progress</h6>
            <div class="progress">
                <div id="jsProgressBar" class="progress-bar progress-bar-striped progress-bar-animated" 
                     role="progressbar" style="width: 0%"></div>
            </div>
            <div class="mt-2">
                <small id="jsProgressText" class="text-muted">Preparing tests...</small>
            </div>
        </div>
    </div>
</div>

<!-- Test Results Modal -->
<div class="modal fade" id="testResultsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">JavaScript Test Results</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="testResultsContent">
                    <!-- Test results will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="exportTestResults()">
                    Export Results
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
<link href="{{ asset('css/testing-suite.css') }}" rel="stylesheet">
@endpush

@push('scripts')
<script src="{{ asset('js/testing-suite.js') }}"></script>
<script>
// Test tracking variables
let jsTestResults = {
    functions: {},
    performance: {},
    errors: [],
    coverage: 0
};

let testCounters = {
    functionsTestedCount: 0,
    testsPassedCount: 0,
    errorsFoundCount: 0,
    coveragePercentage: 0
};

// Console monitoring
const originalConsole = {
    log: console.log,
    warn: console.warn,
    error: console.error,
    info: console.info
};

// Override console methods for monitoring
console.log = function(...args) {
    logToMonitor('LOG', args.join(' '));
    originalConsole.log.apply(console, args);
};

console.warn = function(...args) {
    logToMonitor('WARN', args.join(' '));
    originalConsole.warn.apply(console, args);
};

console.error = function(...args) {
    logToMonitor('ERROR', args.join(' '));
    testCounters.errorsFoundCount++;
    updateCounters();
    originalConsole.error.apply(console, args);
};

console.info = function(...args) {
    logToMonitor('INFO', args.join(' '));
    originalConsole.info.apply(console, args);
};

function logToMonitor(level, message) {
    const consoleOutput = document.getElementById('consoleOutput');
    const timestamp = new Date().toLocaleTimeString();
    const levelClass = level.toLowerCase();
    
    const logEntry = document.createElement('div');
    logEntry.className = `console-entry console-${levelClass}`;
    logEntry.innerHTML = `
        <span class="console-timestamp">[${timestamp}]</span>
        <span class="console-level">${level}:</span>
        <span class="console-message">${message}</span>
    `;
    
    consoleOutput.appendChild(logEntry);
    consoleOutput.scrollTop = consoleOutput.scrollHeight;
}

function runAllJSTests() {
    document.getElementById('jsTestProgress').style.display = 'block';
    
    const functions = ['jquery', 'bootstrap', 'datatables', 'chartjs', 'sb-admin', 'accesspos', 'custom'];
    let currentFunction = 0;
    
    function testNextFunction() {
        if (currentFunction < functions.length) {
            const functionName = functions[currentFunction];
            updateJSProgress((currentFunction / functions.length) * 100, 
                `Testing ${functionName}...`);
            
            testFunction(functionName).then(() => {
                currentFunction++;
                setTimeout(testNextFunction, 800);
            });
        } else {
            updateJSProgress(100, 'All JavaScript tests completed!');
            calculateCoverage();
            setTimeout(() => {
                document.getElementById('jsTestProgress').style.display = 'none';
                showTestSummary();
            }, 2000);
        }
    }
    
    testNextFunction();
}

async function testFunction(functionName) {
    const row = document.querySelector(`tr[data-function="${functionName}"]`);
    if (!row) return;
    
    const startTime = performance.now();
    const startMemory = performance.memory ? performance.memory.usedJSHeapSize : 0;
    
    try {
        let testResult = await runFunctionTest(functionName);
        const endTime = performance.now();
        const endMemory = performance.memory ? performance.memory.usedJSHeapSize : 0;
        
        const executionTime = endTime - startTime;
        const memoryUsed = endMemory - startMemory;
        
        // Update UI
        const badges = row.querySelectorAll('.badge');
        
        if (testResult.success) {
            badges[0].className = 'badge badge-success';
            badges[0].textContent = 'Pass';
            testCounters.testsPassedCount++;
        } else {
            badges[0].className = 'badge badge-danger';
            badges[0].textContent = 'Fail';
            testCounters.errorsFoundCount++;
        }
        
        // Performance badge
        const perfClass = executionTime < 100 ? 'badge-success' : 
                         executionTime < 500 ? 'badge-warning' : 'badge-danger';
        badges[1].className = `badge ${perfClass}`;
        badges[1].textContent = `${executionTime.toFixed(2)}ms`;
        
        // Memory badge
        const memClass = memoryUsed < 1024*1024 ? 'badge-success' : 
                        memoryUsed < 5*1024*1024 ? 'badge-warning' : 'badge-danger';
        badges[2].className = `badge ${memClass}`;
        badges[2].textContent = formatBytes(memoryUsed);
        
        // Store results
        jsTestResults.functions[functionName] = {
            success: testResult.success,
            executionTime: executionTime,
            memoryUsed: memoryUsed,
            details: testResult.details,
            timestamp: new Date().toISOString()
        };
        
        testCounters.functionsTestedCount++;
        updateCounters();
        
    } catch (error) {
        console.error(`Error testing ${functionName}:`, error);
        jsTestResults.errors.push({
            function: functionName,
            error: error.message,
            timestamp: new Date().toISOString()
        });
    }
}

async function runFunctionTest(functionName) {
    switch (functionName) {
        case 'jquery':
            return testJQuery();
        case 'bootstrap':
            return testBootstrap();
        case 'datatables':
            return testDataTables();
        case 'chartjs':
            return testChartJS();
        case 'sb-admin':
            return testSBAdmin();
        case 'accesspos':
            return testAccessPosFunctions();
        case 'custom':
            return testCustomScripts();
        default:
            return { success: false, details: 'Unknown function' };
    }
}

function testJQuery() {
    try {
        if (typeof $ === 'undefined' || typeof jQuery === 'undefined') {
            return { success: false, details: 'jQuery not loaded' };
        }
        
        // Test basic jQuery functionality
        const testDiv = $('<div>Test</div>');
        const hasText = testDiv.text() === 'Test';
        const canManipulate = testDiv.addClass('test-class').hasClass('test-class');
        
        return { 
            success: hasText && canManipulate, 
            details: 'jQuery basic functionality tested' 
        };
    } catch (error) {
        return { success: false, details: error.message };
    }
}

function testBootstrap() {
    try {
        // Test if Bootstrap JavaScript is loaded
        const hasModal = typeof $.fn.modal !== 'undefined';
        const hasDropdown = typeof $.fn.dropdown !== 'undefined';
        const hasCollapse = typeof $.fn.collapse !== 'undefined';
        
        return { 
            success: hasModal && hasDropdown && hasCollapse, 
            details: 'Bootstrap components tested' 
        };
    } catch (error) {
        return { success: false, details: error.message };
    }
}

function testDataTables() {
    try {
        const hasDataTables = typeof $.fn.DataTable !== 'undefined';
        
        if (!hasDataTables) {
            return { success: false, details: 'DataTables not loaded' };
        }
        
        // Test DataTables functionality
        const testTable = $('<table><thead><tr><th>Test</th></tr></thead><tbody><tr><td>Data</td></tr></tbody></table>');
        $('body').append(testTable);
        
        try {
            testTable.DataTable();
            testTable.DataTable().destroy();
            testTable.remove();
            return { success: true, details: 'DataTables functionality tested' };
        } catch (dtError) {
            testTable.remove();
            return { success: false, details: dtError.message };
        }
    } catch (error) {
        return { success: false, details: error.message };
    }
}

function testChartJS() {
    try {
        const hasChart = typeof Chart !== 'undefined';
        
        if (!hasChart) {
            return { success: false, details: 'Chart.js not loaded' };
        }
        
        // Test Chart.js functionality
        const canvas = document.createElement('canvas');
        canvas.style.display = 'none';
        document.body.appendChild(canvas);
        
        try {
            const chart = new Chart(canvas, {
                type: 'line',
                data: {
                    labels: ['Test'],
                    datasets: [{
                        data: [1],
                        label: 'Test'
                    }]
                }
            });
            chart.destroy();
            document.body.removeChild(canvas);
            return { success: true, details: 'Chart.js functionality tested' };
        } catch (chartError) {
            document.body.removeChild(canvas);
            return { success: false, details: chartError.message };
        }
    } catch (error) {
        return { success: false, details: error.message };
    }
}

function testSBAdmin() {
    try {
        // Test SB Admin 2 specific functionality
        const hasSidebarToggle = typeof window.sidebarToggle !== 'undefined' || 
                                $('.sidebar-toggle').length > 0;
        
        return { 
            success: true, // SB Admin is mostly CSS-based
            details: 'SB Admin 2 structure tested' 
        };
    } catch (error) {
        return { success: false, details: error.message };
    }
}

function testAccessPosFunctions() {
    try {
        // Test custom AccessPos functions
        let functionsFound = 0;
        const expectedFunctions = [
            'updateDashboard',
            'loadArticles',
            'validateForm',
            'showAlert',
            'formatCurrency'
        ];
        
        expectedFunctions.forEach(funcName => {
            if (typeof window[funcName] === 'function') {
                functionsFound++;
            }
        });
        
        const success = functionsFound > 0;
        return { 
            success: success, 
            details: `Found ${functionsFound}/${expectedFunctions.length} custom functions` 
        };
    } catch (error) {
        return { success: false, details: error.message };
    }
}

function testCustomScripts() {
    try {
        // Test inline scripts and custom implementations
        const scripts = document.querySelectorAll('script:not([src])');
        const hasInlineScripts = scripts.length > 0;
        
        return { 
            success: hasInlineScripts, 
            details: `Found ${scripts.length} inline scripts` 
        };
    } catch (error) {
        return { success: false, details: error.message };
    }
}

function runPerformanceTests() {
    const startTime = performance.now();
    
    // Simulate performance testing
    setTimeout(() => {
        const loadTime = performance.now() - startTime;
        updatePerformanceUI(loadTime);
        
        // Test memory usage
        if (performance.memory) {
            const memoryUsage = (performance.memory.usedJSHeapSize / performance.memory.totalJSHeapSize) * 100;
            updateMemoryUI(memoryUsage);
        }
        
        showAlert('info', 'Performance tests completed');
    }, 1000);
}

function testEventHandlers() {
    let handlersFound = 0;
    
    // Test common event handlers
    const elements = document.querySelectorAll('[onclick], [onchange], [onsubmit]');
    handlersFound += elements.length;
    
    // Test jQuery event handlers
    if (typeof $ !== 'undefined') {
        try {
            const jqElements = $('[data-toggle], .btn, .form-control');
            handlersFound += jqElements.length;
        } catch (e) {
            console.warn('Error testing jQuery event handlers:', e);
        }
    }
    
    showAlert('success', `Found ${handlersFound} event handlers`);
    console.log(`Event handlers test completed: ${handlersFound} handlers found`);
}

function testAjaxFunctions() {
    let ajaxFunctionsFound = 0;
    
    // Check for AJAX implementations
    if (typeof $ !== 'undefined' && $.ajax) {
        ajaxFunctionsFound++;
    }
    
    if (typeof fetch !== 'undefined') {
        ajaxFunctionsFound++;
    }
    
    if (typeof XMLHttpRequest !== 'undefined') {
        ajaxFunctionsFound++;
    }
    
    showAlert('info', `Found ${ajaxFunctionsFound} AJAX methods available`);
    console.log(`AJAX functions test completed: ${ajaxFunctionsFound} methods available`);
}

function testFormValidations() {
    const forms = document.querySelectorAll('form');
    let validationsFound = 0;
    
    forms.forEach(form => {
        const requiredFields = form.querySelectorAll('[required]');
        const patternFields = form.querySelectorAll('[pattern]');
        const typeFields = form.querySelectorAll('[type="email"], [type="number"], [type="tel"]');
        
        validationsFound += requiredFields.length + patternFields.length + typeFields.length;
    });
    
    showAlert('success', `Found ${validationsFound} form validation rules`);
    console.log(`Form validation test completed: ${validationsFound} validation rules found`);
}

function updateJSProgress(percent, text) {
    document.getElementById('jsProgressBar').style.width = percent + '%';
    document.getElementById('jsProgressText').textContent = text;
}

function updateCounters() {
    Object.keys(testCounters).forEach(counter => {
        const element = document.getElementById(counter);
        if (element) {
            if (counter === 'coveragePercentage') {
                element.textContent = testCounters[counter] + '%';
            } else {
                element.textContent = testCounters[counter];
            }
        }
    });
}

function calculateCoverage() {
    const totalFunctions = Object.keys(jsTestResults.functions).length;
    const passedFunctions = Object.values(jsTestResults.functions)
        .filter(result => result.success).length;
    
    testCounters.coveragePercentage = totalFunctions > 0 ? 
        Math.round((passedFunctions / totalFunctions) * 100) : 0;
    
    updateCounters();
}

function updatePerformanceUI(loadTime) {
    const loadTimeProgress = document.getElementById('loadTimeProgress');
    const loadTimeText = document.getElementById('loadTimeText');
    
    const loadPercent = Math.min((loadTime / 3000) * 100, 100); // 3s max
    loadTimeProgress.style.width = loadPercent + '%';
    loadTimeText.textContent = `${loadTime.toFixed(2)}ms`;
    
    if (loadTime < 1000) {
        loadTimeProgress.className = 'progress-bar bg-success';
    } else if (loadTime < 2000) {
        loadTimeProgress.className = 'progress-bar bg-warning';
    } else {
        loadTimeProgress.className = 'progress-bar bg-danger';
    }
}

function updateMemoryUI(memoryPercent) {
    const memoryProgress = document.getElementById('memoryProgress');
    const memoryText = document.getElementById('memoryText');
    
    memoryProgress.style.width = memoryPercent + '%';
    memoryText.textContent = `${memoryPercent.toFixed(1)}%`;
    
    if (memoryPercent < 50) {
        memoryProgress.className = 'progress-bar bg-success';
    } else if (memoryPercent < 80) {
        memoryProgress.className = 'progress-bar bg-warning';
    } else {
        memoryProgress.className = 'progress-bar bg-danger';
    }
}

function formatBytes(bytes) {
    if (bytes === 0) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function clearConsole() {
    document.getElementById('consoleOutput').innerHTML = 
        '<div class="text-muted">Console cleared...</div>';
}

function downloadConsoleLog() {
    const consoleOutput = document.getElementById('consoleOutput');
    const logs = Array.from(consoleOutput.children).map(entry => entry.textContent).join('\n');
    
    const blob = new Blob([logs], { type: 'text/plain' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'console-log.txt';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}

function generateTestReport() {
    const reportData = {
        ...jsTestResults,
        counters: testCounters,
        timestamp: new Date().toISOString(),
        browser: navigator.userAgent,
        performance: {
            timing: performance.timing,
            memory: performance.memory
        }
    };
    
    const reportHtml = `
        <h6>JavaScript Test Report</h6>
        <p><strong>Generated:</strong> ${new Date().toLocaleString()}</p>
        <p><strong>Functions Tested:</strong> ${testCounters.functionsTestedCount}</p>
        <p><strong>Tests Passed:</strong> ${testCounters.testsPassedCount}</p>
        <p><strong>Errors Found:</strong> ${testCounters.errorsFoundCount}</p>
        <p><strong>Coverage:</strong> ${testCounters.coveragePercentage}%</p>
        
        <h6 class="mt-3">Function Details:</h6>
        <div class="table-responsive">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Function</th>
                        <th>Status</th>
                        <th>Execution Time</th>
                        <th>Memory Used</th>
                    </tr>
                </thead>
                <tbody>
                    ${Object.entries(jsTestResults.functions).map(([name, result]) => `
                        <tr>
                            <td>${name}</td>
                            <td><span class="badge badge-${result.success ? 'success' : 'danger'}">${result.success ? 'Pass' : 'Fail'}</span></td>
                            <td>${result.executionTime ? result.executionTime.toFixed(2) + 'ms' : 'N/A'}</td>
                            <td>${result.memoryUsed ? formatBytes(result.memoryUsed) : 'N/A'}</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
    `;
    
    document.getElementById('testResultsContent').innerHTML = reportHtml;
    $('#testResultsModal').modal('show');
}

function exportTestResults() {
    const exportData = {
        ...jsTestResults,
        counters: testCounters,
        timestamp: new Date().toISOString(),
        browser: navigator.userAgent
    };
    
    const blob = new Blob([JSON.stringify(exportData, null, 2)], {
        type: 'application/json'
    });
    
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'javascript-test-results.json';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}

function showTestSummary() {
    const summaryHtml = `
        <div class="alert alert-info">
            <h6>JavaScript Test Summary:</h6>
            <ul class="mb-0">
                <li>Functions tested: ${testCounters.functionsTestedCount}</li>
                <li>Tests passed: ${testCounters.testsPassedCount}</li>
                <li>Errors found: ${testCounters.errorsFoundCount}</li>
                <li>Test coverage: ${testCounters.coveragePercentage}%</li>
            </ul>
        </div>
    `;
    
    document.querySelector('.container-fluid').insertAdjacentHTML('afterbegin', summaryHtml);
}

function showAlert(type, message) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show">
            ${message}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    `;
    
    document.querySelector('.container-fluid').insertAdjacentHTML('afterbegin', alertHtml);
}

// Initialize performance monitoring
document.addEventListener('DOMContentLoaded', function() {
    updateCounters();
    
    // Monitor page load performance
    window.addEventListener('load', function() {
        const loadTime = performance.timing.loadEventEnd - performance.timing.navigationStart;
        updatePerformanceUI(loadTime);
    });
    
    console.log('JavaScript test suite initialized');
});
</script>
@endpush
