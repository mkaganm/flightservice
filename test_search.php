<?php
require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;
use App\Models\SearchRequest;
use App\Providers\AirArabia\REST\Services\SearchService;

// Load .env if available
if (file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

// SearchRequest örneği oluştur
$searchRequest = new SearchRequest();
$searchRequest->setFrom('COK');
$searchRequest->setTo('MCT');
$searchRequest->setDepartureDate('2025-08-24');
//$searchRequest->setReturnDate('2025-08-24');
$searchRequest->setAdultCount(1);
$searchRequest->setChildCount(0);
$searchRequest->setInfantCount(0);

$searchService = new SearchService();
$response = $searchService->searchFlights($searchRequest);

echo "SearchService response:\n";
print_r($response);

