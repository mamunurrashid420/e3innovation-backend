#!/bin/bash

# E3 Innovation Backend Deployment Script
# This script deploys the Laravel backend to production

echo "🚀 Starting E3 Innovation Backend Deployment..."

# Step 1: Pull latest code (if using git)
echo "📥 Pulling latest code..."
# git pull origin main

# Step 2: Install/Update dependencies
echo "📦 Installing dependencies..."
composer install --optimize-autoloader --no-dev

# Step 3: Clear all caches
echo "🧹 Clearing caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Step 4: Run migrations
echo "🗄️ Running database migrations..."
php artisan migrate --force

# Step 5: Create storage link
echo "🔗 Creating storage link..."
php artisan storage:link

# Step 6: Optimize for production
echo "⚡ Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Step 7: Set permissions
echo "🔐 Setting permissions..."
chmod -R 755 storage bootstrap/cache
# chown -R www-data:www-data storage bootstrap/cache

# Step 8: Verify routes
echo "✅ Verifying routes..."
php artisan route:list | grep public

echo "✨ Deployment completed successfully!"
echo ""
echo "📋 Next steps:"
echo "1. Test API endpoints: curl https://api.e3bd.com/api/public/sliders"
echo "2. Check logs: tail -f storage/logs/laravel.log"
echo "3. Monitor application"

