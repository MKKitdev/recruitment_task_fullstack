<?php

namespace App\Service\NBP\ExchangeRate\DTO\Factories;

use App\DTO\NBP\ExchangeRates\CurrencyDTO;
use App\DTO\NBP\ExchangeRates\DTO;

class SupportedCurrencyFactory implements FactoryInterface
{
    /** {@inheritDoc} */
    public function isSupported(array $rateData, DTO $exchangeRatesDTO): bool
    {
        return in_array($rateData['code'], $exchangeRatesDTO->getSupportedCurrenciesConfig())
            && !in_array($rateData['code'], $exchangeRatesDTO->getBuyableCurrenciesConfig());
    }

    /** {@inheritDoc} */
    public function appendCurrencyDTOToDTO(array $rateData, DTO $exchangeRatesDTO): void
    {
        $exchangeRatesDTO->appendSupportedCurrency(
            CurrencyDTO::createFromResponseArray($rateData)
                ->setBuyPrice(null)
                ->setSellPrice(round($rateData['mid'] + 0.15, self::PRICE_ROUND_PRECISION))
        );
    }
}