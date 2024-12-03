<?php
// Charger les variables d'environnement depuis .env
function loadEnv() {
    $envFile = __DIR__ . '/../.env';
    if (file_exists($envFile)) {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                putenv("$key=$value");
                $_ENV[$key] = $value;
                $_SERVER[$key] = $value;
            }
        }
    }
}

// Charger les variables d'environnement
loadEnv();

// Configuration de base de données
define('DB_HOST', getenv('DB_HOST'));
define('DB_NAME', getenv('DB_NAME'));
define('DB_USER', getenv('DB_USER'));
define('DB_PASS', getenv('DB_PASS'));

// Configuration JWT
define('JWT_SECRET', getenv('JWT_SECRET'));
define('JWT_EXPIRATION', getenv('JWT_EXPIRATION'));

// Configuration CORS
define('CORS_ALLOWED_ORIGINS', getenv('CORS_ALLOWED_ORIGINS'));

// Configuration Upload
define('UPLOAD_MAX_SIZE', getenv('UPLOAD_MAX_SIZE'));
define('ALLOWED_FILE_TYPES', getenv('ALLOWED_FILE_TYPES'));
