<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="AccessPOS Pro - Système de gestion de point de vente">
    <meta name="author" content="AccessPOS">

    <title>Connexion - AccessPOS Pro</title>

    <!-- Custom fonts for this template-->
    <link href="{{ asset('startbootstrap-sb-admin-2-gh-pages/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{ asset('startbootstrap-sb-admin-2-gh-pages/css/sb-admin-2.min.css') }}" rel="stylesheet">

    <style>
        .bg-login-image {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #4e73df 100%);
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        
        .login-card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 0.75rem 1.5rem rgba(18, 38, 63, 0.03);
        }
        
        .card-body {
            padding: 2rem;
        }
        
        .text-center .h4 {
            color: #5a5c69;
            margin-bottom: 2rem;
        }
        
        .form-control-user {
            border-radius: 10rem;
            padding: 1.5rem 1rem;
            border: 1px solid #d1d3e2;
            background-color: #fff;
            color: #6e707e;
            transition: all 0.3s;
        }
        
        .form-control-user:focus {
            border-color: #4e73df;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }
        
        .btn-user {
            border-radius: 10rem;
            padding: 0.75rem 1rem;
            font-size: 0.8rem;
            font-weight: bold;
            letter-spacing: 0.1rem;
            text-transform: uppercase;
        }
        
        .brand-logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #4e73df, #36b9cc);
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            color: white;
            font-size: 2rem;
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
        }
        
        .welcome-text {
            color: #5a5c69;
            font-size: 0.9rem;
            margin-bottom: 2rem;
        }
        
        .forgot-password {
            color: #4e73df;
            text-decoration: none;
            font-size: 0.85rem;
        }
        
        .forgot-password:hover {
            color: #2e59d9;
            text-decoration: underline;
        }
        
        .alert-custom {
            border-radius: 0.5rem;
            border: none;
            padding: 1rem 1.5rem;
        }
        
        .feature-list {
            padding-left: 0;
            list-style: none;
        }
        
        .feature-item {
            padding: 0.5rem 0;
            color: white;
            display: flex;
            align-items: center;
        }
        
        .feature-item i {
            margin-right: 0.75rem;
            color: #36b9cc;
        }
        
        .login-footer {
            text-align: center;
            margin-top: 2rem;
            color: #858796;
            font-size: 0.8rem;
        }
        
        .loading-btn {
            position: relative;
        }
        
        .loading-btn .spinner-border {
            width: 1rem;
            height: 1rem;
            margin-right: 0.5rem;
        }
        
        @media (max-width: 768px) {
            .col-lg-6 {
                padding: 1rem;
            }
            
            .card-body {
                padding: 1.5rem;
            }
        }
    </style>
</head>

