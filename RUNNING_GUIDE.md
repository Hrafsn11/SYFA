# Laravel Application Running Guide

## 🚀 **Multiple Ways to Run This Laravel Application**

You have several options to run this Laravel application. Choose the one that best fits your development environment:

---

## **Option 1: Traditional PHP Development Server (Recommended for Local Development)**

### Prerequisites:

- PHP 8.1+ installed
- Composer installed
- Node.js and NPM installed

### Steps:

```bash
# 1. Install dependencies
composer install
npm install

# 2. Build frontend assets
npm run dev

# 3. Start the PHP development server
php artisan serve

# 4. Access the application
# Open: http://localhost:8000
```

### Database Configuration:

- **Current:** SQLite (no setup required)
- **File:** `database/database.sqlite`
- **Migrations:** Already run
- **Seeding:** Already completed

---

## **Option 2: Laravel Sail (Docker) - For Containerized Development**

### Prerequisites:

- Docker Desktop installed and running

### Steps:

```bash
# 1. Start Docker containers
./vendor/bin/sail up -d

# 2. Access the application
# Open: http://localhost:8000
```

### Database Configuration:

- **Type:** MySQL 8.0 (in Docker container)
- **Host:** mysql
- **Database:** laravel
- **User:** sail
- **Password:** password

---

## **Option 3: XAMPP/MAMP/WAMP (Local Web Server)**

### Prerequisites:

- XAMPP/MAMP/WAMP installed
- PHP 8.1+ enabled

### Steps:

```bash
# 1. Copy project to web server directory
# XAMPP: C:\xampp\htdocs\syifa
# MAMP: /Applications/MAMP/htdocs/syifa

# 2. Install dependencies
composer install
npm install

# 3. Build assets
npm run dev

# 4. Configure virtual host (optional)
# Access via: http://localhost/syifa or your virtual host
```

---

## **Option 4: Production Deployment**

### For Production:

```bash
# 1. Install dependencies (no dev dependencies)
composer install --optimize-autoloader --no-dev

# 2. Build production assets
npm run build

# 3. Set up web server (Apache/Nginx)
# 4. Configure database (MySQL/PostgreSQL)
# 5. Set up SSL certificates
# 6. Configure environment variables
```

---

## **Current Application Status**

### ✅ **What's Working:**

- **Authentication System:** Login/Register with Laravel Breeze
- **Role Management:** Admin, User roles with Spatie Permission
- **Permission System:** Granular permissions for users, roles, permissions
- **Admin Dashboard:** Complete CRUD for users, roles, permissions
- **Database:** SQLite (local) or MySQL (Docker)
- **Frontend:** Tailwind CSS with Livewire components

### 🔑 **Default Credentials:**

- **Admin Email:** `admin@example.com`
- **Admin Password:** `password`
- **Admin Role:** Super Admin with all permissions

### 📊 **Available Features:**

- User Management (Create, Read, Update, Delete)
- Role Management (Create, Read, Update, Delete)
- Permission Management (Create, Read, Update, Delete)
- Dashboard with statistics
- Responsive design with Tailwind CSS

---

## **Development Commands**

### Traditional PHP Server:

```bash
# Start server
php artisan serve

# Run migrations
php artisan migrate

# Seed database
php artisan db:seed

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Run tests
php artisan test
```

### Laravel Sail (Docker):

```bash
# Start containers
./vendor/bin/sail up -d

# Run commands through Sail
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan db:seed
./vendor/bin/sail test

# Access database
./vendor/bin/sail mysql

# Stop containers
./vendor/bin/sail down
```

---

## **Troubleshooting**

### Common Issues:

#### 1. **Port Already in Use:**

```bash
# Kill process on port 8000
lsof -ti:8000 | xargs kill -9

# Or use different port
php artisan serve --port=8080
```

#### 2. **Database Connection Issues:**

```bash
# Check database file exists
ls -la database/database.sqlite

# Create if missing
touch database/database.sqlite

# Run migrations
php artisan migrate
```

#### 3. **Permission Issues:**

```bash
# Fix storage permissions
chmod -R 755 storage bootstrap/cache

# Fix ownership
sudo chown -R $USER:$USER .
```

#### 4. **Asset Compilation Issues:**

```bash
# Clear and rebuild
npm run build
# or
npm run dev
```

---

## **Environment Configuration**

### Current .env Settings:

```env
APP_NAME=Laravel
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database (SQLite - No setup required)
DB_CONNECTION=sqlite
DB_DATABASE=/path/to/database/database.sqlite

# For MySQL (if using Sail)
# DB_CONNECTION=mysql
# DB_HOST=mysql
# DB_PORT=3306
# DB_DATABASE=laravel
# DB_USERNAME=sail
# DB_PASSWORD=password
```

---

## **Quick Start (Choose Your Method)**

### **For Quick Local Development:**

```bash
php artisan serve
# Visit: http://localhost:8000
# Login: admin@example.com / password
```

### **For Docker Development:**

```bash
./vendor/bin/sail up -d
# Visit: http://localhost:8000
# Login: admin@example.com / password
```

### **For Production:**

- Set up proper web server (Apache/Nginx)
- Configure production database
- Set `APP_ENV=production`
- Set `APP_DEBUG=false`
- Configure SSL certificates

---

## **Support**

- **Laravel Documentation:** https://laravel.com/docs
- **Laravel Sail Documentation:** https://laravel.com/docs/sail
- **Spatie Permission:** https://spatie.be/docs/laravel-permission
- **Livewire Documentation:** https://livewire.laravel.com/docs
