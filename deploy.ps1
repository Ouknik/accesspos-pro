# ===================================
# AccessPos Pro - Windows Production Deployment Script
# ===================================

param(
    [switch]$Help,
    [switch]$Test,
    [switch]$Backup,
    [string]$ProjectPath = "C:\inetpub\wwwroot\accesspos-pro",
    [string]$BackupPath = "C:\Backups\accesspos"
)

# Colors for output
$Red = [ConsoleColor]::Red
$Green = [ConsoleColor]::Green
$Yellow = [ConsoleColor]::Yellow
$Blue = [ConsoleColor]::Blue
$White = [ConsoleColor]::White

# Configuration
$AppName = "AccessPos Pro"
$NodeVersion = "18"
$PHPVersion = "8.1"

# Functions
function Write-Header($Message) {
    Write-Host "==================================" -ForegroundColor $Blue
    Write-Host "  $Message" -ForegroundColor $Blue
    Write-Host "==================================" -ForegroundColor $Blue
}

function Write-Success($Message) {
    Write-Host "✅ $Message" -ForegroundColor $Green
}

function Write-Warning($Message) {
    Write-Host "⚠️  $Message" -ForegroundColor $Yellow
}

function Write-Error($Message) {
    Write-Host "❌ $Message" -ForegroundColor $Red
}

function Test-Requirements {
    Write-Header "فحص المتطلبات"
    
    # Check PHP
    try {
        $phpVersion = & php -v 2>$null | Select-String "PHP $PHPVersion"
        if ($phpVersion) {
            Write-Success "PHP $PHPVersion متوفر"
        } else {
            Write-Error "PHP $PHPVersion مطلوب"
            exit 1
        }
    } catch {
        Write-Error "PHP غير متوفر"
        exit 1
    }
    
    # Check Node.js
    try {
        $nodeVersion = & node -v 2>$null
        if ($nodeVersion -like "*v$NodeVersion*") {
            Write-Success "Node.js $NodeVersion متوفر"
        } else {
            Write-Error "Node.js $NodeVersion مطلوب"
            exit 1
        }
    } catch {
        Write-Error "Node.js غير متوفر"
        exit 1
    }
    
    # Check Composer
    try {
        & composer --version 2>$null | Out-Null
        Write-Success "Composer متوفر"
    } catch {
        Write-Error "Composer غير متوفر"
        exit 1
    }
    
    # Check npm
    try {
        & npm --version 2>$null | Out-Null
        Write-Success "npm متوفر"
    } catch {
        Write-Error "npm غير متوفر"
        exit 1
    }
}

function New-Backup {
    Write-Header "إنشاء نسخة احتياطية"
    
    $timestamp = Get-Date -Format "yyyyMMdd_HHmmss"
    $currentBackup = Join-Path $BackupPath $timestamp
    
    # Create backup directory
    if (!(Test-Path $BackupPath)) {
        New-Item -ItemType Directory -Path $BackupPath -Force | Out-Null
    }
    New-Item -ItemType Directory -Path $currentBackup -Force | Out-Null
    
    # Backup database
    $envFile = Join-Path $ProjectPath ".env"
    if (Test-Path $envFile) {
        Write-Warning "إنشاء نسخة احتياطية من قاعدة البيانات..."
        
        # Extract database config from .env
        $envContent = Get-Content $envFile
        $dbHost = ($envContent | Where-Object { $_ -like "DB_HOST=*" }) -replace "DB_HOST=", ""
        $dbPort = ($envContent | Where-Object { $_ -like "DB_PORT=*" }) -replace "DB_PORT=", ""
        $dbDatabase = ($envContent | Where-Object { $_ -like "DB_DATABASE=*" }) -replace "DB_DATABASE=", ""
        $dbUsername = ($envContent | Where-Object { $_ -like "DB_USERNAME=*" }) -replace "DB_USERNAME=", ""
        $dbPassword = ($envContent | Where-Object { $_ -like "DB_PASSWORD=*" }) -replace "DB_PASSWORD=", ""
        
        # Create database backup using mysqldump
        $backupFile = Join-Path $currentBackup "database.sql"
        & mysqldump -h$dbHost -P$dbPort -u$dbUsername -p$dbPassword $dbDatabase > $backupFile 2>$null
        
        if ($LASTEXITCODE -eq 0) {
            Write-Success "تم إنشاء نسخة احتياطية من قاعدة البيانات"
        } else {
            Write-Warning "فشل في إنشاء نسخة احتياطية من قاعدة البيانات"
        }
    }
    
    # Backup files
    Write-Warning "إنشاء نسخة احتياطية من الملفات..."
    
    $storagePath = Join-Path $ProjectPath "storage"
    if (Test-Path $storagePath) {
        Copy-Item -Path $storagePath -Destination $currentBackup -Recurse -Force
    }
    
    $uploadsPath = Join-Path $ProjectPath "public\uploads"
    if (Test-Path $uploadsPath) {
        Copy-Item -Path $uploadsPath -Destination $currentBackup -Recurse -Force
    }
    
    if (Test-Path $envFile) {
        Copy-Item -Path $envFile -Destination $currentBackup
    }
    
    Write-Success "تم إنشاء النسخة الاحتياطية في: $currentBackup"
    return $currentBackup
}

