<?php

namespace App\DTO\NBP\ExchangeRates;

use App\Interfaces\ArrayableInterface;
use DateTime;
use JsonSerializable;

class DTO implements ArrayableInterface, JsonSerializable
{
    /** @var array<int, string> $supportedCurrenciesConfig */
    private $supportedCurrenciesConfig;

    /** @var array<int, string> $buyableCurrenciesConfig */
    private $buyableCurrenciesConfig;

    /** @var DateTime $date */
    private $date;

    /** @var CurrencyDTO[] */
    private $buyableCurrencies = [];

    /** @var CurrencyDTO[] */
    private $supportedCurrencies = [];

    public function getSupportedCurrenciesConfig(): array
    {
        return $this->supportedCurrenciesConfig;
    }

    public function setSupportedCurrenciesConfig(array $supportedCurrenciesConfig): self
    {
        $this->supportedCurrenciesConfig = $supportedCurrenciesConfig;

        return $this;
    }

    public function getBuyableCurrenciesConfig(): array
    {
        return $this->buyableCurrenciesConfig;
    }

    public function setBuyableCurrenciesConfig(array $buyableCurrenciesConfig): self
    {
        $this->buyableCurrenciesConfig = $buyableCurrenciesConfig;

        return $this;
    }

    public function getDate(): DateTime
    {
        return $this->date;
    }

    public function setDate(DateTime $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getBuyableCurrencies(): array
    {
        return $this->buyableCurrencies;
    }

    public function appendBuyableCurrency(CurrencyDTO $currencyDTO): self
    {
        $this->buyableCurrencies[] = $currencyDTO;

        return $this;
    }

    public function getSupportedCurrencies(): array
    {
        return $this->supportedCurrencies;
    }

    public function appendSupportedCurrency(CurrencyDTO $currencyDTO): self
    {
        $this->supportedCurrencies[] = $currencyDTO;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'date' => $this->getDate()->format(RequestDTO::DATE_FORMAT),
            'buyableCurrencies' => $this->getBuyableCurrencies(),
            'supportedCurrencies' => $this->getSupportedCurrencies(),
        ];
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }
}