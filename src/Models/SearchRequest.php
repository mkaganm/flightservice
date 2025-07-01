<?php
namespace App\Models;

class SearchRequest
{
    private ?string $from = null;
    private ?string $to = null;
    private ?string $departureDate = null;
    private ?string $returnDate = null;
    private ?int $adultCount = null;
    private ?int $childCount = null;
    private ?int $infantCount = null;

    public function getFrom(): ?string
    {
        return $this->from;
    }
    public function setFrom(?string $from): void
    {
        $this->from = $from;
    }

    public function getTo(): ?string
    {
        return $this->to;
    }
    public function setTo(?string $to): void
    {
        $this->to = $to;
    }

    public function getDepartureDate(): ?string
    {
        return $this->departureDate;
    }
    public function setDepartureDate(?string $departureDate): void
    {
        $this->departureDate = $departureDate;
    }

    public function getReturnDate(): ?string
    {
        return $this->returnDate;
    }
    public function setReturnDate(?string $returnDate): void
    {
        $this->returnDate = $returnDate;
    }

    public function getAdultCount(): ?int
    {
        return $this->adultCount;
    }
    public function setAdultCount(?int $adultCount): void
    {
        $this->adultCount = $adultCount;
    }

    public function getChildCount(): ?int
    {
        return $this->childCount;
    }
    public function setChildCount(?int $childCount): void
    {
        $this->childCount = $childCount;
    }

    public function getInfantCount(): ?int
    {
        return $this->infantCount;
    }
    public function setInfantCount(?int $infantCount): void
    {
        $this->infantCount = $infantCount;
    }

    public function isRoundTrip(): bool
    {
        return $this->getReturnDate() !== null && $this->getReturnDate() !== '';
    }
}
