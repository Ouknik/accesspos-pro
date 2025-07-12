<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="AccessPOS Pro - Réinitialisation du mot de passe">
    <meta name="author" content="AccessPOS">

    <title>Nouveau mot de passe - AccessPOS Pro</title>

    <!-- Custom fonts for this template-->
    <link href="{{ asset('startbootstrap-sb-admin-2-gh-pages/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{ asset('startbootstrap-sb-admin-2-gh-pages/css/sb-admin-2.min.css') }}" rel="stylesheet">

    <style>
        .reset-password-card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 0.75rem 1.5rem rgba(18, 38, 63, 0.03);
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
        
        .alert-custom {
            border-radius: 0.5rem;
            border: none;
            padding: 1rem 1.5rem;
        }
        
        .password-requirements {
            background: #f8f9fc;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1rem;
            border-left: 4px solid #4e73df;
        }
        
        .requirement-item {
            font-size: 0.8rem;
            color: #5a5c69;
            margin-bottom: 0.25rem;
        }
        
        .requirement-item.valid {
            color: #1cc88a;
        }
        
        .requirement-item i {
            width: 16px;
        }
        
        .password-strength {
            height: 4px;
            background: #e3e6f0;
            border-radius: 2px;
            margin-top: 0.5rem;
            overflow: hidden;
        }
        
        .password-strength-bar {
            height: 100%;
            transition: all 0.3s ease;
            width: 0%;
        }
        
        .password-strength-weak {
            background: #e74a3b;
            width: 25%;
        }
        
        .password-strength-fair {
            background: #f6c23e;
            width: 50%;
        }
        
        .password-strength-good {
            background: #36b9cc;
            width: 75%;
        }
        
        .password-strength-strong {
            background: #1cc88a;
            width: 100%;
        }
    </style>
</head>

