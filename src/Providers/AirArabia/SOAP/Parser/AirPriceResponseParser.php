<?php

namespace App\Providers\AirArabia\SOAP\Parser;

class AirPriceResponseParser
{
    /**
     * Parse the OTA_AirPriceRS SOAP response array into a structured PHP array
     *
     * @param array $response The decoded SOAP response (array or stdClass)
     * @return array Parsed flight pricing data
     */
    public static function parse($response): array
    {
        // Convert stdClass to array if needed
        $response = json_decode(json_encode($response), true);
        $result = [
            'EchoToken' => $response['@attributes']['EchoToken'] ?? null,
            'PrimaryLangID' => $response['@attributes']['PrimaryLangID'] ?? null,
            'RetransmissionIndicator' => $response['@attributes']['RetransmissionIndicator'] ?? null,
            'SequenceNmbr' => $response['@attributes']['SequenceNmbr'] ?? null,
            'TransactionIdentifier' => $response['@attributes']['TransactionIdentifier'] ?? null,
            'Version' => $response['@attributes']['Version'] ?? null,
            'PricedItineraries' => [],
            'Success' => isset($response['Success']),
            'Errors' => $response['Errors'] ?? null,
        ];

        if (!empty($response['PricedItineraries']['PricedItinerary'])) {
            $itineraries = $response['PricedItineraries']['PricedItinerary'];
            if (isset($itineraries['AirItinerary'])) {
                // Only one itinerary, wrap in array
                $itineraries = [$itineraries];
            }
            foreach ($itineraries as $itinerary) {
                $parsedItinerary = [
                    'SequenceNumber' => $itinerary['@attributes']['SequenceNumber'] ?? null,
                    'AirItinerary' => self::parseAirItinerary($itinerary['AirItinerary'] ?? []),
                    'AirItineraryPricingInfo' => self::parsePricingInfo($itinerary['AirItineraryPricingInfo'] ?? []),
                ];
                $result['PricedItineraries'][] = $parsedItinerary;
            }
        }
        return $result;
    }

    private static function parseAirItinerary(array $airItinerary): array
    {
        $result = [
            'OriginDestinationOptions' => []
        ];
        if (!empty($airItinerary['OriginDestinationOptions']['OriginDestinationOption'])) {
            $odo = $airItinerary['OriginDestinationOptions']['OriginDestinationOption'];
            if (isset($odo['FlightSegment'])) {
                $odo = [$odo];
            }
            foreach ($odo as $option) {
                $segments = [];
                if (!empty($option['FlightSegment'])) {
                    $flightSegments = $option['FlightSegment'];
                    if (isset($flightSegments['@attributes'])) {
                        $flightSegments = [$flightSegments];
                    }
                    foreach ($flightSegments as $segment) {
                        $segments[] = [
                            'ArrivalDateTime' => $segment['@attributes']['ArrivalDateTime'] ?? null,
                            'DepartureDateTime' => $segment['@attributes']['DepartureDateTime'] ?? null,
                            'FlightNumber' => $segment['@attributes']['FlightNumber'] ?? null,
                            'RPH' => $segment['@attributes']['RPH'] ?? null,
                            'returnFlag' => $segment['@attributes']['returnFlag'] ?? null,
                            'DepartureAirport' => $segment['DepartureAirport']['@attributes'] ?? [],
                            'ArrivalAirport' => $segment['ArrivalAirport']['@attributes'] ?? [],
                        ];
                    }
                }
                $result['OriginDestinationOptions'][] = [
                    'FlightSegments' => $segments
                ];
            }
        }
        // Parse bundled services if present
        if (!empty($airItinerary['OriginDestinationOptions']['AABundledServiceExt'])) {
            $bundledExt = $airItinerary['OriginDestinationOptions']['AABundledServiceExt'];
            if (isset($bundledExt['bundledService'])) {
                $bundledExt = [$bundledExt];
            }
            $result['AABundledServiceExt'] = [];
            foreach ($bundledExt as $ext) {
                $services = $ext['bundledService'] ?? [];
                if (isset($services['bunldedServiceId'])) {
                    $services = [$services];
                }
                $parsedServices = [];
                foreach ($services as $service) {
                    $parsedServices[] = [
                        'bunldedServiceId' => $service['bunldedServiceId'] ?? null,
                        'bundledServiceName' => $service['bundledServiceName'] ?? null,
                        'perPaxBundledFee' => $service['perPaxBundledFee'] ?? null,
                        'bookingClasses' => $service['bookingClasses'] ?? null,
                        'description' => $service['description'] ?? null,
                        'includedServies' => (array)($service['includedServies'] ?? []),
                    ];
                }
                $result['AABundledServiceExt'][] = [
                    'applicableOnd' => $ext['@attributes']['applicableOnd'] ?? null,
                    'applicableOndSequence' => $ext['@attributes']['applicableOndSequence'] ?? null,
                    'bundledService' => $parsedServices,
                ];
            }
        }
        return $result;
    }

    private static function parsePricingInfo(array $pricingInfo): array
    {
        $result = [
            'PricingSource' => $pricingInfo['@attributes']['PricingSource'] ?? null,
            'ItinTotalFare' => [],
            'PTC_FareBreakdowns' => [],
            'AvailableFlexiFares' => [],
        ];
        if (!empty($pricingInfo['ItinTotalFare'])) {
            $result['ItinTotalFare'] = $pricingInfo['ItinTotalFare'];
        }
        if (!empty($pricingInfo['PTC_FareBreakdowns']['PTC_FareBreakdown'])) {
            $breakdowns = $pricingInfo['PTC_FareBreakdowns']['PTC_FareBreakdown'];
            if (isset($breakdowns['PassengerTypeQuantity'])) {
                $breakdowns = [$breakdowns];
            }
            foreach ($breakdowns as $bd) {
                $result['PTC_FareBreakdowns'][] = $bd;
            }
        }
        if (!empty($pricingInfo['AvailableFlexiFares']['FlexiFare'])) {
            $flexiFares = $pricingInfo['AvailableFlexiFares']['FlexiFare'];
            if (isset($flexiFares['FlexiFareAmount'])) {
                $flexiFares = [$flexiFares];
            }
            foreach ($flexiFares as $fare) {
                $result['AvailableFlexiFares'][] = $fare;
            }
        }
        return $result;
    }
}