#!/bin/bash

# Laravel Environment Setup Script
# This script helps set up the environment for the Laravel application

echo "🚀 Laravel Environment Setup"
echo "=============================="

# Check if .env exists
if [ ! -f .env ]; then
    echo "📋 Copying .env.example to .env..."
    cp .env.example .env
    echo "✅ .env file created"
else
    echo "⚠️  .env file already exists"
    read -p "Do you want to overwrite it? (y/N): " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        cp .env.example .env
        echo "✅ .env file overwritten"
    else
        echo "ℹ️  Keeping existing .env file"
    fi
fi

# Generate application key
echo "🔑 Generating application key..."
php artisan key:generate

# Ask for database configuration
echo ""
echo "🗄️  Database Configuration"
echo "=========================="
echo "1. SQLite (Default - No setup required)"
echo "2. MySQL (Local development)"
echo "3. MySQL (Laravel Sail)"
echo "4. PostgreSQL"
echo ""

read -p "Choose database type (1-4): " db_choice

case $db_choice in
    1)
        echo "✅ Using SQLite configuration"
        # SQLite is already configured in .env.example
        ;;
    2)
        echo "📝 MySQL Local Configuration"
        read -p "Enter MySQL host (default: 127.0.0.1): " mysql_host
        mysql_host=${mysql_host:-127.0.0.1}
        
        read -p "Enter MySQL port (default: 3306): " mysql_port
        mysql_port=${mysql_port:-3306}
        
        read -p "Enter database name: " mysql_db
        read -p "Enter MySQL username: " mysql_user
        read -s -p "Enter MySQL password: " mysql_pass
        echo
        
        # Update .env file
        sed -i.bak "s/DB_CONNECTION=sqlite/DB_CONNECTION=mysql/" .env
        sed -i.bak "s/#DB_HOST=mysql/DB_HOST=$mysql_host/" .env
        sed -i.bak "s/#DB_PORT=3306/DB_PORT=$mysql_port/" .env
        sed -i.bak "s|DB_DATABASE=.*|DB_DATABASE=$mysql_db|" .env
        sed -i.bak "s/#DB_USERNAME=sail/DB_USERNAME=$mysql_user/" .env
        sed -i.bak "s/#DB_PASSWORD=password/DB_PASSWORD=$mysql_pass/" .env
        
        echo "✅ MySQL configuration updated"
        ;;
    3)
        echo "🐳 Using Laravel Sail MySQL configuration"
        # Sail configuration is already in .env.example
        ;;
    4)
        echo "📝 PostgreSQL Configuration"
        read -p "Enter PostgreSQL host (default: 127.0.0.1): " pgsql_host
        pgsql_host=${pgsql_host:-127.0.0.1}
        
        read -p "Enter PostgreSQL port (default: 5432): " pgsql_port
        pgsql_port=${pgsql_port:-5432}
        
        read -p "Enter database name: " pgsql_db
        read -p "Enter PostgreSQL username: " pgsql_user
        read -s -p "Enter PostgreSQL password: " pgsql_pass
        echo
        
        # Update .env file
        sed -i.bak "s/DB_CONNECTION=sqlite/DB_CONNECTION=pgsql/" .env
        sed -i.bak "s/#DB_HOST=mysql/DB_HOST=$pgsql_host/" .env
        sed -i.bak "s/#DB_PORT=3306/DB_PORT=$pgsql_port/" .env
        sed -i.bak "s|DB_DATABASE=.*|DB_DATABASE=$pgsql_db|" .env
        sed -i.bak "s/#DB_USERNAME=sail/DB_USERNAME=$pgsql_user/" .env
        sed -i.bak "s/#DB_PASSWORD=password/DB_PASSWORD=$pgsql_pass/" .env
        
        echo "✅ PostgreSQL configuration updated"
        ;;
    *)
        echo "❌ Invalid choice. Using default SQLite configuration."
        ;;
esac

# Ask for application URL
echo ""
echo "🌐 Application URL Configuration"
echo "================================"
read -p "Enter application URL (default: http://localhost:8000): " app_url
app_url=${app_url:-http://localhost:8000}

# Update APP_URL in .env
sed -i.bak "s|APP_URL=.*|APP_URL=$app_url|" .env

# Ask for environment
echo ""
echo "🔧 Environment Configuration"
echo "============================"
echo "1. Local Development"
echo "2. Staging"
echo "3. Production"
echo ""

read -p "Choose environment (1-3): " env_choice

case $env_choice in
    1)
        echo "✅ Setting up for Local Development"
        sed -i.bak "s/APP_ENV=.*/APP_ENV=local/" .env
        sed -i.bak "s/APP_DEBUG=.*/APP_DEBUG=true/" .env
        ;;
    2)
        echo "✅ Setting up for Staging"
        sed -i.bak "s/APP_ENV=.*/APP_ENV=staging/" .env
        sed -i.bak "s/APP_DEBUG=.*/APP_DEBUG=false/" .env
        ;;
    3)
        echo "✅ Setting up for Production"
        sed -i.bak "s/APP_ENV=.*/APP_ENV=production/" .env
        sed -i.bak "s/APP_DEBUG=.*/APP_DEBUG=false/" .env
        ;;
    *)
        echo "❌ Invalid choice. Using default local development."
        ;;
esac

# Clean up backup files
rm -f .env.bak

echo ""
echo "🎉 Environment setup complete!"
echo "=============================="
echo ""
echo "📋 Next steps:"
echo "1. Install dependencies: composer install"
echo "2. Install NPM packages: npm install"
echo "3. Build assets: npm run dev"
echo "4. Run migrations: php artisan migrate"
echo "5. Seed database: php artisan db:seed"
echo "6. Start server: php artisan serve"
echo ""
echo "🔑 Default admin credentials:"
echo "Email: admin@example.com"
echo "Password: password"
echo ""
echo "📚 For more information, see:"
echo "- ENVIRONMENT_SETUP.md"
echo "- RUNNING_GUIDE.md"
echo "- SAIL.md"
