<?php

namespace Unit\App\Service\NBP\ExchangeRate;

use App\DTO\NBP\ExchangeRates\DTO;
use App\DTO\NBP\ExchangeRates\RequestDTO;
use App\Exception\NBPException;
use App\Repository\API\NBP\ExchangeRateRepositoryInterface;
use App\Service\NBP\ExchangeRate\DTO\FactoryServiceInterface;
use App\Service\NBP\ExchangeRate\GetService;
use DateTime;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GetServiceTest extends TestCase
{
    /** @var ExchangeRateRepositoryInterface|MockObject)|MockObject */
    private $exchangeRateRepositoryMock;

    /** @var FactoryServiceInterface|MockObject */
    private $factoryServiceMock;

    /** @var FilesystemAdapter */
    private $cache;

    public function setUp(): void
    {
        parent::setUp();

        $this->factoryServiceMock = $this->createMock(FactoryServiceInterface::class);
        $this->exchangeRateRepositoryMock = $this->createMock(ExchangeRateRepositoryInterface::class);

        $this->cache = new FilesystemAdapter();
    }

    public function testGetExchangeRateDTOByRequestDTOWhenNotGetDataFromApi(): void
    {
        $this->cache->clear();

        $dateMock = DateTime::CreateFromFormat(RequestDTO::DATE_FORMAT, '2024-07-07');

        $this->exchangeRateRepositoryMock
            ->expects($this->once())
            ->method('getRatesByTableAndDate')
            ->with($dateMock)
            ->willReturn(null);

        $getServiceMock = $this->getMockedGetService();

        $this->expectException(NBPException::class);
        $this->expectExceptionCode(404);
        $this->expectErrorMessage('No NBP data found for 2024-07-07');

        $getServiceMock->getExchangeRateDTOByRequestDTO(
            $this->getRequestDTOMock($dateMock)
        );
    }

    public function testGetExchangeRateDTOByDateWhenIsSuccess(): void
    {
        $this->cache->clear();

        $dateMock = DateTime::CreateFromFormat(RequestDTO::DATE_FORMAT, '2024-07-17');
        $requestDTOMock = $this->getRequestDTOMock($dateMock);

        $this->exchangeRateRepositoryMock
            ->expects($this->once())
            ->method('getRatesByTableAndDate')
            ->with($dateMock)
            ->willReturn(['exampleData']);

        $this->factoryServiceMock
            ->expects($this->once())
            ->method('createExchangeRatesDTO')
            ->with(['exampleData'], $requestDTOMock)
            ->willReturn(new DTO);

        $getServiceMock = $this->getMockedGetService();

        $result = $getServiceMock->getExchangeRateDTOByRequestDTO($requestDTOMock);

        $this->assertInstanceOf(DTO::class, $result);
    }

    private function getMockedGetService(): GetService
    {
        return new GetService(
            $this->exchangeRateRepositoryMock,
            $this->factoryServiceMock
        );
    }

    private function getRequestDTOMock(DateTime $date): RequestDTO
    {
        return (new RequestDTO())
            ->setDate($date);
    }
}