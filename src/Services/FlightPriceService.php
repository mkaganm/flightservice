<?php

namespace App\Services;

use App\Models\Flight;

class FlightPriceService
{
    private static float $feeRate = 0.10; // fixme : this might be configurable in the future

    /**
     * Update the price of each flight by adding a service fee.
     *
     * @param Flight[] $flights
     * @return Flight[]
     */
    public static function updatePrice(array $flights): array
    {
        foreach ($flights as $flight) {
            $flightPrice = $flight->getCabinPrices()[0]->getPrice();
            $flight->setProviderPrice($flightPrice);

            $serviceFee = $flightPrice * self::$feeRate;
            $newPrice = $flightPrice + $serviceFee;
            $flight->setFlightPrice($newPrice);
        }
        return $flights;
    }

}
