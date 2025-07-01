<?php

namespace App\Models;

class PaxPrices implements \JsonSerializable
{
    private string $paxCode;
    private float $paxPrice;

    public function getPaxCode(): string
    {
        return $this->paxCode;
    }
    public function setPaxCode(string $paxCode): void
    {
        $this->paxCode = $paxCode;
    }

    public function getPaxPrice(): float
    {
        return $this->paxPrice;
    }
    public function setPaxPrice(float $paxPrice): void
    {
        $this->paxPrice = $paxPrice;
    }

    public function jsonSerialize(): array
    {
        return [
            'paxCode' => $this->paxCode,
            'paxPrice' => $this->paxPrice,
        ];
    }
}