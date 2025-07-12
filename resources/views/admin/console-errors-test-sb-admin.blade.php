@extends('layouts.sb-admin')

@section('title', 'Console Errors & Warnings Test - AccessPos Pro')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Console Errors & Warnings Test</h1>
        <div>
            <button class="btn btn-primary" onclick="startMonitoring()">
                <i class="fas fa-play fa-sm text-white-50"></i> Start Monitoring
            </button>
            <button class="btn btn-warning" onclick="runErrorTests()">
                <i class="fas fa-bug fa-sm text-white-50"></i> Run Error Tests
            </button>
            <button class="btn btn-secondary" onclick="generateErrorReport()">
                <i class="fas fa-file-alt fa-sm text-white-50"></i> Generate Report
            </button>
        </div>
    </div>

    <!-- Error Statistics -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Errors</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="errorsCount">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-circle fa-2x text-gray-300"></i>
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
                                Warnings</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="warningsCount">0</div>
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
                                Info Messages</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="infoCount">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-info-circle fa-2x text-gray-300"></i>
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
                                Debug Messages</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="debugCount">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-bug fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Console Monitor -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Live Console Monitor</h6>
                    <div class="dropdown no-arrow">
                        <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" 
                                data-toggle="dropdown">
                            Filter <i class="fas fa-caret-down"></i>
                        </button>
                        <div class="dropdown-menu">
                            <label class="dropdown-item">
                                <input type="checkbox" id="showErrors" checked> Errors
                            </label>
                            <label class="dropdown-item">
                                <input type="checkbox" id="showWarnings" checked> Warnings
                            </label>
                            <label class="dropdown-item">
                                <input type="checkbox" id="showInfo" checked> Info
                            </label>
                            <label class="dropdown-item">
                                <input type="checkbox" id="showDebug" checked> Debug
                            </label>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="consoleMonitor" style="height: 400px; overflow-y: auto; background: #1a1a1a; color: #00ff00; padding: 15px; border-radius: 5px; font-family: 'Courier New', monospace; font-size: 13px;">
                        <div class="console-welcome">
                            <span style="color: #00ff00;">[SYSTEM]</span> Console monitoring initialized...<br>
                            <span style="color: #00ff00;">[SYSTEM]</span> Ready to capture errors and warnings.<br>
                            <span style="color: #888;">Click "Start Monitoring" to begin.</span>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button class="btn btn-sm btn-danger" onclick="clearConsoleMonitor()">
                            <i class="fas fa-trash"></i> Clear
                        </button>
                        <button class="btn btn-sm btn-info" onclick="downloadConsoleLog()">
                            <i class="fas fa-download"></i> Download Log
                        </button>
                        <button class="btn btn-sm btn-success" onclick="exportFilteredLog()">
                            <i class="fas fa-filter"></i> Export Filtered
                        </button>
                        <span class="ml-3">
                            <small class="text-muted">Status: <span id="monitoringStatus">Stopped</span></small>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Error Analysis -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-danger">Error Analysis</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6>Most Common Errors</h6>
                        <div id="commonErrors">
                            <small class="text-muted">No errors detected yet.</small>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <h6>Error Sources</h6>
                        <canvas id="errorSourceChart" width="300" height="200"></canvas>
                    </div>
                    
                    <div class="mb-3">
                        <h6>Error Timeline</h6>
                        <div id="errorTimeline">
                            <small class="text-muted">No timeline data yet.</small>
                        </div>
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
                        <button class="btn btn-outline-danger btn-sm btn-block" onclick="simulateError()">
                            <i class="fas fa-bug"></i> Simulate Error
                        </button>
                    </div>
                    <div class="mb-2">
                        <button class="btn btn-outline-warning btn-sm btn-block" onclick="simulateWarning()">
                            <i class="fas fa-exclamation-triangle"></i> Simulate Warning
                        </button>
                    </div>
                    <div class="mb-2">
                        <button class="btn btn-outline-info btn-sm btn-block" onclick="testNetworkErrors()">
                            <i class="fas fa-wifi"></i> Test Network Errors
                        </button>
                    </div>
                    <div class="mb-2">
                        <button class="btn btn-outline-secondary btn-sm btn-block" onclick="testResourceErrors()">
                            <i class="fas fa-file-alt"></i> Test Resource Errors
                        </button>
                    </div>
                    <hr>
                    <div class="mb-2">
                        <button class="btn btn-success btn-sm btn-block" onclick="runHealthCheck()">
                            <i class="fas fa-heart"></i> Health Check
                        </button>
                    </div>
                </div>
            </div>

            <!-- Auto-Detection Settings -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Auto-Detection</h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="autoDetectErrors" checked>
                            <label class="custom-control-label" for="autoDetectErrors">
                                Auto-detect JavaScript Errors
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="autoDetectNetwork" checked>
                            <label class="custom-control-label" for="autoDetectNetwork">
                                Auto-detect Network Errors
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="autoDetectResources" checked>
                            <label class="custom-control-label" for="autoDetectResources">
                                Auto-detect Resource Errors
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="autoDetectDeprecated">
                            <label class="custom-control-label" for="autoDetectDeprecated">
                                Auto-detect Deprecated APIs
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Details Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-warning">Error Details</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="errorDetailsTable">
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>Type</th>
                            <th>Message</th>
                            <th>Source</th>
                            <th>Line</th>
                            <th>Stack</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="7" class="text-center text-muted">No errors recorded yet.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Error Details Modal -->