function Update-Code {
    Write-Header "تحديث الكود"
    
    Set-Location $ProjectPath
    
    # Pull latest code (if using git)
    if (Test-Path ".git") {
        Write-Warning "سحب آخر التحديثات من Git..."
        & git pull origin main
        if ($LASTEXITCODE -eq 0) {
            Write-Success "تم تحديث الكود"
        } else {
            Write-Error "فشل في تحديث الكود"
            exit 1
        }
    } else {
        Write-Warning "لا يوجد Git repository - تحديث يدوي مطلوب"
    }
}

function Install-Dependencies {
    Write-Header "تثبيت التبعيات"
    
    Set-Location $ProjectPath
    
    # Install PHP dependencies
    Write-Warning "تثبيت PHP dependencies..."
    & composer install --no-dev --optimize-autoloader --no-interaction
    if ($LASTEXITCODE -eq 0) {
        Write-Success "تم تثبيت PHP dependencies"
    } else {
        Write-Error "فشل في تثبيت PHP dependencies"
        exit 1
    }
    
    # Install Node.js dependencies
    Write-Warning "تثبيت Node.js dependencies..."
    & npm ci --production
    if ($LASTEXITCODE -eq 0) {
        Write-Success "تم تثبيت Node.js dependencies"
    } else {
        Write-Error "فشل في تثبيت Node.js dependencies"
        exit 1
    }
}

function Build-Assets {
    Write-Header "بناء الأصول (Assets)"
    
    Set-Location $ProjectPath
    
    # Build production assets
    Write-Warning "بناء production assets..."
    & npm run build
    if ($LASTEXITCODE -eq 0) {
        Write-Success "تم بناء الأصول"
    } else {
        Write-Error "فشل في بناء الأصول"
        exit 1
    }
}

function Optimize-Laravel {
    Write-Header "تحسين Laravel"
    
    Set-Location $ProjectPath
    
    # Clear all caches
    Write-Warning "مسح الكاش..."
    & php artisan cache:clear
    & php artisan config:clear
    & php artisan route:clear
    & php artisan view:clear
    Write-Success "تم مسح الكاش"
    
    # Run migrations
    Write-Warning "تشغيل الـ migrations..."
    & php artisan migrate --force
    if ($LASTEXITCODE -eq 0) {
        Write-Success "تم تشغيل الـ migrations"
    } else {
        Write-Error "فشل في تشغيل الـ migrations"
        exit 1
    }
    
    # Cache configurations
    Write-Warning "تجميع الإعدادات..."
    & php artisan config:cache
    & php artisan route:cache
    & php artisan view:cache
    Write-Success "تم تجميع الإعدادات"
    
    # Optimize autoloader
    Write-Warning "تحسين الـ autoloader..."
    & composer dump-autoload --optimize
    Write-Success "تم تحسين الـ autoloader"
}

function Set-Permissions {
    Write-Header "إعداد الصلاحيات"
    
    Set-Location $ProjectPath
    
    Write-Warning "إعداد صلاحيات الملفات..."
    
    # Set IIS_IUSRS permissions for web directory
    $acl = Get-Acl $ProjectPath
    $accessRule = New-Object System.Security.AccessControl.FileSystemAccessRule("IIS_IUSRS", "FullControl", "ContainerInherit,ObjectInherit", "None", "Allow")
    $acl.SetAccessRule($accessRule)
    Set-Acl $ProjectPath $acl
    
    # Special permissions for storage
    $storagePath = Join-Path $ProjectPath "storage"
    if (Test-Path $storagePath) {
        $acl = Get-Acl $storagePath
        $acl.SetAccessRule($accessRule)
        Set-Acl $storagePath $acl
    }
    
    # Special permissions for bootstrap/cache
    $cachePath = Join-Path $ProjectPath "bootstrap\cache"
    if (Test-Path $cachePath) {
        $acl = Get-Acl $cachePath
        $acl.SetAccessRule($accessRule)
        Set-Acl $cachePath $acl
    }
    
    Write-Success "تم إعداد الصلاحيات"
}

function Restart-Services {
    Write-Header "إعادة تشغيل الخدمات"
    
    # Restart IIS
    Write-Warning "إعادة تشغيل IIS..."
    try {
        & iisreset /restart
        Write-Success "تم إعادة تشغيل IIS"
    } catch {
        Write-Warning "فشل في إعادة تشغيل IIS - قد تحتاج لصلاحيات مدير"
    }
}

