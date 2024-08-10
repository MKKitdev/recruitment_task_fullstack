<?php

namespace App\Repository\API\NBP;

use DateTime;
use Throwable;

interface ExchangeRateRepositoryInterface
{
    const ENDPOINT_DEFAULT_TABLE_CODE = 'A';
    const EXCHANGE_RATES_ENDPOINT_PATTERN = 'https://api.nbp.pl/api/exchangerates/tables/%s/%s/?format=json';
    const EXCHANGE_RATES_DATE_FORMAT = 'Y-m-d';

    /**
     * @param DateTime $date
     * @param string $table
     *
     * @return array<mixed>|null
     *
     * @throws Throwable
     */
    public function getRatesByTableAndDate(DateTime $date, string $table = 'A'): ?array;
}