<?php
/**
 * config_example.php
 * Copy this file to config.php and fill the placeholders with your environment values.
 * Do NOT commit config.php with real credentials.
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Hosts considered local — adjust if needed
$localEnvironments = ['localhost', '127.0.0.1', '::1'];
$serverHost = $_SERVER['SERVER_NAME'] ?? $_SERVER['HTTP_HOST'] ?? '';
$isLocal = in_array($serverHost, $localEnvironments);

if ($isLocal) {
    define('IS_PROD', false);

    // Database (development)
    define('DB_HOST', 'localhost');
    define('DB_PORT', '3306');
    define('DB_USER', 'your_db_user');
    define('DB_PWD', 'your_db_password');
    define('DB_NAME', 'your_db_name');

    // Application secret / pepper — replace with a long random string
    define('SEL', 'replace_with_dev_pepper_string');

    // Domain & base URL for links
    define('DOMAIN', 'http://localhost');
    define('HOME_URL', '/');

    // reCAPTCHA keys (optional)
    define('PUBLIC_KEY', '');
    define('SECRET_KEY', '');

    // SMTP (development/test)
    define('HOST', 'smtp.example.dev');
    define('PORT', '1025');
    define('USERNAME', 'smtp_user');
    define('PASSWORD', 'smtp_password');

    define('MAIL_DESTINATAIRE', 'contact@example.dev');
    define('SENDER', 'Le Media Voironnais (dev)');
    define('ADMIN_EMAIL', 'admin@example.dev');
    define('ADMIN_SENDER_NAME', 'Administrateur (dev)');
} else {
    define('IS_PROD', true);

    // Database (production) — FILL BEFORE DEPLOY
    define('DB_HOST', 'prod_db_host');
    define('DB_PORT', '3306');
    define('DB_USER', 'prod_db_user');
    define('DB_PWD', 'prod_db_password');
    define('DB_NAME', 'prod_db_name');

    // Application secret / pepper — use a secure random string
    define('SEL', 'replace_with_production_pepper');

    define('DOMAIN', 'https://your-production-domain.tld');
    define('HOME_URL', '/');

    // reCAPTCHA production keys
    define('PUBLIC_KEY', '');
    define('SECRET_KEY', '');

    // SMTP production — fill with provider credentials
    define('HOST', 'smtp.yourprovider.tld');
    define('PORT', '465'); // or 587
    define('USERNAME', 'smtp_user');
    define('PASSWORD', 'smtp_password');

    define('MAIL_DESTINATAIRE', 'contact@your-production-domain.tld');
    define('SENDER', 'Le Media Voironnais');
    define('ADMIN_EMAIL', 'admin@your-production-domain.tld');
    define('ADMIN_SENDER_NAME', 'Administrateur');
}

/**
 * After copying to config.php:
 * - Replace placeholders with secure values.
 * - Keep config.php out of version control.
 * - Keep config.php out of VCS or use environment variables in production.
 */
