<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="AccessPos Pro - Système de gestion de point de vente">
    <meta name="author" content="AccessPos Team">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'AccessPos Pro') - Administration</title>

    <!-- Custom fonts for this template-->
    <link href="{{ asset('startbootstrap-sb-admin-2-gh-pages/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{ asset('startbootstrap-sb-admin-2-gh-pages/css/sb-admin-2.min.css') }}" rel="stylesheet">

    <!-- DataTables CSS -->
    <link href="{{ asset('startbootstrap-sb-admin-2-gh-pages/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">

    <!-- AccessPos Custom Styles -->
    <style>
        :root {
            --accesspos-primary: #4e73df;
            --accesspos-primary-dark: #3d5bd0;
            --accesspos-secondary: #858796;
            --accesspos-success: #1cc88a;
            --accesspos-info: #36b9cc;
            --accesspos-warning: #f6c23e;
            --accesspos-danger: #e74a3b;
            --accesspos-light: #f8f9fc;
            --accesspos-dark: #5a5c69;
        }
        
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .sidebar .nav-item .nav-link[data-toggle="collapse"]::after {
            color: #d1d3e2;
        }
        
        .sidebar-brand {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .card-header {
            background: linear-gradient(135deg, #f8f9fc 0%, #e2e6ea 100%);
            border-bottom: 1px solid #e3e6f0;
            font-weight: 600;
        }
        
        .btn-primary {
            background: var(--accesspos-primary);
            border-color: var(--accesspos-primary);
        }
        
        .btn-primary:hover {
            background: var(--accesspos-primary-dark);
            border-color: var(--accesspos-primary-dark);
        }
        
        .text-primary {
            color: var(--accesspos-primary) !important;
        }
        
        .border-left-primary {
            border-left: 0.25rem solid var(--accesspos-primary) !important;
        }
        
        .progress-bar {
            background-color: var(--accesspos-primary);
        }
        
        .custom-file-input:focus ~ .custom-file-label {
            border-color: var(--accesspos-primary);
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }
        
        .accesspos-card {
            transition: transform 0.2s ease-in-out;
        }
        
        .accesspos-card:hover {
            transform: translateY(-5px);
        }
        
        .sidebar .nav-item.active .nav-link {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 0.35rem;
        }
        
        .topbar .navbar-search .form-control {
            border: 1px solid #d1d3e2;
            border-radius: 10rem;
        }
        
        .dropdown-list {
            max-height: 300px;
            overflow-y: auto;
        }
        
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: var(--accesspos-danger);
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }
        
        .stats-card {
            border-radius: 15px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
        
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
        }
        
        .spinner-border-xl {
            width: 3rem;
            height: 3rem;
        }
    </style>

    @stack('styles')
</head>

<body id="page-top">
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="text-center">
            <div class="spinner-border spinner-border-xl text-primary" role="status">
                <span class="sr-only">Chargement...</span>
            </div>
            <div class="mt-2">Chargement en cours...</div>
        </div>
    </div>

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        @include('layouts.partials.sb-admin-sidebar')
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                @include('layouts.partials.sb-admin-topbar')
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    
                    <!-- Breadcrumbs -->
                    @include('layouts.partials.sb-admin-breadcrumbs')
                    <!-- End of Breadcrumbs -->
                    
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('warning'))
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            {{ session('warning') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('info'))
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <i class="fas fa-info-circle me-2"></i>
                            {{ session('info') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @yield('content')
                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            @include('layouts.partials.sb-admin-footer')
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    @include('layouts.partials.sb-admin-logout-modal')

    <!-- Bootstrap core JavaScript-->
    <script src="{{ asset('startbootstrap-sb-admin-2-gh-pages/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('startbootstrap-sb-admin-2-gh-pages/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Core plugin JavaScript-->
    <script src="{{ asset('startbootstrap-sb-admin-2-gh-pages/vendor/jquery-easing/jquery.easing.min.js') }}"></script>

    <!-- Custom scripts for all pages-->
    <script src="{{ asset('startbootstrap-sb-admin-2-gh-pages/js/sb-admin-2.min.js') }}"></script>

    <!-- DataTables JavaScript-->
    <script src="{{ asset('startbootstrap-sb-admin-2-gh-pages/vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('startbootstrap-sb-admin-2-gh-pages/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>

    <!-- Chart.js -->
    <script src="{{ asset('startbootstrap-sb-admin-2-gh-pages/vendor/chart.js/Chart.min.js') }}"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- AccessPos Core JavaScript -->
    <script>
        // إعدادات عامة للـ CSRF
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        // نظام التنبيهات الموحد لـ AccessPos
        window.AccessPosAlert = {
            success: function(message, title = 'Succès') {
                Swal.fire({
                    icon: 'success',
                    title: title,
                    text: message,
                    confirmButtonColor: '#1cc88a',
                    timer: 3000,
                    showConfirmButton: false
                });
            },
            error: function(message, title = 'Erreur') {
                Swal.fire({
                    icon: 'error',
                    title: title,
                    text: message,
                    confirmButtonColor: '#e74a3b'
                });
            },
            warning: function(message, title = 'Attention') {
                Swal.fire({
                    icon: 'warning',
                    title: title,
                    text: message,
                    confirmButtonColor: '#f6c23e'
                });
            },
            info: function(message, title = 'Information') {
                Swal.fire({
                    icon: 'info',
                    title: title,
                    text: message,
                    confirmButtonColor: '#36b9cc'
                });
            },
            confirm: function(message, callback, title = 'Confirmation') {
                Swal.fire({
                    title: title,
                    text: message,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#4e73df',
                    cancelButtonColor: '#858796',
                    confirmButtonText: 'Oui, confirmer',
                    cancelButtonText: 'Annuler'
                }).then((result) => {
                    if (result.isConfirmed && callback) {
                        callback();
                    }
                });
            }
        };

        // نظام تحميل موحد
        window.AccessPosLoader = {
            show: function() {
                $('#loadingOverlay').fadeIn();
            },
            hide: function() {
                $('#loadingOverlay').fadeOut();
            }
        };

        // وظائف مساعدة للـ DataTables
        window.AccessPosDataTable = {
            init: function(selector, options = {}) {
                const defaultOptions = {
                    "language": {
                        "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/French.json"
                    },
                    "pageLength": 25,
                    "responsive": true,
                    "processing": true,
                    "dom": 'Bfrtip',
                    "buttons": [
                        'copy', 'csv', 'excel', 'pdf', 'print'
                    ]
                };
                return $(selector).DataTable($.extend(defaultOptions, options));
            }
        };

        // وظائف مساعدة للـ Charts
        window.AccessPosChart = {
            colors: {
                primary: '#4e73df',
                success: '#1cc88a',
                info: '#36b9cc',
                warning: '#f6c23e',
                danger: '#e74a3b',
                secondary: '#858796'
            },
            defaultOptions: {
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        left: 10,
                        right: 25,
                        top: 25,
                        bottom: 0
                    }
                }
            }
        };

        // تهيئة عامة عند تحميل الصفحة
        $(document).ready(function() {
            // إخفاء التنبيهات بعد 5 ثواني
            setTimeout(function() {
                $('.alert').fadeOut();
            }, 5000);

            // تهيئة tooltips
            $('[data-toggle="tooltip"]').tooltip();

            // تهيئة popovers
            $('[data-toggle="popover"]').popover();

            // معالجة النماذج بـ AJAX
            $('.ajax-form').on('submit', function(e) {
                e.preventDefault();
                AccessPosLoader.show();
                
                const form = $(this);
                const url = form.attr('action');
                const method = form.attr('method') || 'POST';
                const data = new FormData(this);

                $.ajax({
                    url: url,
                    method: method,
                    data: data,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        AccessPosLoader.hide();
                        if (response.success) {
                            AccessPosAlert.success(response.message);
                            if (response.redirect) {
                                window.location.href = response.redirect;
                            }
                        }
                    },
                    error: function(xhr) {
                        AccessPosLoader.hide();
                        const response = xhr.responseJSON;
                        AccessPosAlert.error(response.message || 'Une erreur est survenue');
                    }
                });
            });
        });
    </script>

    @stack('scripts')
</body>

</html>
