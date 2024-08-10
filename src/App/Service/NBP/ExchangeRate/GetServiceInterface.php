<?php

namespace App\Service\NBP\ExchangeRate;

use App\DTO\NBP\ExchangeRates\DTO;
use App\DTO\NBP\ExchangeRates\RequestDTO;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

interface GetServiceInterface
{
    const NBP_EXCHANGE_RATE_CACHE_KEY_PATTERN = 'nbp_exchange_rates_%s';
    const CACHE_KEY_DATE_FORMAT = 'Y_m_d';

    /**
     * @param RequestDTO $requestDTO
     *
     * @return DTO
     *
     * @throws NotFoundHttpException
     * @throws Throwable
     * @throws InvalidArgumentException
     */
    public function getExchangeRateDTOByRequestDTO(RequestDTO $requestDTO): DTO;
}