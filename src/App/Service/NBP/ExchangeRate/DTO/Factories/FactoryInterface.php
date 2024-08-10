<?php

namespace App\Service\NBP\ExchangeRate\DTO\Factories;

use App\DTO\NBP\ExchangeRates\DTO;

interface FactoryInterface
{
    const PRICE_ROUND_PRECISION = 4;

    /**
     * @param array<string, mixed> $rateData
     * @param DTO $exchangeRatesDTO
     *
     * @return bool
     */
    public function isSupported(
        array $rateData,
        DTO $exchangeRatesDTO
    ): bool;

    /**
     * @param array $rateData
     * @param DTO $exchangeRatesDTO
     *
     * @return void
     */
    public function appendCurrencyDTOToDTO(
        array $rateData,
        DTO $exchangeRatesDTO
    ): void;
}