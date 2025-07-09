#!/bin/bash
# نسخة تصحيحية لحذف الكود المكرر

# قراءة الملف حتى أول </html> فقط
$lines = Get-Content "resources\views\admin\tableau-de-bord-moderne.blade.php"
$endIndex = -1

for ($i = 0; $i -lt $lines.Count; $i++) {
    if ($lines[$i] -match "</html>") {
        $endIndex = $i
        break
    }
}

if ($endIndex -ne -1) {
    $cleanLines = $lines[0..$endIndex]
    $cleanLines | Set-Content "resources\views\admin\tableau-de-bord-moderne-clean.blade.php"
    Write-Host "Fichier nettoyé créé avec $($endIndex + 1) lignes"
} else {
    Write-Host "Balise </html> non trouvée"
}
