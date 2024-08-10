<?php

namespace App\Service\NBP\ExchangeRate;

use App\DTO\NBP\ExchangeRates\DTO;
use App\DTO\NBP\ExchangeRates\RequestDTO;
use App\Exception\NBPException;
use App\Repository\API\NBP\ExchangeRateRepositoryInterface;
use App\Service\NBP\ExchangeRate\DTO\FactoryServiceInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Cache\ItemInterface;
use Throwable;

class GetService implements GetServiceInterface
{
    /** @var ExchangeRateRepositoryInterface */
    private $exchangeRateRepository;
    /** @var FactoryServiceInterface */
    private $factoryService;

    /** @var FilesystemAdapter */
    private $cache;

    public function __construct(
        ExchangeRateRepositoryInterface $exchangeRateRepository,
        FactoryServiceInterface         $factoryService
    )
    {
        $this->exchangeRateRepository = $exchangeRateRepository;
        $this->factoryService = $factoryService;

        $this->cache = new FilesystemAdapter();
    }

    /** {@inheritDoc} */
    public function getExchangeRateDTOByRequestDTO(RequestDTO $requestDTO): DTO
    {
        return $this->cache->get(
            $this->prepareNBPExchangeRatesCacheKey($requestDTO),
            function (ItemInterface $item) use ($requestDTO) {
                $item->expiresAfter(null);

                return $this->prepareExchangeRateDTO($requestDTO);
            });
    }

    private function prepareNBPExchangeRatesCacheKey(RequestDTO $requestDTO): string
    {
        return sprintf(
            self::NBP_EXCHANGE_RATE_CACHE_KEY_PATTERN,
            $requestDTO->getDate()->format(self::CACHE_KEY_DATE_FORMAT)
        );
    }

    /**
     * @param RequestDTO $requestDTO
     *
     * @return DTO
     *
     * @throws Throwable
     */
    private function prepareExchangeRateDTO(RequestDTO $requestDTO): DTO
    {
        $apiNBPData = $this->exchangeRateRepository->getRatesByTableAndDate($requestDTO->getDate());

        if (!$apiNBPData) {
            throw new NBPException(
                sprintf(
                    'No NBP data found for %s',
                    $requestDTO->getDate()->format(RequestDTO::DATE_FORMAT)
                ),
                Response::HTTP_NOT_FOUND
            );
        }

        return $this->factoryService->createExchangeRatesDTO($apiNBPData, $requestDTO);
    }
}