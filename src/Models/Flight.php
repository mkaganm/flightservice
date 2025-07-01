<?php
namespace App\Models;

class Flight implements \JsonSerializable
{
    /**
     * @var Segment[] $segments
     */
    private array $segments = [];
    /**
     * @var CabinPrices[] $cabinPrices
     */
    private array $cabinPrices = [];

    // this is flight price with service fee
    private float $flightPrice = 0.0;
    private float $providerPrice = 0.0;

    public function getSegments(): array
    {
        return $this->segments;
    }
    public function setSegments(array $segments): void
    {
        $this->segments = $segments;
    }

    public function getCabinPrices(): array
    {
        return $this->cabinPrices;
    }
    public function setCabinPrices(array $cabinPrices): void
    {
        $this->cabinPrices = $cabinPrices;
    }

    public function getFlightPrice(): float
    {
        return $this->flightPrice;
    }
    public function setFlightPrice(float $flightPrice): void
    {
        $this->flightPrice = $flightPrice;
    }

    public function getProviderPrice(): float
    {
        return $this->providerPrice;
    }

    public function setProviderPrice(float $providerPrice): void
    {
        $this->providerPrice = $providerPrice;
    }

    public function jsonSerialize(): array
    {
        return [
            'segments' => $this->segments,
            'cabinPrices' => $this->cabinPrices,
            'flightPrice' => $this->flightPrice,
            'providerPrice' => $this->providerPrice,
        ];
    }
}
