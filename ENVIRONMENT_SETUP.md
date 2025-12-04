# Environment Setup Guide

## 📋 **Environment Variables Configuration**

This guide explains how to set up your Laravel application environment variables for different deployment scenarios.

---

## 🚀 **Quick Setup**

### **1. Copy Environment File**

```bash
cp .env.example .env
```

### **2. Generate Application Key**

```bash
php artisan key:generate
```

### **3. Configure Database**

Choose your database configuration based on your setup:

---

## 🗄️ **Database Configurations**

### **SQLite (Default - No Setup Required)**

```env
DB_CONNECTION=sqlite
DB_DATABASE=/path/to/your/database/database.sqlite
```

### **MySQL (Local Development)**

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=your_password
```

### **MySQL (Laravel Sail)**

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=sail
DB_PASSWORD=password
```

### **PostgreSQL**

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=your_password
```

---

## 🔧 **Environment-Specific Configurations**

### **Local Development**

```env
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
LOG_LEVEL=debug
```

### **Staging**

```env
APP_ENV=staging
APP_DEBUG=false
APP_URL=https://staging.yourdomain.com
LOG_LEVEL=info
```

### **Production**

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
LOG_LEVEL=error
```

---

## 🐳 **Laravel Sail Configuration**

### **Docker Environment Variables**

```env
# Sail Configuration
WWWGROUP=1000
WWWUSER=1000
SAIL_XDEBUG_MODE=develop,debug
SAIL_XDEBUG_CONFIG="client_host=host.docker.internal"

# MySQL for Sail
MYSQL_ROOT_PASSWORD=password
MYSQL_DATABASE=laravel
MYSQL_USER=sail
MYSQL_PASSWORD=password

# Redis for Sail
REDIS_HOST=redis
REDIS_PORT=6379

# Mail for Sail
MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
```

---

## 📧 **Mail Configuration**

### **Log Driver (Development)**

```env
MAIL_MAILER=log
```

### **SMTP (Production)**

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### **Mailhog (Sail)**

```env
MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
```

---

## 🗃️ **Cache Configuration**

### **Database Cache (Default)**

```env
CACHE_STORE=database
```

### **Redis Cache**

```env
CACHE_STORE=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_PASSWORD=null
```

### **File Cache**

```env
CACHE_STORE=file
```

---

## 🔐 **Security Configuration**

### **Session Security**

```env
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=true
SESSION_DOMAIN=yourdomain.com
```

### **Sanctum Configuration**

```env
SANCTUM_STATEFUL_DOMAINS=localhost,127.0.0.1,yourdomain.com
```

### **Rate Limiting**

```env
RATE_LIMIT_PER_MINUTE=60
RATE_LIMIT_BURST=100
```

---

## 🔍 **Debugging Configuration**

### **Development**

```env
APP_DEBUG=true
LOG_LEVEL=debug
DEVELOPMENT_MODE=true
DEBUG_BAR_ENABLED=true
QUERY_LOG_ENABLED=true
```

### **Production**

```env
APP_DEBUG=false
LOG_LEVEL=error
DEVELOPMENT_MODE=false
DEBUG_BAR_ENABLED=false
QUERY_LOG_ENABLED=false
```

---

## 📱 **Third-Party Services**

### **Social Authentication**

```env
# Google OAuth
GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-client-secret
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback

# Facebook OAuth
FACEBOOK_CLIENT_ID=your-facebook-client-id
FACEBOOK_CLIENT_SECRET=your-facebook-client-secret
FACEBOOK_REDIRECT_URI=http://localhost:8000/auth/facebook/callback

# GitHub OAuth
GITHUB_CLIENT_ID=your-github-client-id
GITHUB_CLIENT_SECRET=your-github-client-secret
GITHUB_REDIRECT_URI=http://localhost:8000/auth/github/callback
```

### **Payment Gateways**

