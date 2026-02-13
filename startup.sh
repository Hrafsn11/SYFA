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

# 2. Link Storage (Explicit Fix)
echo "🔗 Linking storage..."
# Navigate to public directory
cd /home/site/wwwroot/public

# Remove existing storage directory/symlink if it exists
if [ -d "storage" ] || [ -L "storage" ]; then
    echo "  - Removing existing storage symlink..."
    rm -rf storage
fi

# Create symlink manually as per user confirmation
echo "  - Creating new symlink..."
ln -s ../storage/app/public storage

# Verify symlink
if [ -L "storage" ]; then
    echo "✅ Storage symlink created successfully."
else
    echo "❌ Failed to create storage symlink."
fi

# 3. Run Migrations
echo "📦 Running database migrations..."
cd /home/site/wwwroot
php artisan migrate --force

# 4. Clear Caches
echo "🧹 Clearing caches..."
php artisan optimize:clear

echo "✅ Setup complete!"
