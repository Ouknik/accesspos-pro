<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="AccessPOS Pro - Réinitialisation du mot de passe">
    <meta name="author" content="AccessPOS">

    <title>Mot de passe oublié - AccessPOS Pro</title>

    <!-- Custom fonts for this template-->
    <link href="{{ asset('startbootstrap-sb-admin-2-gh-pages/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{ asset('startbootstrap-sb-admin-2-gh-pages/css/sb-admin-2.min.css') }}" rel="stylesheet">

    <style>
        .forgot-password-card {
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
        
        .back-link {
            color: #4e73df;
            text-decoration: none;
            font-size: 0.85rem;
        }
        
        .back-link:hover {
            color: #2e59d9;
            text-decoration: underline;
        }
        
        .instruction-text {
            color: #5a5c69;
            font-size: 0.9rem;
            line-height: 1.6;
        }
    </style>
</head>

<body class="bg-gradient-primary">
    <div class="container">
        <!-- Outer Row -->
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12 col-md-9">
                <div class="card o-hidden border-0 shadow-lg my-5 forgot-password-card">
                    <div class="card-body p-0">
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-block bg-password-image" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #4e73df 100%);">
                                <div class="p-5 text-white h-100 d-flex flex-column justify-content-center">
                                    <div class="text-center">
                                        <div class="brand-logo mx-auto">
                                            <i class="fas fa-key"></i>
                                        </div>
                                        <h3 class="text-white font-weight-bold mb-3">Récupération de compte</h3>
                                        <p class="mb-4">Ne vous inquiétez pas, cela arrive aux meilleurs d'entre nous!</p>
                                        
                                        <div class="text-left">
                                            <h5 class="text-white mb-3">Instructions:</h5>
                                            <ul class="text-white-50" style="list-style: none; padding-left: 0;">
                                                <li class="mb-2">
                                                    <i class="fas fa-check-circle text-success mr-2"></i>
                                                    Entrez votre adresse email
                                                </li>
                                                <li class="mb-2">
                                                    <i class="fas fa-check-circle text-success mr-2"></i>
                                                    Vérifiez votre boîte mail
                                                </li>
                                                <li class="mb-2">
                                                    <i class="fas fa-check-circle text-success mr-2"></i>
                                                    Cliquez sur le lien reçu
                                                </li>
                                                <li class="mb-2">
                                                    <i class="fas fa-check-circle text-success mr-2"></i>
                                                    Créez un nouveau mot de passe
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
                                            <i class="fas fa-key"></i>
                                        </div>
                                        <h1 class="h4 text-gray-900 mb-2">Mot de passe oublié?</h1>
                                        <p class="instruction-text mb-4">
                                            Pas de problème! Entrez simplement votre adresse email ci-dessous et nous vous enverrons un lien pour réinitialiser votre mot de passe.
                                        </p>
                                    </div>

                                    @if (session('status'))
                                        <div class="alert alert-success alert-custom" role="alert">
                                            <div class="d-flex">
                                                <i class="fas fa-check-circle mr-2 mt-1"></i>
                                                <div>
                                                    <strong>Email envoyé!</strong><br>
                                                    {{ session('status') }}
                                                </div>
                                            </div>
                                        </div>
                                    @endif

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

                                    <form method="POST" action="{{ route('password.email') }}" class="user" id="resetForm">
                                        @csrf
                                        <div class="form-group">
                                            <input type="email" 
                                                   class="form-control form-control-user @error('email') is-invalid @enderror" 
                                                   id="exampleInputEmail" 
                                                   name="email" 
                                                   value="{{ old('email') }}" 
                                                   placeholder="Entrez votre adresse email..."
                                                   required 
                                                   autofocus>
                                            @error('email')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary btn-user btn-block" id="resetBtn">
                                            <span class="btn-text">
                                                <i class="fas fa-paper-plane mr-1"></i>
                                                Envoyer le lien de réinitialisation
                                            </span>
                                        </button>
                                    </form>

                                    <hr>
                                    
                                    <div class="text-center">
                                        <a class="back-link" href="{{ route('login') }}">
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
            $('#resetForm').on('submit', function(e) {
                const email = $('#exampleInputEmail').val();
                
                if (!email) {
                    e.preventDefault();
                    showAlert('Veuillez entrer votre adresse email.', 'danger');
                    return false;
                }
                
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    e.preventDefault();
                    showAlert('Veuillez entrer une adresse email valide.', 'danger');
                    return false;
                }
                
                showLoading();
            });
            
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 8000);
        });
        
        function showLoading() {
            const btn = $('#resetBtn');
            const btnText = btn.find('.btn-text');
            
            btn.prop('disabled', true);
            btnText.html('<span class="spinner-border spinner-border-sm mr-2"></span>Envoi en cours...');
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
        }
    </script>
</body>

</html>
