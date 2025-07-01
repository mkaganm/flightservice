<?php
namespace App;

class Router
{
    public function run()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];

        // Settings
        $settings = [
            'host' => $_SERVER['HTTP_HOST'] ?? 'localhost',
            'port' => $_SERVER['SERVER_PORT'] ?? '8000',
            'base_path' => dirname($_SERVER['SCRIPT_NAME']),
            'protocol' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http',
        ];

        if ($uri === '/search' && $method === 'POST') {
            $controller = new \App\Controllers\SearchController();
            $controller->handleSearch();
            return;
        }

        // 404 Not Found
        http_response_code(404);
        echo json_encode(['error' => 'Not Found']);
    }
}
