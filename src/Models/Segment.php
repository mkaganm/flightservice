<?php

namespace App\Models;

class Segment implements \JsonSerializable
{
    private string $flightNumber;
    private string $origin;
    private string $destination;
    private string $originTerminal;
    private string $destinationTerminal;
    private string $originCountryCode;
    private string $destinationCountryCode;
    private string $segmentCode;
    private $departureTime;
    private $arrivalTime;


    public function getFlightNumber(): string
    {
        return $this->flightNumber;
    }
    public function setFlightNumber(string $flightNumber): void
    {
        $this->flightNumber = $flightNumber;
    }

    public function getOrigin(): string
    {
        return $this->origin;
    }
    public function setOrigin(string $origin): void
    {
        $this->origin = $origin;
    }

    public function getDestination(): string
    {
        return $this->destination;
    }
    public function setDestination(string $destination): void
    {
        $this->destination = $destination;
    }

    public function getOriginTerminal(): string
    {
        return $this->originTerminal;
    }
    public function setOriginTerminal(string $originTerminal): void
    {
        $this->originTerminal = $originTerminal;
    }

    public function getDestinationTerminal(): string
    {
        return $this->destinationTerminal;
    }
    public function setDestinationTerminal(string $destinationTerminal): void
    {
        $this->destinationTerminal = $destinationTerminal;
    }

    public function getOriginCountryCode(): string
    {
        return $this->originCountryCode;
    }
    public function setOriginCountryCode(string $originCountryCode): void
    {
        $this->originCountryCode = $originCountryCode;
    }

    public function getDestinationCountryCode(): string
    {
        return $this->destinationCountryCode;
    }
    public function setDestinationCountryCode(string $destinationCountryCode): void
    {
        $this->destinationCountryCode = $destinationCountryCode;
    }

    public function getSegmentCode(): string
    {
        return $this->segmentCode;
    }
    public function setSegmentCode(string $segmentCode): void
    {
        $this->segmentCode = $segmentCode;
    }

    public function getDepartureTime()
    {
        return $this->departureTime;
    }
    public function setDepartureTime($departureTime): void
    {
        $this->departureTime = $departureTime;
    }

    public function getArrivalTime()
    {
        return $this->arrivalTime;
    }
    public function setArrivalTime($arrivalTime): void
    {
        $this->arrivalTime = $arrivalTime;
    }

    public function jsonSerialize(): array
    {
        return [
            'flightNumber' => $this->flightNumber,
            'origin' => $this->origin,
            'destination' => $this->destination,
            'originTerminal' => $this->originTerminal,
            'destinationTerminal' => $this->destinationTerminal,
            'originCountryCode' => $this->originCountryCode,
            'destinationCountryCode' => $this->destinationCountryCode,
            'segmentCode' => $this->segmentCode,
            'departureTime' => $this->departureTime,
            'arrivalTime' => $this->arrivalTime,
        ];
    }


}