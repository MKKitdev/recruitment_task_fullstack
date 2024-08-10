<?php

namespace App\DTO\NBP\ExchangeRates;

use JsonSerializable;
use App\Interfaces\ArrayableInterface;

class CurrencyDTO implements JsonSerializable, ArrayableInterface
{
    private const NBP_RATE_ROUND_PRECISION = 4;

    /** @var string $name */
    private $name;
    /** @var string $code */
    private $code;
    /** @var float $rate */
    private $midRate;

    /** @var ?float */
    private $buyPrice;
    /** @var float */
    private $sellPrice;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getMidRate(): float
    {
        return $this->midRate;
    }

    public function setMidRate(float $midRate): self
    {
        $this->midRate = $midRate;

        return $this;
    }

    public static function createFromResponseArray(array $data): self
    {
        return (new CurrencyDTO())
            ->setName(ucfirst($data['currency']))
            ->setCode($data['code'])
            ->setMidRate(round($data['mid'], self::NBP_RATE_ROUND_PRECISION));
    }

    public function getBuyPrice(): ?float
    {
        return $this->buyPrice;
    }

    public function setBuyPrice(?float $buyPrice): self
    {
        $this->buyPrice = $buyPrice;

        return $this;
    }

    public function getSellPrice(): float
    {
        return $this->sellPrice;
    }

    public function setSellPrice(float $sellPrice): self
    {
        $this->sellPrice = $sellPrice;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'code' => $this->getCode(),
            'name' => $this->getName(),
            'nbpMidRate' => $this->getMidRate(),
            'buyPrice' => $this->getBuyPrice(),
            'sellPrice' => $this->getSellPrice(),
        ];
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }
}