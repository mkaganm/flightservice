<?php

namespace App\Services;

use App\Models\SearchRequest;
use App\Providers\AirArabia\REST\Services\SearchService;

class FlightSearchService
{
    public function search(SearchRequest $searchRequest): array
    {
        $searchService = new SearchService();

        $flights = $searchService->searchFlights($searchRequest);

        $flights = FlightPriceService::updatePrice($flights);

        return $flights;
    }
}