<?php

/**
 * Environment Validation Script
 * Validates that all required environment variables are set correctly
 */

// Load environment variables from .env file
if (file_exists('.env')) {
    $lines = file('.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && !str_starts_with(trim($line), '#')) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value, '"\'');
        }
    }
}

echo "🔍 Laravel Environment Validation\n";
echo "==================================\n\n";

$errors = [];
$warnings = [];

// Required variables
$required = [
    'APP_NAME' => 'Application name',
    'APP_ENV' => 'Application environment',
    'APP_KEY' => 'Application key',
    'APP_URL' => 'Application URL',
    'DB_CONNECTION' => 'Database connection',
    'DB_DATABASE' => 'Database name',
];

// Check required variables
foreach ($required as $key => $description) {
    $value = $_ENV[$key] ?? null;

    if (empty($value)) {
        $errors[] = "❌ Missing required variable: $key ($description)";
    } else {
        echo "✅ $key: " . (strlen($value) > 50 ? substr($value, 0, 50) . '...' : $value) . "\n";
    }
}

// Check APP_KEY format
if (!empty($_ENV['APP_KEY'])) {
    if (!str_starts_with($_ENV['APP_KEY'], 'base64:')) {
        $errors[] = "❌ APP_KEY should start with 'base64:'";
    } elseif (strlen($_ENV['APP_KEY']) < 40) {
        $errors[] = "❌ APP_KEY appears to be too short";
    }
}

// Check database configuration
$db_connection = $_ENV['DB_CONNECTION'] ?? '';
switch ($db_connection) {
    case 'sqlite':
        $db_path = $_ENV['DB_DATABASE'] ?? '';
        if (!file_exists($db_path)) {
            $warnings[] = "⚠️  SQLite database file does not exist: $db_path";
        }
        break;

    case 'mysql':
        $required_mysql = ['DB_HOST', 'DB_PORT', 'DB_USERNAME', 'DB_PASSWORD'];
        foreach ($required_mysql as $key) {
            if (empty($_ENV[$key])) {
                $errors[] = "❌ Missing MySQL configuration: $key";
            }
        }
        break;

    case 'pgsql':
        $required_pgsql = ['DB_HOST', 'DB_PORT', 'DB_USERNAME', 'DB_PASSWORD'];
        foreach ($required_pgsql as $key) {
            if (empty($_ENV[$key])) {
                $errors[] = "❌ Missing PostgreSQL configuration: $key";
            }
        }
        break;
}

// Check environment-specific settings
$app_env = $_ENV['APP_ENV'] ?? '';
switch ($app_env) {
    case 'production':
        if ($_ENV['APP_DEBUG'] === 'true') {
            $warnings[] = "⚠️  APP_DEBUG should be false in production";
        }
        if (str_contains($_ENV['APP_URL'] ?? '', 'localhost')) {
            $warnings[] = "⚠️  APP_URL should not contain localhost in production";
        }
        break;

    case 'local':
        if ($_ENV['APP_DEBUG'] !== 'true') {
            $warnings[] = "⚠️  APP_DEBUG should be true in local development";
        }
        break;
}

// Check security settings
if ($_ENV['APP_ENV'] === 'production') {
    if (empty($_ENV['SESSION_ENCRYPT']) || $_ENV['SESSION_ENCRYPT'] !== 'true') {
        $warnings[] = "⚠️  SESSION_ENCRYPT should be true in production";
    }

    if (empty($_ENV['MAIL_FROM_ADDRESS']) || $_ENV['MAIL_FROM_ADDRESS'] === 'hello@example.com') {
        $warnings[] = "⚠️  MAIL_FROM_ADDRESS should be set to your domain email";
    }
}

// Check cache configuration
$cache_store = $_ENV['CACHE_STORE'] ?? 'file';
if ($cache_store === 'redis' && empty($_ENV['REDIS_HOST'])) {
    $warnings[] = "⚠️  REDIS_HOST not set but CACHE_STORE is redis";
}

// Check session configuration
$session_driver = $_ENV['SESSION_DRIVER'] ?? 'file';
if ($session_driver === 'database' && $db_connection === 'sqlite') {
    $warnings[] = "⚠️  Using database sessions with SQLite may impact performance";
}

// Display results
echo "\n📊 Validation Results\n";
echo "=====================\n";

if (empty($errors) && empty($warnings)) {
    echo "🎉 All checks passed! Your environment is properly configured.\n";
} else {
    if (!empty($errors)) {
        echo "\n❌ Errors (must be fixed):\n";
        foreach ($errors as $error) {
            echo "   $error\n";
        }
    }

    if (!empty($warnings)) {
        echo "\n⚠️  Warnings (recommended to fix):\n";
        foreach ($warnings as $warning) {
            echo "   $warning\n";
        }
    }
}

// Database connection test
echo "\n🔗 Database Connection Test\n";
echo "===========================\n";

try {
    $config = [
        'driver' => $db_connection,
        'database' => $_ENV['DB_DATABASE'],
    ];

    if ($db_connection === 'mysql') {
        $config['host'] = $_ENV['DB_HOST'];
        $config['port'] = $_ENV['DB_PORT'];
        $config['username'] = $_ENV['DB_USERNAME'];
        $config['password'] = $_ENV['DB_PASSWORD'];
    } elseif ($db_connection === 'pgsql') {
        $config['host'] = $_ENV['DB_HOST'];
        $config['port'] = $_ENV['DB_PORT'];
        $config['username'] = $_ENV['DB_USERNAME'];
        $config['password'] = $_ENV['DB_PASSWORD'];
    }

    $pdo = new PDO(
        $db_connection === 'sqlite' ? "sqlite:{$config['database']}" :
            "{$db_connection}:host={$config['host']};port={$config['port']};dbname={$config['database']}",
        $config['username'] ?? null,
        $config['password'] ?? null
    );

    echo "✅ Database connection successful\n";

    // Test if migrations table exists
    if ($db_connection === 'sqlite') {
        $stmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='migrations'");
        if ($stmt->fetch()) {
            echo "✅ Migrations table exists\n";
        } else {
            echo "⚠️  Migrations table not found - run 'php artisan migrate'\n";
        }
    } else {
        $stmt = $pdo->query("SHOW TABLES LIKE 'migrations'");
        if ($stmt->fetch()) {
            echo "✅ Migrations table exists\n";
        } else {
            echo "⚠️  Migrations table not found - run 'php artisan migrate'\n";
        }
    }
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
}

// Performance recommendations
echo "\n💡 Performance Recommendations\n";
echo "===============================\n";

if ($db_connection === 'sqlite') {
    echo "ℹ️  SQLite is good for development but consider MySQL/PostgreSQL for production\n";
}

if (($_ENV['CACHE_STORE'] ?? 'file') === 'file') {
    echo "ℹ️  Consider using Redis for better cache performance\n";
}

if ($_ENV['SESSION_DRIVER'] === 'file') {
    echo "ℹ️  Consider using database sessions for better performance\n";
}

echo "\n📚 For more information, see ENVIRONMENT_SETUP.md\n";
