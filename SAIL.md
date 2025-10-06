# Laravel Sail Documentation

## Overview

Laravel Sail is a lightweight command-line interface for interacting with Laravel's default Docker development environment. Sail provides a great starting point for building a Laravel application using PHP, MySQL, and Redis without requiring prior Docker experience.

## Prerequisites

- Docker Desktop installed and running
- Git (for version control)

## Getting Started

### 1. Install Laravel Sail

```bash
# Install Sail via Composer
composer require laravel/sail --dev

# Publish Sail's docker-compose.yml file
php artisan sail:install
```

### 2. Start the Application

```bash
# Start all services
./vendor/bin/sail up

# Start in background (detached mode)
./vendor/bin/sail up -d

# Stop services
./vendor/bin/sail down
```

### 3. Common Sail Commands

#### Application Commands

```bash
# Run Artisan commands
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan tinker
./vendor/bin/sail artisan queue:work

# Run Composer commands
./vendor/bin/sail composer install
./vendor/bin/sail composer require package/name

# Run NPM commands
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev
./vendor/bin/sail npm run build
```

#### Database Commands

```bash
# Access MySQL database
./vendor/bin/sail mysql

# Run database migrations
./vendor/bin/sail artisan migrate

# Seed the database
./vendor/bin/sail artisan db:seed

# Fresh migration with seeding
./vendor/bin/sail artisan migrate:fresh --seed
```

#### Testing Commands

```bash
# Run PHPUnit tests
./vendor/bin/sail test

# Run tests with coverage
./vendor/bin/sail test --coverage
```

#### Container Management

```bash
# List running containers
./vendor/bin/sail ps

# View container logs
./vendor/bin/sail logs

# Execute commands in container
./vendor/bin/sail shell

# Restart services
./vendor/bin/sail restart
```

## Project Configuration

### Environment Variables (.env)

```env
APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:your-app-key-here
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=sail
DB_PASSWORD=password

MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### Docker Services

- **Laravel App**: PHP 8.2 with Laravel framework
- **MySQL**: Database server
- **Redis**: Cache and session storage
- **Mailhog**: Email testing
- **Selenium**: Browser testing (optional)

## Development Workflow

### 1. Initial Setup

```bash
# Clone the repository
git clone <repository-url>
cd <project-directory>

# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
./vendor/bin/sail artisan key:generate

# Run migrations
./vendor/bin/sail artisan migrate

# Seed database
./vendor/bin/sail artisan db:seed
```

### 2. Daily Development

```bash
# Start services
./vendor/bin/sail up -d

# Run development server
./vendor/bin/sail npm run dev

# Access application
# http://localhost
```

### 3. Database Management

```bash
# Create new migration
./vendor/bin/sail artisan make:migration create_table_name

# Create model with migration
./vendor/bin/sail artisan make:model ModelName -m

# Rollback migrations
./vendor/bin/sail artisan migrate:rollback

# Reset database
./vendor/bin/sail artisan migrate:fresh --seed
```

## Troubleshooting

### Common Issues

#### Port Conflicts

```bash
# If port 80 is in use, use a different port
./vendor/bin/sail up --port=8000
```

#### Permission Issues

```bash
# Fix file permissions
sudo chown -R $USER:$USER .
chmod -R 755 storage bootstrap/cache
```

#### Container Issues

```bash
# Rebuild containers
./vendor/bin/sail build --no-cache

# Remove all containers and volumes
./vendor/bin/sail down -v
```

#### Database Connection Issues

```bash
# Check if MySQL is running
./vendor/bin/sail ps

# Restart MySQL service
./vendor/bin/sail restart mysql
```

### Useful Commands

#### Debugging

```bash
# View container logs
./vendor/bin/sail logs laravel.test

# Access container shell
./vendor/bin/sail shell

# Check service status
./vendor/bin/sail ps
```

#### Maintenance

```bash
# Clear application cache
./vendor/bin/sail artisan cache:clear

# Clear configuration cache
./vendor/bin/sail artisan config:clear

# Clear route cache
./vendor/bin/sail artisan route:clear

# Clear view cache
./vendor/bin/sail artisan view:clear
```

## Production Considerations

### Security

- Change default passwords
- Use strong database credentials
- Enable SSL/TLS for database connections
- Regular security updates

### Performance

- Optimize Docker images
- Use production-ready database configurations
- Enable query caching
- Use Redis for session storage

### Monitoring

- Set up logging
- Monitor resource usage
- Database performance monitoring
- Application health checks

## Additional Resources

- [Laravel Sail Documentation](https://laravel.com/docs/sail)
- [Docker Documentation](https://docs.docker.com/)
- [Laravel Documentation](https://laravel.com/docs)
- [MySQL Documentation](https://dev.mysql.com/doc/)

## Support

For issues related to:

- **Laravel Sail**: Check Laravel documentation
- **Docker**: Check Docker documentation
- **Database**: Check MySQL documentation
- **Application**: Check Laravel documentation
