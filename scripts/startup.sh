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

# 2. Link Storage
echo "🔗 Linking storage..."
cd /home/site/wwwroot

# Ensure storage directory exists
if [ ! -d "storage/app/public" ]; then
    mkdir -p storage/app/public
fi

# Run storage:link (it handles existing links gracefully, or we can force it)
php artisan storage:link

echo "✅ Setup complete!"
