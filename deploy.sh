#!/bin/bash

# ===================================
# AccessPos Pro - Production Deployment Script
# ===================================

set -e  # Exit on any error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
APP_NAME="AccessPos Pro"
PROJECT_DIR="/var/www/accesspos-pro"
BACKUP_DIR="/var/backups/accesspos"
NODE_VERSION="18"
PHP_VERSION="8.1"

# Functions
print_header() {
    echo -e "${BLUE}=================================="
    echo -e "  $1"
    echo -e "==================================${NC}"
}

print_success() {
    echo -e "${GREEN}✅ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

print_error() {
    echo -e "${RED}❌ $1${NC}"
}

check_requirements() {
    print_header "فحص المتطلبات"
    
    # Check PHP version
    if ! php -v | grep -q "PHP $PHP_VERSION"; then
        print_error "PHP $PHP_VERSION مطلوب"
        exit 1
    fi
    print_success "PHP $PHP_VERSION متوفر"
    
    # Check Node.js version
    if ! node -v | grep -q "v$NODE_VERSION"; then
        print_error "Node.js $NODE_VERSION مطلوب"
        exit 1
    fi
    print_success "Node.js $NODE_VERSION متوفر"
    
    # Check Composer
    if ! command -v composer &> /dev/null; then
        print_error "Composer غير متوفر"
        exit 1
    fi
    print_success "Composer متوفر"
    
    # Check npm
    if ! command -v npm &> /dev/null; then
        print_error "npm غير متوفر"
        exit 1
    fi
    print_success "npm متوفر"
}

create_backup() {
    print_header "إنشاء نسخة احتياطية"
    
    # Create backup directory
    mkdir -p "$BACKUP_DIR/$(date +%Y%m%d_%H%M%S)"
    CURRENT_BACKUP="$BACKUP_DIR/$(date +%Y%m%d_%H%M%S)"
    
    # Backup database
    if [ -f "$PROJECT_DIR/.env" ]; then
        print_warning "إنشاء نسخة احتياطية من قاعدة البيانات..."
        
        # Extract database config from .env
        DB_HOST=$(grep DB_HOST $PROJECT_DIR/.env | cut -d '=' -f2)
        DB_PORT=$(grep DB_PORT $PROJECT_DIR/.env | cut -d '=' -f2)
        DB_DATABASE=$(grep DB_DATABASE $PROJECT_DIR/.env | cut -d '=' -f2)
        DB_USERNAME=$(grep DB_USERNAME $PROJECT_DIR/.env | cut -d '=' -f2)
        DB_PASSWORD=$(grep DB_PASSWORD $PROJECT_DIR/.env | cut -d '=' -f2)
        
        mysqldump -h$DB_HOST -P$DB_PORT -u$DB_USERNAME -p$DB_PASSWORD $DB_DATABASE > "$CURRENT_BACKUP/database.sql"
        print_success "تم إنشاء نسخة احتياطية من قاعدة البيانات"
    fi
    
    # Backup files
    print_warning "إنشاء نسخة احتياطية من الملفات..."
    cp -r "$PROJECT_DIR/storage" "$CURRENT_BACKUP/"
    cp -r "$PROJECT_DIR/public/uploads" "$CURRENT_BACKUP/" 2>/dev/null || true
    cp "$PROJECT_DIR/.env" "$CURRENT_BACKUP/" 2>/dev/null || true
    
    print_success "تم إنشاء النسخة الاحتياطية في: $CURRENT_BACKUP"
}

update_code() {
    print_header "تحديث الكود"
    
    cd "$PROJECT_DIR"
    
    # Pull latest code (if using git)
    if [ -d ".git" ]; then
        print_warning "سحب آخر التحديثات من Git..."
        git pull origin main
        print_success "تم تحديث الكود"
    else
        print_warning "لا يوجد Git repository - تحديث يدوي مطلوب"
    fi
}

install_dependencies() {
    print_header "تثبيت التبعيات"
    
    cd "$PROJECT_DIR"
    
    # Install PHP dependencies
    print_warning "تثبيت PHP dependencies..."
    composer install --no-dev --optimize-autoloader --no-interaction
    print_success "تم تثبيت PHP dependencies"
    
    # Install Node.js dependencies
    print_warning "تثبيت Node.js dependencies..."
    npm ci --production
    print_success "تم تثبيت Node.js dependencies"
}

build_assets() {
    print_header "بناء الأصول (Assets)"
    
    cd "$PROJECT_DIR"
    
    # Build production assets
    print_warning "بناء production assets..."
    npm run build
    print_success "تم بناء الأصول"
    
    # Optimize images (if imagemin is available)
    if command -v imagemin &> /dev/null; then
        print_warning "تحسين الصور..."
        find public/images -name "*.jpg" -o -name "*.png" -o -name "*.jpeg" | xargs imagemin --out-dir=public/images/
        print_success "تم تحسين الصور"
    fi
}

optimize_laravel() {
    print_header "تحسين Laravel"
    
    cd "$PROJECT_DIR"
    
    # Clear all caches
    print_warning "مسح الكاش..."
    php artisan cache:clear
    php artisan config:clear
    php artisan route:clear
    php artisan view:clear
    print_success "تم مسح الكاش"
    
    # Run migrations
    print_warning "تشغيل الـ migrations..."
    php artisan migrate --force
    print_success "تم تشغيل الـ migrations"
    
    # Cache configurations
    print_warning "تجميع الإعدادات..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    print_success "تم تجميع الإعدادات"
    
    # Optimize autoloader
    print_warning "تحسين الـ autoloader..."
    composer dump-autoload --optimize
    print_success "تم تحسين الـ autoloader"
}

set_permissions() {
    print_header "إعداد الصلاحيات"
    
    cd "$PROJECT_DIR"
    
    # Set proper permissions
    print_warning "إعداد صلاحيات الملفات..."
    
    # Web server user (usually www-data on Ubuntu)
    WEB_USER="www-data"
    
    # Set ownership
    sudo chown -R $WEB_USER:$WEB_USER .
    
    # Set permissions
    sudo find . -type f -exec chmod 644 {} \;
    sudo find . -type d -exec chmod 755 {} \;
    
    # Special permissions for storage and bootstrap/cache
    sudo chmod -R 775 storage/
    sudo chmod -R 775 bootstrap/cache/
    sudo chmod -R 775 public/uploads/ 2>/dev/null || true
    
    print_success "تم إعداد الصلاحيات"
}

restart_services() {
    print_header "إعادة تشغيل الخدمات"
    
    # Restart PHP-FPM
    if systemctl is-active --quiet php$PHP_VERSION-fpm; then
        print_warning "إعادة تشغيل PHP-FPM..."
        sudo systemctl restart php$PHP_VERSION-fpm
        print_success "تم إعادة تشغيل PHP-FPM"
    fi
    
    # Restart Nginx
    if systemctl is-active --quiet nginx; then
        print_warning "إعادة تشغيل Nginx..."
        sudo systemctl restart nginx
        print_success "تم إعادة تشغيل Nginx"
    fi
    
    # Restart Redis (if used)
    if systemctl is-active --quiet redis; then
        print_warning "إعادة تشغيل Redis..."
        sudo systemctl restart redis
        print_success "تم إعادة تشغيل Redis"
    fi
    
    # Restart Queue Workers (if used)
    if systemctl is-active --quiet laravel-worker; then
        print_warning "إعادة تشغيل Queue Workers..."
        sudo systemctl restart laravel-worker
        print_success "تم إعادة تشغيل Queue Workers"
    fi
}

run_tests() {
    print_header "تشغيل الاختبارات"
    
    cd "$PROJECT_DIR"
    
    # Run basic health check
    print_warning "فحص صحة التطبيق..."
    
    # Check if site is accessible
    if curl -f -s -o /dev/null "http://localhost"; then
        print_success "الموقع متاح ويعمل"
    else
        print_error "الموقع غير متاح"
        exit 1
    fi
    
    # Check database connection
    if php artisan tinker --execute="DB::connection()->getPdo();" &>/dev/null; then
        print_success "الاتصال بقاعدة البيانات يعمل"
    else
        print_error "فشل الاتصال بقاعدة البيانات"
        exit 1
    fi
    
    # Run Laravel tests (if available)
    if [ -f "phpunit.xml" ]; then
        print_warning "تشغيل اختبارات Laravel..."
        php artisan test --env=testing
        print_success "اجتازت جميع الاختبارات"
    fi
}

cleanup() {
    print_header "تنظيف الملفات"
    
    cd "$PROJECT_DIR"
    
    # Remove development files
    print_warning "حذف ملفات التطوير..."
    rm -f .env.example
    rm -f README.md
    rm -rf tests/
    rm -rf node_modules/
    
    # Clean up old logs
    print_warning "تنظيف السجلات القديمة..."
    find storage/logs/ -name "*.log" -mtime +30 -delete 2>/dev/null || true
    
    # Clean up old backups
    print_warning "تنظيف النسخ الاحتياطية القديمة..."
    find "$BACKUP_DIR" -type d -mtime +30 -exec rm -rf {} + 2>/dev/null || true
    
    print_success "تم التنظيف"
}

main() {
    print_header "نشر $APP_NAME"
    
    # Ask for confirmation
    echo -e "${YELLOW}هذا سكريبت نشر الإنتاج. هل تريد المتابعة؟ (y/N)${NC}"
    read -r confirmation
    if [[ ! $confirmation =~ ^[Yy]$ ]]; then
        print_warning "تم إلغاء النشر"
        exit 0
    fi
    
    # Enable maintenance mode
    cd "$PROJECT_DIR"
    php artisan down --message="جاري التحديث..." --retry=60
    
    # Execute deployment steps
    check_requirements
    create_backup
    update_code
    install_dependencies
    build_assets
    optimize_laravel
    set_permissions
    restart_services
    
    # Disable maintenance mode
    php artisan up
    
    run_tests
    cleanup
    
    print_header "✅ تم النشر بنجاح!"
    echo -e "${GREEN}$APP_NAME جاهز للاستخدام${NC}"
    echo -e "${BLUE}النسخة الاحتياطية محفوظة في: $CURRENT_BACKUP${NC}"
}

# Show help
if [[ "$1" == "--help" ]] || [[ "$1" == "-h" ]]; then
    echo "استخدام: $0 [خيارات]"
    echo ""
    echo "خيارات:"
    echo "  --help, -h     عرض هذه المساعدة"
    echo "  --test         تشغيل الاختبارات فقط"
    echo "  --backup       إنشاء نسخة احتياطية فقط"
    echo ""
    echo "مثال:"
    echo "  $0             نشر كامل"
    echo "  $0 --test      اختبار النشر"
    echo "  $0 --backup    نسخة احتياطية فقط"
    exit 0
fi

# Handle different modes
if [[ "$1" == "--test" ]]; then
    run_tests
    exit 0
elif [[ "$1" == "--backup" ]]; then
    create_backup
    exit 0
else
    main
fi
