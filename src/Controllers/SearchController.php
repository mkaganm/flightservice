<?php
namespace App\Controllers;

use App\Models\SearchRequest;
use App\Services\FlightSearchService;

class SearchController
{
    public function handleSearch()
    {
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid JSON input']);
            return;
        }

        $searchRequest = new SearchRequest();
        $searchRequest->setFrom($input['from'] ?? null);
        $searchRequest->setTo($input['to'] ?? null);
        $searchRequest->setDepartureDate($input['departureDate'] ?? null);
        $searchRequest->setReturnDate($input['returnDate'] ?? null);
        $searchRequest->setAdultCount($input['adultCount'] ?? 1);
        $searchRequest->setChildCount($input['childCount'] ?? 0);
        $searchRequest->setInfantCount($input['infantCount'] ?? 0);

        $service = new FlightSearchService();
        $result = $service->search($searchRequest);

        // Dizi ise ve nesne içeriyorsa, array_map ile jsonSerialize uygula
        if (is_array($result) && !empty($result) && is_object($result[0]) && $result[0] instanceof \JsonSerializable) {
            $result = array_map(fn($item) => $item->jsonSerialize(), $result);
        }

        if (ob_get_length()) {
            ob_clean();
        }
        echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        flush();
    }
}