<body class="bg-gradient-primary">
    <div class="container">
        <!-- Outer Row -->
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12 col-md-9">
                <div class="card o-hidden border-0 shadow-lg my-5 reset-password-card">
                    <div class="card-body p-0">
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-block" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #4e73df 100%);">
                                <div class="p-5 text-white h-100 d-flex flex-column justify-content-center">
                                    <div class="text-center">
                                        <div class="brand-logo mx-auto">
                                            <i class="fas fa-lock"></i>
                                        </div>
                                        <h3 class="text-white font-weight-bold mb-3">Nouveau mot de passe</h3>
                                        <p class="mb-4">Choisissez un mot de passe sécurisé pour protéger votre compte.</p>
                                        
                                        <div class="text-left">
                                            <h5 class="text-white mb-3">Conseils de sécurité:</h5>
                                            <ul class="text-white-50" style="list-style: none; padding-left: 0;">
                                                <li class="mb-2">
                                                    <i class="fas fa-shield-alt text-success mr-2"></i>
                                                    Au moins 8 caractères
                                                </li>
                                                <li class="mb-2">
                                                    <i class="fas fa-shield-alt text-success mr-2"></i>
                                                    Mélange de lettres et chiffres
                                                </li>
                                                <li class="mb-2">
                                                    <i class="fas fa-shield-alt text-success mr-2"></i>
                                                    Caractères spéciaux
                                                </li>
                                                <li class="mb-2">
                                                    <i class="fas fa-shield-alt text-success mr-2"></i>
                                                    Évitez les mots courants
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <div class="brand-logo d-lg-none">
                                            <i class="fas fa-lock"></i>
                                        </div>
                                        <h1 class="h4 text-gray-900 mb-2">Réinitialiser le mot de passe</h1>
                                        <p class="mb-4 text-gray-600" style="font-size: 0.9rem;">
                                            Créez un nouveau mot de passe sécurisé pour votre compte.
                                        </p>
                                    </div>

                                    @if ($errors->any())
                                        <div class="alert alert-danger alert-custom" role="alert">
                                            <div class="d-flex">
                                                <i class="fas fa-exclamation-triangle mr-2 mt-1"></i>
                                                <div>
                                                    <strong>Erreur!</strong>
                                                    <ul class="mb-0 mt-2">
                                                        @foreach ($errors->all() as $error)
                                                            <li>{{ $error }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <form method="POST" action="{{ route('password.update') }}" class="user" id="resetPasswordForm">
                                        @csrf
                                        <input type="hidden" name="token" value="{{ $token }}">

                                        <div class="form-group">
                                            <input type="email" 
                                                   class="form-control form-control-user @error('email') is-invalid @enderror" 
                                                   id="email" 
                                                   name="email" 
                                                   value="{{ $email ?? old('email') }}" 
                                                   placeholder="Adresse email"
                                                   required 
                                                   readonly>
                                            @error('email')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <input type="password" 
                                                   class="form-control form-control-user @error('password') is-invalid @enderror" 
                                                   id="password" 
                                                   name="password" 
                                                   placeholder="Nouveau mot de passe"
                                                   required>
                                            <div class="password-strength">
                                                <div class="password-strength-bar" id="strengthBar"></div>
                                            </div>
                                            @error('password')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <input type="password" 
                                                   class="form-control form-control-user" 
                                                   id="password_confirmation" 
                                                   name="password_confirmation" 
                                                   placeholder="Confirmer le mot de passe"
                                                   required>
                                        </div>

                                        <!-- Password Requirements -->
                                        <div class="password-requirements">
                                            <small class="text-muted font-weight-bold">Exigences du mot de passe:</small>
                                            <div class="requirement-item" id="req-length">
                                                <i class="fas fa-times text-danger"></i> Au moins 8 caractères
                                            </div>
                                            <div class="requirement-item" id="req-lowercase">
                                                <i class="fas fa-times text-danger"></i> Une lettre minuscule
                                            </div>
                                            <div class="requirement-item" id="req-uppercase">
                                                <i class="fas fa-times text-danger"></i> Une lettre majuscule
                                            </div>
                                            <div class="requirement-item" id="req-number">
                                                <i class="fas fa-times text-danger"></i> Un chiffre
                                            </div>
                                            <div class="requirement-item" id="req-special">
                                                <i class="fas fa-times text-danger"></i> Un caractère spécial
                                            </div>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary btn-user btn-block" id="resetBtn">
                                            <span class="btn-text">
                                                <i class="fas fa-key mr-1"></i>
                                                Réinitialiser le mot de passe
                                            </span>
                                        </button>
                                    </form>

                                    <hr>
                                    
                                    <div class="text-center">
                                        <a class="small" href="{{ route('login') }}" style="color: #4e73df; text-decoration: none;">
                                            <i class="fas fa-arrow-left mr-1"></i>
                                            Retour à la connexion
                                        </a>
                                    </div>

                                    <div class="text-center mt-4">
                                        <small class="text-muted">
                                            &copy; {{ date('Y') }} AccessPOS Pro. Tous droits réservés.
                                        </small>
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

    <script>
        $(document).ready(function() {
            // Password strength and requirements checking
            $('#password').on('input', function() {
                checkPasswordRequirements($(this).val());
                checkPasswordStrength($(this).val());
            });
            
            // Password confirmation matching
            $('#password_confirmation').on('input', function() {
                checkPasswordMatch();
            });
            
            // Form submission
            $('#resetPasswordForm').on('submit', function(e) {
                const password = $('#password').val();
                const confirmation = $('#password_confirmation').val();
                
                if (!password || !confirmation) {
                    e.preventDefault();
                    showAlert('Veuillez remplir tous les champs.', 'danger');
                    return false;
                }
                
                if (password !== confirmation) {
                    e.preventDefault();
                    showAlert('Les mots de passe ne correspondent pas.', 'danger');
                    return false;
                }
                
                if (!isPasswordValid(password)) {
                    e.preventDefault();
                    showAlert('Le mot de passe ne respecte pas toutes les exigences.', 'danger');
                    return false;
                }
                
                showLoading();
            });
        });
        
        function checkPasswordRequirements(password) {
            const requirements = {
                'req-length': password.length >= 8,
                'req-lowercase': /[a-z]/.test(password),
                'req-uppercase': /[A-Z]/.test(password),
                'req-number': /\d/.test(password),
                'req-special': /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password)
            };
            
            Object.keys(requirements).forEach(req => {
                const element = $(`#${req}`);
                const icon = element.find('i');
                
                if (requirements[req]) {
                    element.addClass('valid');
                    icon.removeClass('fa-times text-danger').addClass('fa-check text-success');
                } else {
                    element.removeClass('valid');
                    icon.removeClass('fa-check text-success').addClass('fa-times text-danger');
                }
            });
        }
        
        function checkPasswordStrength(password) {
            let score = 0;
            const strengthBar = $('#strengthBar');
            
            // Length
            if (password.length >= 8) score++;
            if (password.length >= 12) score++;
            
            // Character types
            if (/[a-z]/.test(password)) score++;
            if (/[A-Z]/.test(password)) score++;
            if (/\d/.test(password)) score++;
            if (/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password)) score++;
            
            // Complexity
            if (password.length >= 10 && /[a-z]/.test(password) && /[A-Z]/.test(password) && 
                /\d/.test(password) && /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password)) {
                score++;
            }
            
            strengthBar.removeClass('password-strength-weak password-strength-fair password-strength-good password-strength-strong');
            
            if (score <= 2) {
                strengthBar.addClass('password-strength-weak');
            } else if (score <= 4) {
                strengthBar.addClass('password-strength-fair');
            } else if (score <= 6) {
                strengthBar.addClass('password-strength-good');
            } else {
                strengthBar.addClass('password-strength-strong');
            }
        }
        
        function checkPasswordMatch() {
            const password = $('#password').val();
            const confirmation = $('#password_confirmation').val();
            const confirmField = $('#password_confirmation');
            
            if (confirmation && password !== confirmation) {
                confirmField.addClass('is-invalid');
                if (!confirmField.next('.invalid-feedback').length) {
                    confirmField.after('<div class="invalid-feedback">Les mots de passe ne correspondent pas.</div>');
                }
            } else {
                confirmField.removeClass('is-invalid');
                confirmField.next('.invalid-feedback').remove();
            }
        }
        
        function isPasswordValid(password) {
            return password.length >= 8 &&
                   /[a-z]/.test(password) &&
                   /[A-Z]/.test(password) &&
                   /\d/.test(password) &&
                   /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password);
        }
        
        function showLoading() {
            const btn = $('#resetBtn');
            const btnText = btn.find('.btn-text');
            
            btn.prop('disabled', true);
            btnText.html('<span class="spinner-border spinner-border-sm mr-2"></span>Réinitialisation...');
        }
        
        function showAlert(message, type = 'info') {
            const alertClass = `alert-${type}`;
            const iconClass = type === 'danger' ? 'fa-exclamation-triangle' : 'fa-info-circle';
            
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
            
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);
        }
    </script>
</body>

</html>
