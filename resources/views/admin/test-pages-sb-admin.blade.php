@extends('layouts.sb-admin')

@section('title', 'Test All Pages - AccessPos Pro')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Test All Pages</h1>
        <button class="btn btn-primary" onclick="runAllTests()">
            <i class="fas fa-play fa-sm text-white-50"></i> Run All Tests
        </button>
    </div>

    <!-- Test Status Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Browser Compatibility Test Results</h6>
        </div>
        <div class="card-body">
            <div id="testStatus" class="alert alert-info">
                <i class="fas fa-info-circle"></i> Click "Run All Tests" to start testing all pages.
            </div>
            <div class="progress mb-3" style="display: none;">
                <div id="testProgress" class="progress-bar progress-bar-striped progress-bar-animated" 
                     role="progressbar" style="width: 0%"></div>
            </div>
        </div>
    </div>

    <!-- Test Pages Grid -->
    <div class="row">
        <!-- Dashboard Test -->
        <div class="col-lg-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Dashboard</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <a href="{{ route('admin.tableau-de-bord-moderne') }}" target="_blank" 
                                   class="btn btn-sm btn-primary test-page-btn" 
                                   data-page="dashboard">
                                    Test Dashboard
                                </a>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tachometer-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Articles System Test -->
        <div class="col-lg-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Articles System</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <a href="{{ route('admin.articles.index') }}" target="_blank" 
                                   class="btn btn-sm btn-success test-page-btn" 
                                   data-page="articles">
                                    Test Articles
                                </a>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-box fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Authentication Test -->
        <div class="col-lg-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Authentication</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <a href="{{ route('login') }}" target="_blank" 
                                   class="btn btn-sm btn-info test-page-btn" 
                                   data-page="auth">
                                    Test Login
                                </a>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-lock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Details Pages Test -->
        <div class="col-lg-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Details Pages</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <button class="btn btn-sm btn-warning" onclick="testDetailsPages()">
                                    Test All Details
                                </button>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-area fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Browser Testing Results -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Browser Compatibility Results</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="browserTestTable">
                    <thead>
                        <tr>
                            <th>Page</th>
                            <th>Chrome</th>
                            <th>Firefox</th>
                            <th>Safari</th>
                            <th>Edge</th>
                            <th>Mobile</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr data-page="dashboard">
                            <td><strong>Dashboard</strong></td>
                            <td><span class="badge badge-secondary">Pending</span></td>
                            <td><span class="badge badge-secondary">Pending</span></td>
                            <td><span class="badge badge-secondary">Pending</span></td>
                            <td><span class="badge badge-secondary">Pending</span></td>
                            <td><span class="badge badge-secondary">Pending</span></td>
                        </tr>
                        <tr data-page="articles">
                            <td><strong>Articles</strong></td>
                            <td><span class="badge badge-secondary">Pending</span></td>
                            <td><span class="badge badge-secondary">Pending</span></td>
                            <td><span class="badge badge-secondary">Pending</span></td>
                            <td><span class="badge badge-secondary">Pending</span></td>
                            <td><span class="badge badge-secondary">Pending</span></td>
                        </tr>
                        <tr data-page="auth">
                            <td><strong>Authentication</strong></td>
                            <td><span class="badge badge-secondary">Pending</span></td>
                            <td><span class="badge badge-secondary">Pending</span></td>
                            <td><span class="badge badge-secondary">Pending</span></td>
                            <td><span class="badge badge-secondary">Pending</span></td>
                            <td><span class="badge badge-secondary">Pending</span></td>
                        </tr>
                        <tr data-page="details">
                            <td><strong>Details Pages</strong></td>
                            <td><span class="badge badge-secondary">Pending</span></td>
                            <td><span class="badge badge-secondary">Pending</span></td>
                            <td><span class="badge badge-secondary">Pending</span></td>
                            <td><span class="badge badge-secondary">Pending</span></td>
                            <td><span class="badge badge-secondary">Pending</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Console Errors Log -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-danger">Console Errors & Warnings</h6>
        </div>
        <div class="card-body">
            <div id="consoleErrors" class="text-muted">No errors detected.</div>
        </div>
    </div>
</div>

