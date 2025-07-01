<?php
use Dotenv\Dotenv;

require_once __DIR__ . '/vendor/autoload.php';

// Load .env if available
if (file_exists(__DIR__ . '/.env')) {
    require_once __DIR__ . '/vendor/autoload.php';
    Dotenv::createImmutable(__DIR__)->safeLoad();
}

use App\Providers\AirArabia\REST\Services\AuthService;

$authService = new AuthService();
$accessToken = $authService->authenticate();

if ($accessToken) {
    echo "Access Token: " . $accessToken . PHP_EOL;
} else {
    echo "Authentication failed." . PHP_EOL;
}
