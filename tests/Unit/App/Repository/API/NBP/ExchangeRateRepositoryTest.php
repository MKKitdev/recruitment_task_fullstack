<?php

namespace Unit\App\Repository\API\NBP;

use App\Repository\API\NBP\ExchangeRateRepository;
use DateTime;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class ExchangeRateRepositoryTest extends TestCase
{
    /** @var MockObject|HttpClientInterface */
    private $httpClientMock;

    /** @var MockObject|ResponseInterface */
    private $responseMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->httpClientMock = $this->createMock(HttpClientInterface::class);

        $this->responseMock = $this->createMock(ResponseInterface::class);
    }

    public function testGetRatesByTableAndDateWhenIsError(): void
    {
        $this->responseMock
            ->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(400);

        $this->httpClientMock
            ->expects($this->once())
            ->method('request')
            ->withAnyParameters()
            ->willReturn($this->responseMock);

        $exchangeRateRepositoryMock = $this->getMockedExchangeRateRepository();

        $result = $exchangeRateRepositoryMock->getRatesByTableAndDate(new DateTime());

        $this->assertNull($result);
    }

    public function testGetRatesByTableAndDateWhenSuccess(): void
    {
        $this->responseMock
            ->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(200);

        $this->responseMock
            ->expects($this->once())
            ->method('getContent')
            ->willReturn('{}');

        $this->httpClientMock
            ->expects($this->once())
            ->method('request')
            ->withAnyParameters()
            ->willReturn($this->responseMock);

        $exchangeRateRepositoryMock = $this->getMockedExchangeRateRepository();

        $result = $exchangeRateRepositoryMock->getRatesByTableAndDate(new DateTime());

        $this->assertIsArray($result);
    }

    private function getMockedExchangeRateRepository(): ExchangeRateRepository
    {
        return new ExchangeRateRepository(
            $this->httpClientMock
        );
    }
}