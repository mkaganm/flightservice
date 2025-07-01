<?php

namespace App\Providers\AirArabia\REST\Parser;

use App\Models\CabinPrices;
use App\Models\Flight;
use App\Models\PaxPrices;
use App\Models\SearchRequest;
use App\Models\Segment;

class SearchResponseParser
{
    /**
     * @param array $response
     * @param SearchRequest $sr
     * @return Flight[]
     */
    public static function parse(array $response, SearchRequest $sr): array
    {
        $ondWiseFlightCombinations = $response['ondWiseFlightCombinations'] ?? [];

        $flights = [];

        $flights = self::parseOneWay($sr, $ondWiseFlightCombinations);

        // If the search request is for a round trip, parse the return flight as well
        if ($sr->isRoundTrip()) {
            $flights = array_merge($flights, self::parseRoundTrip($sr, $ondWiseFlightCombinations));
        }

        return $flights;
    }

    /**
     * Parses one-way flights from the response.
     *
     * @param SearchRequest $sr
     * @param array $ondWiseFlightCombinations
     * @return Flight[]
     */
    private static function parseOneWay(SearchRequest $sr, array $ondWiseFlightCombinations): array
    {
        $oneWayFlights = [];

        $routeCode = $sr->getFrom() . '/' . $sr->getTo();

        $routeProviderFlights = $ondWiseFlightCombinations[$routeCode] ?? [];
        $dateFlights = $routeProviderFlights['dateWiseFlightCombinations'][$sr->getDepartureDate()] ?? [];
        $providerFlights = $dateFlights['flightOptions'] ?? [];

        foreach ($providerFlights as $providerFlight) {
            // Check if the flight is available continuing to the next flight if not
            if ($providerFlight['availabilityStatus'] !== 'AVAILABLE') {
                continue;
            }

            $flight = new Flight();
            $flight->setSegments(self::parseSegments($providerFlight));
            $flight->setCabinPrices(self::parseCabinPrices($providerFlight,$routeCode));

            $oneWayFlights[] = $flight;
        }

        return $oneWayFlights;
    }

    /**
     * Parses round trip flights from the response.
     *
     * @param SearchRequest $sr
     * @param array $response
     * @return array
     */
    private static function parseRoundTrip(SearchRequest $sr, array $ondWiseFlightCombinations): array
    {
        $returnFlights = [];

        $routeCode = $sr->getTo() . '/' . $sr->getFrom();

        $routeProviderFlights = $ondWiseFlightCombinations[$routeCode] ?? [];

        $dateFlights = $routeProviderFlights['dateWiseFlightCombinations'][$sr->getReturnDate()] ?? [];

        $providerFlights = $dateFlights['flightOptions'] ?? [];

        foreach ($providerFlights as $providerFlight) {

            // Check if the flight is available continuing to the next flight if not
            if ($providerFlight['availabilityStatus'] !== 'AVAILABLE') {

                continue;
            }

            $flight = new Flight();
            $flight->setSegments(self::parseSegments($providerFlight));
            $flight->setCabinPrices(self::parseCabinPrices($providerFlight,$routeCode));

            $returnFlights[] = $flight;
        }

        return $returnFlights;
    }

    /**
     * Parses segments from the response.
     *
     * @param array $providerFlight
     * @return Segment[]
     */
    private static function parseSegments(array $providerFlight) :array{
        /**
         * @var Segment[] $segments
         */
        $segments = [];

        $providerSegments = $providerFlight['flightSegments'] ?? [];

        foreach ($providerSegments as $providerSegment) {
            $segment = new Segment();

            $segment->setFlightNumber($providerSegment['flightNumber']);
            $segment->setOrigin($providerSegment['origin']['airportCode'] ?? '');
            $segment->setOriginCountryCode($providerSegment['origin']['countryCode'] ?? '');
            $segment->setOriginTerminal($providerSegment['origin']['terminal'] ?? '');

            $segment->setDestination($providerSegment['destination']['airportCode'] ?? '');
            $segment->setDestinationCountryCode($providerSegment['destination']['countryCode'] ?? '');
            $segment->setDestinationTerminal($providerSegment['destination']['terminal'] ?? '');

            $segment->setDepartureTime($providerSegment['departureDateTimeLocal']);
            $segment->setArrivalTime($providerSegment['arrivalDateTimeLocal'] ?? '');

            $segment->setSegmentCode($providerSegment['segmentCode'] ?? '');

            $segments[] = $segment;
        }

        return $segments;
    }

    /**
     * Parses cabin prices from the response.
     *
     * @param string $routeCode
     * @return CabinPrices[]
     */
    private static function parseCabinPrices(array $providerFlight, string $routeCode) :array   {

        /**
         * @var CabinPrices[] $cabinPrices
         */
        $cabinPrices = [];

        $providerCabinPrices = $providerFlight['cabinPrices'] ?? [];

        foreach ($providerCabinPrices as $providerCabinPrice) {
            $cabinPrice = new CabinPrices();

            $cabinPrice->setCabinClass($providerCabinPrice['cabinClass'] ?? '');
            $cabinPrice->setFareFamily($providerCabinPrice['fareFamily'] ?? '');
            $cabinPrice->setPrice($providerCabinPrice['price']);

            $cabinPrice->setClassCodeKey($routeCode);
            $cabinPrice->setClassCodeValue($providerCabinPrice['fareOndWiseBookingClassCodes'][$routeCode] ?? '');

            $paxPrices = self::parsePaxPrices($providerCabinPrice['paxTypeWiseBasePrices']);
            $cabinPrice->setPaxPrices($paxPrices);

            $cabinPrices[] = $cabinPrice;
        }

        return $cabinPrices;
    }

    /**
     * Parses passenger prices from the response.
     *
     * @param array $paxTypeWiseBasePrices
     * @return PaxPrices[]
     */
    private static function parsePaxPrices(array $paxTypeWiseBasePrices):array
    {
        /**
         * @var PaxPrices[] $paxPrices
         */
        $paxPrices = [];

        foreach ($paxTypeWiseBasePrices as $paxTypeWiseBasePrice) {
            $paxPrice = new PaxPrices();

            $paxPrice->setPaxPrice($paxTypeWiseBasePrice['price']);
            $paxPrice->setPaxCode($paxTypeWiseBasePrice['paxType']);

            $paxPrices[] = $paxPrice;
        }

        return $paxPrices;
    }

}