<div class="modal fade" id="errorDetailsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Error Details</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="errorModalContent">
                    <!-- Error details will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="reportError()">
                    Report Issue
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
<link href="{{ asset('css/testing-suite.css') }}" rel="stylesheet">
<style>
.console-entry {
    margin-bottom: 5px;
    word-wrap: break-word;
}

.console-error { color: #ff6b6b; }
.console-warning { color: #feca57; }
.console-info { color: #54a0ff; }
.console-debug { color: #5f27cd; }
.console-log { color: #00ff00; }

.console-timestamp {
    color: #888;
    font-size: 11px;
}

.console-source {
    color: #888;
    font-size: 11px;
}

.error-badge {
    font-size: 10px;
}
</style>
@endpush

@push('scripts')
<script src="{{ asset('js/testing-suite.js') }}"></script>
<script>
// Console monitoring variables
let isMonitoring = false;
let errorLog = [];
let consoleLog = [];
let errorStats = {
    errors: 0,
    warnings: 0,
    info: 0,
    debug: 0
};

// Original console methods
const originalConsole = {
    error: console.error,
    warn: console.warn,
    info: console.info,
    log: console.log,
    debug: console.debug
};

// Error tracking
let errorChart = null;

function startMonitoring() {
    if (isMonitoring) {
        stopMonitoring();
        return;
    }
    
    isMonitoring = true;
    document.getElementById('monitoringStatus').textContent = 'Running';
    document.querySelector('[onclick="startMonitoring()"]').innerHTML = 
        '<i class="fas fa-stop fa-sm text-white-50"></i> Stop Monitoring';
    
    setupConsoleInterception();
    setupErrorHandlers();
    
    logToMonitor('SYSTEM', 'Monitoring started...', 'success');
}

function stopMonitoring() {
    isMonitoring = false;
    document.getElementById('monitoringStatus').textContent = 'Stopped';
    document.querySelector('[onclick="startMonitoring()"]').innerHTML = 
        '<i class="fas fa-play fa-sm text-white-50"></i> Start Monitoring';
    
    restoreConsole();
    removeErrorHandlers();
    
    logToMonitor('SYSTEM', 'Monitoring stopped.', 'info');
}

function setupConsoleInterception() {
    console.error = function(...args) {
        if (isMonitoring) {
            const message = args.join(' ');
            logError('ERROR', message, 'Console Error');
            logToMonitor('ERROR', message, 'error');
            errorStats.errors++;
            updateErrorStats();
        }
        originalConsole.error.apply(console, args);
    };
    
    console.warn = function(...args) {
        if (isMonitoring) {
            const message = args.join(' ');
            logError('WARNING', message, 'Console Warning');
            logToMonitor('WARN', message, 'warning');
            errorStats.warnings++;
            updateErrorStats();
        }
        originalConsole.warn.apply(console, args);
    };
    
    console.info = function(...args) {
        if (isMonitoring) {
            const message = args.join(' ');
            logToMonitor('INFO', message, 'info');
            errorStats.info++;
            updateErrorStats();
        }
        originalConsole.info.apply(console, args);
    };
    
    console.log = function(...args) {
        if (isMonitoring) {
            const message = args.join(' ');
            logToMonitor('LOG', message, 'log');
        }
        originalConsole.log.apply(console, args);
    };
    
    console.debug = function(...args) {
        if (isMonitoring) {
            const message = args.join(' ');
            logToMonitor('DEBUG', message, 'debug');
            errorStats.debug++;
            updateErrorStats();
        }
        originalConsole.debug.apply(console, args);
    };
}

function setupErrorHandlers() {
    // JavaScript errors
    window.addEventListener('error', function(event) {
        if (isMonitoring && document.getElementById('autoDetectErrors').checked) {
            const error = {
                type: 'JavaScript Error',
                message: event.message,
                source: event.filename,
                line: event.lineno,
                column: event.colno,
                stack: event.error ? event.error.stack : 'No stack trace available'
            };
            
            logError('ERROR', error.message, 'JavaScript Error', error);
            logToMonitor('JS-ERROR', `${error.message} at ${error.source}:${error.line}`, 'error');
            errorStats.errors++;
            updateErrorStats();
        }
    });
    
    // Promise rejections
    window.addEventListener('unhandledrejection', function(event) {
        if (isMonitoring) {
            const error = {
                type: 'Unhandled Promise Rejection',
                message: event.reason ? event.reason.toString() : 'Unknown promise rejection',
                source: 'Promise',
                line: 'N/A',
                column: 'N/A',
                stack: event.reason && event.reason.stack ? event.reason.stack : 'No stack trace available'
            };
            
            logError('ERROR', error.message, 'Promise Rejection', error);
            logToMonitor('PROMISE-ERROR', error.message, 'error');
            errorStats.errors++;
            updateErrorStats();
        }
    });
    
    // Resource loading errors
    window.addEventListener('error', function(event) {
        if (isMonitoring && document.getElementById('autoDetectResources').checked && event.target !== window) {
            const error = {
                type: 'Resource Loading Error',
                message: `Failed to load resource: ${event.target.src || event.target.href}`,
                source: event.target.tagName,
                line: 'N/A',
                column: 'N/A',
                stack: 'Resource loading error'
            };
            
            logError('ERROR', error.message, 'Resource Error', error);
            logToMonitor('RESOURCE-ERROR', error.message, 'error');
            errorStats.errors++;
            updateErrorStats();
        }
    }, true);
}

function removeErrorHandlers() {
    // Error handlers are automatically removed when monitoring stops
}

function restoreConsole() {
    console.error = originalConsole.error;
    console.warn = originalConsole.warn;
    console.info = originalConsole.info;
    console.log = originalConsole.log;
    console.debug = originalConsole.debug;
}

function logToMonitor(level, message, type) {
    const monitor = document.getElementById('consoleMonitor');
    const timestamp = new Date().toLocaleTimeString();
    
    // Check filter settings
    const showErrors = document.getElementById('showErrors').checked;
    const showWarnings = document.getElementById('showWarnings').checked;
    const showInfo = document.getElementById('showInfo').checked;
    const showDebug = document.getElementById('showDebug').checked;
    
    let shouldShow = true;
    if (type === 'error' && !showErrors) shouldShow = false;
    if (type === 'warning' && !showWarnings) shouldShow = false;
    if (type === 'info' && !showInfo) shouldShow = false;
    if (type === 'debug' && !showDebug) shouldShow = false;
    
    if (!shouldShow) return;
    
    const logEntry = document.createElement('div');
    logEntry.className = `console-entry console-${type}`;
    logEntry.innerHTML = `
        <span class="console-timestamp">[${timestamp}]</span>
        <span style="font-weight: bold;">[${level}]</span>
        ${message}
    `;
    
    monitor.appendChild(logEntry);
    monitor.scrollTop = monitor.scrollHeight;
    
    // Store in log
    consoleLog.push({
        timestamp: timestamp,
        level: level,
        message: message,
        type: type
    });
    
    // Limit log size
    if (consoleLog.length > 1000) {
        consoleLog.shift();
    }
}

function logError(type, message, source, details = null) {
    const error = {
        id: Date.now(),
        timestamp: new Date().toISOString(),
        type: type,
        message: message,
        source: source,
        details: details,
        line: details ? details.line : 'N/A',
        column: details ? details.column : 'N/A',
        stack: details ? details.stack : 'No stack trace'
    };
    
    errorLog.push(error);
    addErrorToTable(error);
    updateErrorAnalysis();
}

function addErrorToTable(error) {
    const table = document.getElementById('errorDetailsTable').getElementsByTagName('tbody')[0];
    
    // Remove "no errors" message if it exists
    if (table.rows[0] && table.rows[0].cells[0].colSpan === 7) {
        table.deleteRow(0);
    }
    
    const row = table.insertRow(0);
    row.innerHTML = `
        <td>${new Date(error.timestamp).toLocaleTimeString()}</td>
        <td><span class="badge badge-${error.type === 'ERROR' ? 'danger' : 'warning'} error-badge">${error.type}</span></td>
        <td title="${error.message}">${error.message.length > 50 ? error.message.substring(0, 50) + '...' : error.message}</td>
        <td>${error.source}</td>
        <td>${error.line}</td>
        <td title="${error.stack}">${error.stack && error.stack.length > 30 ? error.stack.substring(0, 30) + '...' : (error.stack || 'N/A')}</td>
        <td>
            <button class="btn btn-sm btn-primary" onclick="showErrorDetails(${error.id})">
                <i class="fas fa-eye"></i>
            </button>
        </td>
    `;
}

function showErrorDetails(errorId) {
    const error = errorLog.find(e => e.id === errorId);
    if (!error) return;
    
    const content = `
        <div class="row">
            <div class="col-md-6">
                <h6>Error Information</h6>
                <table class="table table-sm">
                    <tr><td><strong>Type:</strong></td><td>${error.type}</td></tr>
                    <tr><td><strong>Time:</strong></td><td>${new Date(error.timestamp).toLocaleString()}</td></tr>
                    <tr><td><strong>Source:</strong></td><td>${error.source}</td></tr>
                    <tr><td><strong>Line:</strong></td><td>${error.line}</td></tr>
                    <tr><td><strong>Column:</strong></td><td>${error.column}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6>Message</h6>
                <div class="alert alert-danger">
                    ${error.message}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <h6>Stack Trace</h6>
                <pre style="background: #f8f9fc; padding: 10px; border-radius: 5px; font-size: 12px; max-height: 200px; overflow-y: auto;">${error.stack}</pre>
            </div>
        </div>
    `;
    
    document.getElementById('errorModalContent').innerHTML = content;
    $('#errorDetailsModal').modal('show');
}

function updateErrorStats() {
    document.getElementById('errorsCount').textContent = errorStats.errors;
    document.getElementById('warningsCount').textContent = errorStats.warnings;
    document.getElementById('infoCount').textContent = errorStats.info;
    document.getElementById('debugCount').textContent = errorStats.debug;
}

function updateErrorAnalysis() {
    // Update common errors
    const errorCounts = {};
    errorLog.forEach(error => {
        const key = error.message.substring(0, 50);
        errorCounts[key] = (errorCounts[key] || 0) + 1;
    });
    
    const sortedErrors = Object.entries(errorCounts)
        .sort(([,a], [,b]) => b - a)
        .slice(0, 3);
    
    const commonErrorsHtml = sortedErrors.map(([message, count]) => 
        `<div class="mb-1">
            <small><strong>${count}x:</strong> ${message}${message.length === 50 ? '...' : ''}</small>
        </div>`
    ).join('');
    
    document.getElementById('commonErrors').innerHTML = 
        commonErrorsHtml || '<small class="text-muted">No errors detected yet.</small>';
    
    // Update error sources chart
    updateErrorChart();
}

function updateErrorChart() {
    const ctx = document.getElementById('errorSourceChart').getContext('2d');
    
    if (errorChart) {
        errorChart.destroy();
    }
    
    const sourceCounts = {};
    errorLog.forEach(error => {
        sourceCounts[error.source] = (sourceCounts[error.source] || 0) + 1;
    });
    
    const labels = Object.keys(sourceCounts);
    const data = Object.values(sourceCounts);
    
    if (labels.length === 0) {
        return;
    }
    
    errorChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: [
                    '#e74c3c',
                    '#f39c12',
                    '#3498db',
                    '#2ecc71',
                    '#9b59b6'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: {
                position: 'bottom',
                labels: {
                    fontSize: 10
                }
            }
        }
    });
}

function clearConsoleMonitor() {
    document.getElementById('consoleMonitor').innerHTML = 
        '<div class="console-welcome"><span style="color: #00ff00;">[SYSTEM]</span> Console cleared.</div>';
    consoleLog = [];
}

function downloadConsoleLog() {
    const logText = consoleLog.map(entry => 
        `[${entry.timestamp}] [${entry.level}] ${entry.message}`
    ).join('\n');
    
    const blob = new Blob([logText], { type: 'text/plain' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `console-log-${new Date().toISOString().split('T')[0]}.txt`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}

function exportFilteredLog() {
    const showErrors = document.getElementById('showErrors').checked;
    const showWarnings = document.getElementById('showWarnings').checked;
    const showInfo = document.getElementById('showInfo').checked;
    const showDebug = document.getElementById('showDebug').checked;
    
    const filteredLog = consoleLog.filter(entry => {
        if (entry.type === 'error' && !showErrors) return false;
        if (entry.type === 'warning' && !showWarnings) return false;
        if (entry.type === 'info' && !showInfo) return false;
        if (entry.type === 'debug' && !showDebug) return false;
        return true;
    });
    
    const logText = filteredLog.map(entry => 
        `[${entry.timestamp}] [${entry.level}] ${entry.message}`
    ).join('\n');
    
    const blob = new Blob([logText], { type: 'text/plain' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `filtered-console-log-${new Date().toISOString().split('T')[0]}.txt`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}

function runErrorTests() {
    logToMonitor('SYSTEM', 'Running error tests...', 'info');
    
    setTimeout(() => simulateError(), 500);
    setTimeout(() => simulateWarning(), 1000);
    setTimeout(() => testNetworkErrors(), 1500);
    setTimeout(() => testResourceErrors(), 2000);
}

function simulateError() {
    try {
        throw new Error('This is a simulated error for testing purposes');
    } catch (e) {
        console.error('Simulated Error:', e.message);
    }
}

function simulateWarning() {
    console.warn('This is a simulated warning for testing purposes');
}

function testNetworkErrors() {
    if (document.getElementById('autoDetectNetwork').checked) {
        // Simulate network error
        fetch('/non-existent-endpoint')
            .catch(error => {
                console.error('Network Error (Simulated):', error.message);
            });
    }
}

function testResourceErrors() {
    if (document.getElementById('autoDetectResources').checked) {
        // Create and try to load a non-existent image
        const img = document.createElement('img');
        img.src = '/non-existent-image.jpg';
        img.style.display = 'none';
        document.body.appendChild(img);
        
        setTimeout(() => {
            document.body.removeChild(img);
        }, 1000);
    }
}

function runHealthCheck() {
    logToMonitor('SYSTEM', 'Running health check...', 'info');
    
    const checks = [
        { name: 'jQuery', test: () => typeof $ !== 'undefined' },
        { name: 'Bootstrap', test: () => typeof $.fn.modal !== 'undefined' },
        { name: 'DataTables', test: () => typeof $.fn.DataTable !== 'undefined' },
        { name: 'Chart.js', test: () => typeof Chart !== 'undefined' },
        { name: 'Local Storage', test: () => typeof Storage !== 'undefined' },
        { name: 'Session Storage', test: () => typeof sessionStorage !== 'undefined' },
        { name: 'Fetch API', test: () => typeof fetch !== 'undefined' },
        { name: 'Promise Support', test: () => typeof Promise !== 'undefined' }
    ];
    
    let passed = 0;
    let failed = 0;
    
    checks.forEach(check => {
        try {
            if (check.test()) {
                logToMonitor('HEALTH', `✓ ${check.name} - OK`, 'success');
                passed++;
            } else {
                logToMonitor('HEALTH', `✗ ${check.name} - FAILED`, 'error');
                failed++;
            }
        } catch (error) {
            logToMonitor('HEALTH', `✗ ${check.name} - ERROR: ${error.message}`, 'error');
            failed++;
        }
    });
    
    logToMonitor('SYSTEM', `Health check completed: ${passed} passed, ${failed} failed`, 
        failed === 0 ? 'success' : 'warning');
}

function generateErrorReport() {
    const report = {
        timestamp: new Date().toISOString(),
        statistics: errorStats,
        errors: errorLog,
        consoleLog: consoleLog.slice(-50), // Last 50 entries
        healthCheck: {
            timestamp: new Date().toISOString()
        }
    };
    
    const blob = new Blob([JSON.stringify(report, null, 2)], {
        type: 'application/json'
    });
    
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `error-report-${new Date().toISOString().split('T')[0]}.json`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
    
    showAlert('success', 'Error report generated and downloaded successfully!');
}

function reportError() {
    showAlert('info', 'Error reporting feature would integrate with your issue tracking system.');
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

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    updateErrorStats();
    
    // Add filter change listeners
    ['showErrors', 'showWarnings', 'showInfo', 'showDebug'].forEach(id => {
        document.getElementById(id).addEventListener('change', function() {
            // Refresh the monitor display based on filters
            // This would typically re-render the console entries
        });
    });
    
    console.log('Console error monitoring system initialized');
});

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    if (isMonitoring) {
        stopMonitoring();
    }
});
</script>
@endpush