<body class="bg-gradient-primary">
    <div class="container">
        <!-- Outer Row -->
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12 col-md-9">
                <div class="card o-hidden border-0 shadow-lg my-5 login-card">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <!-- Left Side - Features & Welcome -->
                            <div class="col-lg-6 d-none d-lg-block bg-login-image">
                                <div class="p-5 text-white h-100 d-flex flex-column justify-content-center">
                                    <div class="text-center mb-4">
                                        <div class="brand-logo mx-auto">
                                            <i class="fas fa-cash-register"></i>
                                        </div>
                                        <h3 class="text-white font-weight-bold">AccessPOS Pro</h3>
                                        <p class="mb-0">Système de Gestion Avancé</p>
                                    </div>
                                    
                                    <div class="mt-4">
                                        <h5 class="text-white mb-3">Fonctionnalités Principales:</h5>
                                        <ul class="feature-list">
                                            <li class="feature-item">
                                                <i class="fas fa-chart-line"></i>
                                                Tableau de bord en temps réel
                                            </li>
                                            <li class="feature-item">
                                                <i class="fas fa-boxes"></i>
                                                Gestion complète des stocks
                                            </li>
                                            <li class="feature-item">
                                                <i class="fas fa-users"></i>
                                                Gestion des clients et fournisseurs
                                            </li>
                                            <li class="feature-item">
                                                <i class="fas fa-receipt"></i>
                                                Facturation et rapports avancés
                                            </li>
                                            <li class="feature-item">
                                                <i class="fas fa-mobile-alt"></i>
                                                Interface responsive
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Right Side - Login Form -->
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <div class="brand-logo d-lg-none">
                                            <i class="fas fa-cash-register"></i>
                                        </div>
                                        <h1 class="h4 text-gray-900 mb-2">Bon retour!</h1>
                                        <p class="welcome-text">
                                            Connectez-vous à votre compte pour accéder au tableau de bord
                                        </p>
                                    </div>

                                    <!-- Display validation errors -->
                                    @if ($errors->any())
                                        <div class="alert alert-danger alert-custom" role="alert">
                                            <div class="d-flex">
                                                <i class="fas fa-exclamation-triangle mr-2 mt-1"></i>
                                                <div>
                                                    <strong>Erreur!</strong> Veuillez corriger les erreurs suivantes:
                                                    <ul class="mb-0 mt-2">
                                                        @foreach ($errors->all() as $error)
                                                            <li>{{ $error }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Display status messages -->
                                    @if (session('status'))
                                        <div class="alert alert-success alert-custom" role="alert">
                                            <div class="d-flex">
                                                <i class="fas fa-check-circle mr-2 mt-1"></i>
                                                <div>{{ session('status') }}</div>
                                            </div>
                                        </div>
                                    @endif

                                    <form method="POST" action="{{ route('login') }}" id="loginForm" class="user">
                                        @csrf
                                        
                                        <div class="form-group">
                                            <input type="email" 
                                                   class="form-control form-control-user @error('email') is-invalid @enderror" 
                                                   id="exampleInputEmail" 
                                                   name="email" 
                                                   value="{{ old('email') }}" 
                                                   placeholder="Adresse email..."
                                                   required 
                                                   autofocus>
                                            @error('email')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        
                                        <div class="form-group">
                                            <input type="password" 
                                                   class="form-control form-control-user @error('password') is-invalid @enderror" 
                                                   id="exampleInputPassword" 
                                                   name="password" 
                                                   placeholder="Mot de passe..."
                                                   required>
                                            @error('password')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox small">
                                                <input type="checkbox" 
                                                       class="custom-control-input" 
                                                       id="customCheck" 
                                                       name="remember" 
                                                       {{ old('remember') ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="customCheck">
                                                    Se souvenir de moi
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary btn-user btn-block loading-btn" id="loginBtn">
                                            <span class="btn-text">Se connecter</span>
                                        </button>
                                    </form>

                                    <hr>
                                    
                                    <div class="text-center">
                                        @if (Route::has('password.request'))
                                            <a class="forgot-password" href="{{ route('password.request') }}">
                                                <i class="fas fa-key mr-1"></i>
                                                Mot de passe oublié?
                                            </a>
                                        @endif
                                    </div>

                                    <div class="login-footer">
                                        <p>&copy; {{ date('Y') }} AccessPOS Pro. Tous droits réservés.</p>
                                        <p>Version 2.0 - Powered by SB Admin 2</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="{{ asset('startbootstrap-sb-admin-2-gh-pages/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('startbootstrap-sb-admin-2-gh-pages/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Core plugin JavaScript-->
    <script src="{{ asset('startbootstrap-sb-admin-2-gh-pages/vendor/jquery-easing/jquery.easing.min.js') }}"></script>

    <!-- Custom scripts for all pages-->
    <script src="{{ asset('startbootstrap-sb-admin-2-gh-pages/js/sb-admin-2.min.js') }}"></script>

    <!-- Custom Login Scripts -->
    <script>
        $(document).ready(function() {
            // Form validation and submission
            $('#loginForm').on('submit', function(e) {
                const email = $('#exampleInputEmail').val();
                const password = $('#exampleInputPassword').val();
                
                // Basic validation
                if (!email || !password) {
                    e.preventDefault();
                    showAlert('Veuillez remplir tous les champs obligatoires.', 'danger');
                    return false;
                }
                
                // Email validation
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    e.preventDefault();
                    showAlert('Veuillez entrer une adresse email valide.', 'danger');
                    return false;
                }
                
                // Show loading state
                showLoading();
            });
            
            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);
            
            // Add focus effects
            $('.form-control-user').on('focus', function() {
                $(this).parent().addClass('focused');
            }).on('blur', function() {
                $(this).parent().removeClass('focused');
            });
            
            // Password visibility toggle (if needed)
            if ($('#togglePassword').length) {
                $('#togglePassword').on('click', function() {
                    const password = $('#exampleInputPassword');
                    const type = password.attr('type') === 'password' ? 'text' : 'password';
                    password.attr('type', type);
                    $(this).find('i').toggleClass('fa-eye fa-eye-slash');
                });
            }
        });
        
        function showLoading() {
            const btn = $('#loginBtn');
            const btnText = btn.find('.btn-text');
            
            btn.prop('disabled', true);
            btnText.html('<span class="spinner-border spinner-border-sm mr-2" role="status"></span>Connexion...');
        }
        
        function hideLoading() {
            const btn = $('#loginBtn');
            const btnText = btn.find('.btn-text');
            
            btn.prop('disabled', false);
            btnText.html('Se connecter');
        }
        
        function showAlert(message, type = 'info') {
            const alertClass = `alert-${type}`;
            const iconClass = type === 'danger' ? 'fa-exclamation-triangle' : 
                            type === 'success' ? 'fa-check-circle' : 'fa-info-circle';
            
            const alertHtml = `
                <div class="alert ${alertClass} alert-custom alert-dismissible fade show" role="alert">
                    <div class="d-flex">
                        <i class="fas ${iconClass} mr-2 mt-1"></i>
                        <div>${message}</div>
                        <button type="button" class="close ml-auto" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                    </div>
                </div>
            `;
            
            $('.user').before(alertHtml);
            
            // Auto-hide after 5 seconds
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);
        }
        
        // Handle form errors and reset loading state
        @if ($errors->any())
            hideLoading();
        @endif
        
        // Demo credentials helper (for development only)
        @if (app()->environment('local'))
            // Double-click to fill demo credentials
            $('.brand-logo').on('dblclick', function() {
                $('#exampleInputEmail').val('admin@accesspos.com');
                $('#exampleInputPassword').val('password123');
                showAlert('Identifiants de démonstration chargés!', 'info');
            });
        @endif
    </script>
</body>

</html>
