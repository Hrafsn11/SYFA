#!/bin/bash

echo "🚀 Starting deployment setup..."

# 1. Configure Nginx
if [ -f /home/site/wwwroot/nginx.conf ]; then
    echo "📋 Configuring Nginx..."
    cp /home/site/wwwroot/nginx.conf /etc/nginx/sites-available/default
    service nginx reload
else
    echo "⚠️  nginx.conf not found, skipping Nginx configuration."
fi

# 2. Tune PHP-FPM for B1 plan (1 core, 1.75GB RAM)
echo "⚙️  Tuning PHP-FPM..."
PHP_FPM_CONF=$(find /usr/local/etc/php-fpm.d -name "www.conf" 2>/dev/null || find /etc/php -name "www.conf" 2>/dev/null | head -1)

if [ -n "$PHP_FPM_CONF" ]; then
    # Switch from dynamic to static for predictable memory usage on low-RAM plans
    sed -i 's/^pm = .*/pm = static/' "$PHP_FPM_CONF"
    # 8 workers × ~50MB each = ~400MB (safe for 1.75GB total)
    sed -i 's/^pm.max_children = .*/pm.max_children = 8/' "$PHP_FPM_CONF"
    # Max requests before worker recycles (prevents memory leaks)
    sed -i 's/^;*pm.max_requests = .*/pm.max_requests = 500/' "$PHP_FPM_CONF"
    # Process idle timeout
    sed -i 's/^;*pm.process_idle_timeout = .*/pm.process_idle_timeout = 10s/' "$PHP_FPM_CONF"

    echo "✅ PHP-FPM tuned: pm=static, max_children=8, max_requests=500"

    # Restart PHP-FPM to apply changes
    pkill php-fpm 2>/dev/null
    sleep 1
    php-fpm -D 2>/dev/null
else
    echo "⚠️  PHP-FPM www.conf not found, skipping tuning."
fi

# 3. Link Storage
echo "🔗 Linking storage..."
cd /home/site/wwwroot

if [ ! -d "storage/app/public" ]; then
    mkdir -p storage/app/public
fi

php artisan storage:link 2>/dev/null

# 4. Laravel Production Optimizations (cache config, routes, views)
echo "🚀 Running Laravel optimizations..."

# Clear old caches first
php artisan config:clear 2>/dev/null
php artisan route:clear 2>/dev/null
php artisan view:clear 2>/dev/null

# Build fresh caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache 2>/dev/null

echo "✅ Laravel caches built (config, routes, views, events)"

# 5. Ensure correct permissions
echo "🔒 Setting permissions..."
chmod -R 775 storage bootstrap/cache 2>/dev/null
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null

echo "✅ Setup complete! App is optimized for Azure B1 plan."
