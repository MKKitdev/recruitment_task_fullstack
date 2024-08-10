<?php

namespace App\Repository\API\NBP;

use DateTime;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ExchangeRateRepository implements ExchangeRateRepositoryInterface
{
    /** @var HttpClientInterface */
    private $httpClient;

    public function __construct(
        HttpClientInterface $httpClient
    )
    {
        $this->httpClient = $httpClient;
    }

    /** {@inheritDoc}
     *
     */
    public function getRatesByTableAndDate(DateTime $date, string $table = self::ENDPOINT_DEFAULT_TABLE_CODE): ?array
    {
        $client = $this->httpClient->request(
            'GET',
            $this->prepareUrlForNBPExchangeRatesEndpoint($date, $table)
        );

        return ($client->getStatusCode() === Response::HTTP_OK)
            ? json_decode($client->getContent(), true)
            : null;
    }

    /**
     * @param DateTime $date
     * @param string $table
     *
     * @return string
     */
    private function prepareUrlForNBPExchangeRatesEndpoint(DateTime $date, string $table): string
    {
        return sprintf(
            self::EXCHANGE_RATES_ENDPOINT_PATTERN,
            $table,
            $date->format(self::EXCHANGE_RATES_DATE_FORMAT)
        );
    }
}