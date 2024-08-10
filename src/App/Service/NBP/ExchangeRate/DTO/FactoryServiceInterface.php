<?php

namespace App\Service\NBP\ExchangeRate\DTO;

use App\DTO\NBP\ExchangeRates\DTO;
use App\DTO\NBP\ExchangeRates\RequestDTO;
use DateTime;

interface FactoryServiceInterface
{
    /**
     * @param array $responseData
     * @param RequestDTO $requestDTO
     *
     * @return DTO
     */
    public function createExchangeRatesDTO(
        array      $responseData,
        RequestDTO $requestDTO
    ): DTO;
}