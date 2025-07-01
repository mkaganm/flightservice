<?php
require_once __DIR__ . '/../vendor/autoload.php';

// Load .env if available
if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
    $dotenv->load();
}

use App\Router;

$router = new Router();
$router->run();
