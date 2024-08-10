<?php

namespace App\Service\NBP\ExchangeRate\DTO\Factories;

use App\DTO\NBP\ExchangeRates\CurrencyDTO;
use App\DTO\NBP\ExchangeRates\DTO;

class BuyableCurrencyFactory implements FactoryInterface
{
    /** {@inheritDoc} */
    public function isSupported(array $rateData, DTO   $exchangeRatesDTO): bool
    {
        return in_array($rateData['code'], $exchangeRatesDTO->getSupportedCurrenciesConfig())
            && in_array($rateData['code'], $exchangeRatesDTO->getBuyableCurrenciesConfig());
    }

    /** {@inheritDoc} */
    public function appendCurrencyDTOToDTO(array $rateData, DTO   $exchangeRatesDTO): void
    {
        $exchangeRatesDTO->appendBuyableCurrency(
            CurrencyDTO::createFromResponseArray($rateData)
                ->setBuyPrice(round($rateData['mid'] - 0.05, self::PRICE_ROUND_PRECISION))
                ->setSellPrice(round($rateData['mid'] + 0.07, self::PRICE_ROUND_PRECISION))
        );
    }
}