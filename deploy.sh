#!/bin/bash

# Configuration
VPS_HOST="163.245.218.28"
VPS_USER="root"
VPS_PASS="Mayana19998"
REMOTE_DIR="/var/www/cassino_new"

echo "🚀 Starting automated deployment to $VPS_HOST..."

# Sync files (excluding node_modules, vendor, and .git)
sshpass -p "$VPS_PASS" rsync -avz --exclude 'node_modules' --exclude 'vendor' --exclude '.git' --exclude '.env' --exclude 'storage/*.log' ./ $VPS_USER@$VPS_HOST:$REMOTE_DIR

# Remote commands
sshpass -p "$VPS_PASS" ssh $VPS_USER@$VPS_HOST << EOF
    cd $REMOTE_DIR
    echo "📦 Installing composer dependencies..."
    composer install --no-dev --optimize-autoloader
    
    echo "🛠 Running migrations..."
    php artisan migrate --force
    
    echo "🧹 Clearing Laravel cache..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan cache:clear
    
    echo "🧱 Optimizing assets..."
    npm install
    npm run build
    
    echo "🔄 Adjusting permissions..."
    mkdir -p public/popups public/banner public/founde public/casino_icons
    chown -R www-data:www-data storage bootstrap/cache public
    chmod -R 775 storage bootstrap/cache public
    
    echo "✅ Deployment finished successfully!"
EOF
