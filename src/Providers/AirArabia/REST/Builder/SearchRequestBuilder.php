<?php

namespace App\Providers\AirArabia\REST\Builder;

use App\Models\SearchRequest;

class SearchRequestBuilder
{
    public static function build(SearchRequest $searchRequest): array
    {
        return [
            'searchOnds' => self::buildSearchOnds($searchRequest),
            'paxCounts' => self::buildPaxCounts($searchRequest),
            'currencyCode' => 'AED',
            'cabinClass' => 'Y',
        ];
    }

    private static function buildPaxCounts(SearchRequest $searchRequest): array
    {
        return [
            ['paxType' => 'ADT', 'count' => $searchRequest->getAdultCount() ?? 1],
            ['paxType' => 'CHD', 'count' => $searchRequest->getChildCount() ?? 0],
            ['paxType' => 'INF', 'count' => $searchRequest->getInfantCount() ?? 0],
        ];
    }

    private static function buildSearchOnds(SearchRequest $searchRequest): array
    {
        $searchOnds = [
            [
                'origin' => [
                    'code' => $searchRequest->getFrom(),
                    'locationType' => 'AIRPORT',
                ],
                'destination' => [
                    'code' => $searchRequest->getTo(),
                    'locationType' => 'AIRPORT',
                ],
                'searchStartDate' => $searchRequest->getDepartureDate() ?? date('Y-m-d'),
                'searchEndDate' => $searchRequest->getDepartureDate() ?? date('Y-m-d'),
                'cabinClass' => 'Y',
                'bookingType' => 'NORMAL',
                'interlineQuoteDetails' => new \stdClass(),
            ]
        ];

        // If return date is set, add a return search OND
        if ($searchRequest->getReturnDate() !== null && $searchRequest->getReturnDate() !== '') {
            $searchOnds[] = [
                'origin' => [
                    'code' => $searchRequest->getTo(),
                    'locationType' => 'AIRPORT',
                ],
                'destination' => [
                    'code' => $searchRequest->getFrom(),
                    'locationType' => 'AIRPORT',
                ],
                'searchStartDate' => $searchRequest->getReturnDate(),
                'searchEndDate' => $searchRequest->getReturnDate(),
                'cabinClass' => 'Y',
                'bookingType' => 'NORMAL',
                'interlineQuoteDetails' => new \stdClass(),
            ];
        }
        return $searchOnds;
    }

}