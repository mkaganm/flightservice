<?php

namespace App\Models;

class CabinPrices implements \JsonSerializable
{
    private string $cabinClass;
    private string $fareFamily;
    private float $price;
    private string $classCodeKey;
    private string $classCodeValue;
    /**
     * @var PaxPrices[] $paxPrices
     */
    private array $paxPrices = [];

    public function getCabinClass(): string
    {
        return $this->cabinClass;
    }
    public function setCabinClass(string $cabinClass): void
    {
        $this->cabinClass = $cabinClass;
    }

    public function getFareFamily(): string
    {
        return $this->fareFamily;
    }
    public function setFareFamily(string $fareFamily): void
    {
        $this->fareFamily = $fareFamily;
    }

    public function getPrice(): float
    {
        return $this->price;
    }
    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    public function getClassCodeKey(): string
    {
        return $this->classCodeKey;
    }
    public function setClassCodeKey(string $classCodeKey): void
    {
        $this->classCodeKey = $classCodeKey;
    }

    public function getClassCodeValue(): string
    {
        return $this->classCodeValue;
    }
    public function setClassCodeValue(string $classCodeValue): void
    {
        $this->classCodeValue = $classCodeValue;
    }

    /**
     * @var PaxPrices[] $paxPrices
     */
    public function setPaxPrices(array $paxPrices): void
    {
        $this->paxPrices = $paxPrices;
    }

    public function getPaxPrices(): array
    {
        return $this->paxPrices;
    }

    public function jsonSerialize(): array
    {
        return [
            'cabinClass' => $this->cabinClass,
            'fareFamily' => $this->fareFamily,
            'price' => $this->price,
            'classCodeKey' => $this->classCodeKey,
            'classCodeValue' => $this->classCodeValue,
            'paxPrices' => $this->paxPrices,
        ];
    }


}