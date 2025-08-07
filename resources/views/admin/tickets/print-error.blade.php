<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erreur - Ticket Introuvable</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            font-size: 14px;
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
        }
        
        .error-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            max-width: 400px;
            margin: 50px auto;
        }
        
        .error-icon {
            font-size: 48px;
            color: #dc3545;
            margin-bottom: 20px;
        }
        
        .error-title {
            font-size: 20px;
            font-weight: bold;
            color: #dc3545;
            margin-bottom: 15px;
        }
        
        .error-message {
            color: #6c757d;
            margin-bottom: 20px;
            line-height: 1.5;
        }
        
        .ticket-ref {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            font-family: 'Courier New', monospace;
            color: #495057;
            margin-bottom: 20px;
        }
        
        .btn {
            padding: 10px 20px;
            margin: 5px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #545b62;
            color: white;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">⚠️</div>
        
        <div class="error-title">Ticket Introuvable</div>
        
        <div class="error-message">
            Le ticket demandé n'a pas pu être trouvé dans la base de données.
            <br>
            Veuillez vérifier la référence et réessayer.
        </div>
        
        <div class="ticket-ref">
            Référence recherchée: <strong>{{ $cmdRef }}</strong>
        </div>
        
        <div>
            <button class="btn btn-secondary" onclick="window.close()">
                ❌ Fermer cette fenêtre
            </button>
        </div>
        
        <div style="margin-top: 20px; font-size: 12px; color: #6c757d;">
            <p>Causes possibles:</p>
            <ul style="text-align: left; display: inline-block;">
                <li>Le ticket a été supprimé</li>
                <li>Erreur de référence</li>
                <li>Problème de connexion à la base</li>
            </ul>
        </div>
    </div>

    <script>
        // Auto-close après 10 secondes
        setTimeout(function() {
            window.close();
        }, 10000);
    </script>
</body>
</html>