```env
# Stripe
STRIPE_KEY=pk_test_your-stripe-key
STRIPE_SECRET=sk_test_your-stripe-secret
STRIPE_WEBHOOK_SECRET=whsec_your-webhook-secret

# PayPal
PAYPAL_CLIENT_ID=your-paypal-client-id
PAYPAL_CLIENT_SECRET=your-paypal-client-secret
PAYPAL_MODE=sandbox
```

### **SMS Services**

```env
# Twilio
TWILIO_SID=your-twilio-sid
TWILIO_TOKEN=your-twilio-token
TWILIO_FROM=+1234567890
```

### **Push Notifications**

```env
# Firebase
FCM_SERVER_KEY=your-fcm-server-key
FCM_SENDER_ID=your-fcm-sender-id
```

---

## 📊 **Analytics & Monitoring**

### **Google Analytics**

```env
GOOGLE_ANALYTICS_ID=GA-XXXXXXXXX-X
```

### **Facebook Pixel**

```env
FACEBOOK_PIXEL_ID=your-pixel-id
```

### **Sentry Monitoring**

```env
SENTRY_LARAVEL_DSN=https://your-sentry-dsn@sentry.io/project-id
SENTRY_TRACES_SAMPLE_RATE=1.0
```

---

## 🚀 **Deployment Configurations**

### **Heroku**

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-app.herokuapp.com
DB_CONNECTION=pgsql
DB_HOST=your-heroku-db-host
DB_PORT=5432
DB_DATABASE=your-heroku-db-name
DB_USERNAME=your-heroku-db-user
DB_PASSWORD=your-heroku-db-password
```

### **AWS**

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
DB_CONNECTION=mysql
DB_HOST=your-rds-endpoint
DB_PORT=3306
DB_DATABASE=your-database
DB_USERNAME=your-username
DB_PASSWORD=your-password
AWS_ACCESS_KEY_ID=your-aws-access-key
AWS_SECRET_ACCESS_KEY=your-aws-secret-key
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your-s3-bucket
```

### **DigitalOcean**

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
DB_CONNECTION=mysql
DB_HOST=your-droplet-ip
DB_PORT=3306
DB_DATABASE=your-database
DB_USERNAME=your-username
DB_PASSWORD=your-password
```

---

## 🔧 **Environment Validation**

### **Required Variables**

- `APP_NAME`
- `APP_ENV`
- `APP_KEY`
- `APP_URL`
- `DB_CONNECTION`
- `DB_DATABASE`

### **Optional Variables**

- `APP_DEBUG`
- `LOG_LEVEL`
- `CACHE_STORE`
- `SESSION_DRIVER`
- `QUEUE_CONNECTION`

---

## 🛠️ **Environment Commands**

### **Check Environment**

```bash
php artisan env
```

### **Clear Configuration Cache**

```bash
php artisan config:clear
php artisan config:cache
```

### **Check Database Connection**

```bash
php artisan migrate:status
```

### **Test Mail Configuration**

```bash
php artisan tinker
Mail::raw('Test email', function($message) {
    $message->to('test@example.com')->subject('Test');
});
```

---

## 📝 **Environment File Templates**

### **Development Template**

```env
APP_NAME="Laravel Admin"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
DB_CONNECTION=sqlite
DB_DATABASE=/path/to/database.sqlite
```

### **Production Template**

```env
APP_NAME="Laravel Admin"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_PORT=3306
DB_DATABASE=your-database
DB_USERNAME=your-username
DB_PASSWORD=your-password
```

---

## 🔒 **Security Best Practices**

1. **Never commit `.env` files to version control**
2. **Use strong, unique passwords**
3. **Enable encryption for sensitive data**
4. **Use environment-specific configurations**
5. **Regularly rotate API keys and secrets**
6. **Use HTTPS in production**
7. **Set appropriate file permissions (600 for .env)**

---

## 📚 **Additional Resources**

- [Laravel Environment Configuration](https://laravel.com/docs/configuration#environment-configuration)
- [Laravel Sail Documentation](https://laravel.com/docs/sail)
- [Laravel Security](https://laravel.com/docs/security)
- [Environment Variables Best Practices](https://12factor.net/config)