function Test-Deployment {
    Write-Header "تشغيل الاختبارات"
    
    Set-Location $ProjectPath
    
    # Check if site is accessible
    Write-Warning "فحص صحة التطبيق..."
    
    try {
        $response = Invoke-WebRequest -Uri "http://localhost" -TimeoutSec 10
        if ($response.StatusCode -eq 200) {
            Write-Success "الموقع متاح ويعمل"
        } else {
            Write-Error "الموقع غير متاح - كود الاستجابة: $($response.StatusCode)"
            exit 1
        }
    } catch {
        Write-Error "فشل في الوصول للموقع: $($_.Exception.Message)"
        exit 1
    }
    
    # Check database connection
    Write-Warning "فحص الاتصال بقاعدة البيانات..."
    & php artisan tinker --execute="DB::connection()->getPdo();" 2>$null
    if ($LASTEXITCODE -eq 0) {
        Write-Success "الاتصال بقاعدة البيانات يعمل"
    } else {
        Write-Error "فشل الاتصال بقاعدة البيانات"
        exit 1
    }
}

function Start-Cleanup {
    Write-Header "تنظيف الملفات"
    
    Set-Location $ProjectPath
    
    # Remove development files
    Write-Warning "حذف ملفات التطوير..."
    $filesToRemove = @("README.md", ".env.example")
    foreach ($file in $filesToRemove) {
        if (Test-Path $file) {
            Remove-Item $file -Force
        }
    }
    
    # Clean up old logs (older than 30 days)
    Write-Warning "تنظيف السجلات القديمة..."
    $logsPath = Join-Path $ProjectPath "storage\logs"
    if (Test-Path $logsPath) {
        Get-ChildItem $logsPath -Filter "*.log" | Where-Object { $_.LastWriteTime -lt (Get-Date).AddDays(-30) } | Remove-Item -Force
    }
    
    # Clean up old backups (older than 30 days)
    Write-Warning "تنظيف النسخ الاحتياطية القديمة..."
    if (Test-Path $BackupPath) {
        Get-ChildItem $BackupPath -Directory | Where-Object { $_.LastWriteTime -lt (Get-Date).AddDays(-30) } | Remove-Item -Recurse -Force
    }
    
    Write-Success "تم التنظيف"
}

function Show-Help {
    Write-Host "استخدام: .\deploy.ps1 [خيارات]" -ForegroundColor $Blue
    Write-Host ""
    Write-Host "خيارات:" -ForegroundColor $Yellow
    Write-Host "  -Help           عرض هذه المساعدة" -ForegroundColor $White
    Write-Host "  -Test           تشغيل الاختبارات فقط" -ForegroundColor $White
    Write-Host "  -Backup         إنشاء نسخة احتياطية فقط" -ForegroundColor $White
    Write-Host "  -ProjectPath    مسار المشروع (افتراضي: C:\inetpub\wwwroot\accesspos-pro)" -ForegroundColor $White
    Write-Host "  -BackupPath     مسار النسخ الاحتياطية (افتراضي: C:\Backups\accesspos)" -ForegroundColor $White
    Write-Host ""
    Write-Host "أمثلة:" -ForegroundColor $Yellow
    Write-Host "  .\deploy.ps1                نشر كامل" -ForegroundColor $White
    Write-Host "  .\deploy.ps1 -Test          اختبار النشر" -ForegroundColor $White
    Write-Host "  .\deploy.ps1 -Backup        نسخة احتياطية فقط" -ForegroundColor $White
}

function Start-MainDeployment {
    Write-Header "نشر $AppName"
    
    # Ask for confirmation
    Write-Host "هذا سكريبت نشر الإنتاج. هل تريد المتابعة؟ (y/N): " -ForegroundColor $Yellow -NoNewline
    $confirmation = Read-Host
    if ($confirmation -ne "y" -and $confirmation -ne "Y") {
        Write-Warning "تم إلغاء النشر"
        exit 0
    }
    
    # Enable maintenance mode
    Set-Location $ProjectPath
    & php artisan down --message="جاري التحديث..." --retry=60
    
    # Execute deployment steps
    Test-Requirements
    $backupPath = New-Backup
    Update-Code
    Install-Dependencies
    Build-Assets
    Optimize-Laravel
    Set-Permissions
    Restart-Services
    
    # Disable maintenance mode
    & php artisan up
    
    Test-Deployment
    Start-Cleanup
    
    Write-Header "✅ تم النشر بنجاح!"
    Write-Host "$AppName جاهز للاستخدام" -ForegroundColor $Green
    Write-Host "النسخة الاحتياطية محفوظة في: $backupPath" -ForegroundColor $Blue
}

# Main execution
if ($Help) {
    Show-Help
    exit 0
}

if ($Test) {
    Test-Deployment
    exit 0
}

if ($Backup) {
    New-Backup
    exit 0
}

# Run main deployment
Start-MainDeployment