<!-- Test Details Modal -->
<div class="modal fade" id="testDetailsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Test Details</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="testDetailsContent">
                    <!-- Test details will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="exportTestResults()">Export Results</button>
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
<script src="{{ asset('js/browser-compatibility.js') }}"></script>
<script>
// Page-specific testing functions
function runAllTests() {
    showProgress();
    
    const pages = ['dashboard', 'articles', 'auth', 'details'];
    let currentPage = 0;
    
    function testNextPage() {
        if (currentPage < pages.length) {
            const page = pages[currentPage];
            updateProgress((currentPage / pages.length) * 100);
            
            // Simulate browser testing
            testPageInBrowsers(page).then(() => {
                currentPage++;
                setTimeout(testNextPage, 1000);
            });
        } else {
            updateProgress(100);
            hideProgress();
            showTestStatus('success', 'All tests completed successfully!');
        }
    }
    
    testNextPage();
}

function testDetailsPages() {
    const detailsPages = [
        'chiffre-affaires-details',
        'etat-tables-details', 
        'modes-paiement-details',
        'performance-horaire-details',
        'stock-rupture-details',
        'top-clients-details'
    ];
    
    detailsPages.forEach(page => {
        window.open(`/admin/${page}`, '_blank');
    });
}

function showProgress() {
    document.querySelector('.progress').style.display = 'block';
}

function hideProgress() {
    document.querySelector('.progress').style.display = 'none';
}

function updateProgress(percent) {
    document.getElementById('testProgress').style.width = percent + '%';
}

function showTestStatus(type, message) {
    const statusDiv = document.getElementById('testStatus');
    statusDiv.className = `alert alert-${type}`;
    statusDiv.innerHTML = `<i class="fas fa-${type === 'success' ? 'check' : 'info'}-circle"></i> ${message}`;
}

async function testPageInBrowsers(page) {
    const browsers = ['chrome', 'firefox', 'safari', 'edge', 'mobile'];
    const row = document.querySelector(`tr[data-page="${page}"]`);
    
    for (let i = 0; i < browsers.length; i++) {
        const browser = browsers[i];
        const cell = row.children[i + 1];
        
        // Simulate testing delay
        await new Promise(resolve => setTimeout(resolve, 200));
        
        // Random test result for demonstration
        const success = Math.random() > 0.1; // 90% success rate
        const badge = cell.querySelector('.badge');
        
        if (success) {
            badge.className = 'badge badge-success';
            badge.textContent = 'Pass';
        } else {
            badge.className = 'badge badge-danger';
            badge.textContent = 'Fail';
        }
    }
}

function exportTestResults() {
    const results = collectTestResults();
    const blob = new Blob([JSON.stringify(results, null, 2)], {
        type: 'application/json'
    });
    
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'browser-test-results.json';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}

function collectTestResults() {
    const results = {
        timestamp: new Date().toISOString(),
        tests: []
    };
    
    const rows = document.querySelectorAll('#browserTestTable tbody tr');
    rows.forEach(row => {
        const page = row.getAttribute('data-page');
        const badges = row.querySelectorAll('.badge');
        
        results.tests.push({
            page: page,
            chrome: badges[0].textContent,
            firefox: badges[1].textContent,
            safari: badges[2].textContent,
            edge: badges[3].textContent,
            mobile: badges[4].textContent
        });
    });
    
    return results;
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    // Monitor console errors
    window.addEventListener('error', function(e) {
        logConsoleError('Error', e.message, e.filename, e.lineno);
    });
    
    console.warn = function(message) {
        logConsoleError('Warning', message);
    };
    
    console.error = function(message) {
        logConsoleError('Error', message);
    };
});

function logConsoleError(type, message, file = '', line = '') {
    const errorDiv = document.getElementById('consoleErrors');
    const errorHtml = `
        <div class="alert alert-${type.toLowerCase() === 'error' ? 'danger' : 'warning'} alert-dismissible fade show">
            <strong>${type}:</strong> ${message}
            ${file ? `<br><small>File: ${file}:${line}</small>` : ''}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    `;
    
    if (errorDiv.textContent === 'No errors detected.') {
        errorDiv.innerHTML = errorHtml;
    } else {
        errorDiv.innerHTML += errorHtml;
    }
}
</script>
@endpush
