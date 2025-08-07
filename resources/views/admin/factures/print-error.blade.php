<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erreur d'impression - Facture {{ $factureRef ?? 'N/A' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Times New Roman', serif;
            font-size: 14px;
            line-height: 1.6;
            color: #333;
            background: #f8f9fa;
            padding: 20px;
        }
        
        .error-container {
            max-width: 600px;
            margin: 50px auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .error-header {
            background: #e74a3b;
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .error-icon {
            font-size: 64px;
            margin-bottom: 15px;
        }
        
        .error-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .error-subtitle {
            font-size: 16px;
            opacity: 0.9;
        }
        
        .error-body {
            padding: 40px;
            text-align: center;
        }
        
        .error-message {
            font-size: 18px;
            color: #666;
            margin-bottom: 20px;
            line-height: 1.5;
        }
        
        .error-details {
            background: #f8f9fa;
            border-left: 4px solid #e74a3b;
            padding: 15px;
            margin: 20px 0;
            text-align: left;
        }
        
        .error-details strong {
            color: #e74a3b;
        }
        
        .actions {
            margin-top: 30px;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 24px;
            margin: 0 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: #007bff;
            color: white;
        }
        
        .btn-primary:hover {
            background: #0056b3;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #545b62;
        }
        
        .help-section {
            background: #e9ecef;
            padding: 20px;
            margin-top: 20px;
            border-radius: 5px;
        }
        
        .help-title {
            font-weight: bold;
            margin-bottom: 10px;
            color: #495057;
        }
        
        .help-list {
            text-align: left;
            margin: 0;
            padding-left: 20px;
        }
        
        .help-list li {
            margin-bottom: 5px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-header">
            <div class="error-icon">⚠️</div>
            <div class="error-title">Erreur d'Impression</div>
            <div class="error-subtitle">Impossible d'imprimer la facture</div>
        </div>
        
        <div class="error-body">
            <div class="error-message">
                Désolé, une erreur s'est produite lors de la préparation de l'impression de la facture.
            </div>
            
            <div class="error-details">
                <strong>Référence de la facture:</strong> {{ $factureRef ?? 'Non spécifiée' }}<br>
                @if(isset($error))
                    <strong>Détails de l'erreur:</strong> {{ $error }}<br>
                @endif
                <strong>Heure de l'erreur:</strong> {{ now()->format('d/m/Y à H:i:s') }}
            </div>
            
            <div class="actions">
                <a href="{{ route('admin.factures.index') }}" class="btn btn-primary">
                    📋 Retour aux Factures
                </a>
                <button onclick="window.close()" class="btn btn-secondary">
                    ❌ Fermer cette Fenêtre
                </button>
            </div>
            
            <div class="help-section">
                <div class="help-title">🔧 Solutions possibles:</div>
                <ul class="help-list">
                    <li>Vérifiez que la facture existe dans la base de données</li>
                    <li>Assurez-vous que vous avez les permissions nécessaires</li>
                    <li>Essayez de rafraîchir la page et réessayer</li>
                    <li>Contactez l'administrateur si le problème persiste</li>
                </ul>
            </div>
        </div>
    </div>

    <script>
        // Optionnel: Fermer automatiquement après 10 secondes
        setTimeout(function() {
            if (confirm('Voulez-vous fermer cette fenêtre automatiquement?')) {
                window.close();
            }
        }, 10000);
    </script>
</body>
</html>
