<?php

namespace Unit\App\Service\NBP\ExchangeRate\DTO;

use App\DTO\NBP\ExchangeRates\DTO;
use App\DTO\NBP\ExchangeRates\RequestDTO;
use App\Exception\NBPException;
use App\Service\NBP\ExchangeRate\DTO\Factories\FactoryInterface;
use App\Service\NBP\ExchangeRate\DTO\FactoryService;
use DateTime;
use Integration\ExchangeRates\ExchangeRatesTest;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class FactoryServiceTest extends KernelTestCase
{
    private const FILEPATH_EXAMPLE_NBP_API_RESPONSE_SUCCESS = '/NBP/ExchangeRates/mock_nbp_api_response_success_small.json';

    /** @var MockObject|ParameterBagInterface */
    private $parameterBagMock;

    /** @var FactoryInterface|MockObject */
    private $factoryMock;

    public function setUp(): void
    {
        parent::setUp();
        static::bootKernel();

        $this->parameterBagMock = $this->createMock(ParameterBagInterface::class);

        $this->factoryMock = $this->createMock(FactoryInterface::class);
    }

    public function testCreateExchangeRatesDTO(): void
    {
        $this->parameterBagMock
            ->expects($this->once())
            ->method('get')
            ->with('nbp')
            ->willReturn([
                'exchangeRates' => [
                    'supportedCurrencies' => ['USD', 'EUR', 'CZK', 'IDR', 'BRL'],
                    'buyableCurrencies' => ['USD', 'EUR',],
                ]
            ]);

        $this->factoryMock
            ->expects($this->exactly(4))
            ->method('isSupported')
            ->withAnyParameters()
            ->willReturnOnConsecutiveCalls(true, true, false, false);

        $factoryServiceMock = $this->getMockedFactoryService([
            $this->factoryMock, $this->factoryMock
        ]);

        $result = $factoryServiceMock->createExchangeRatesDTO(
            json_decode(
                $this->getTestFileContent(self::FILEPATH_EXAMPLE_NBP_API_RESPONSE_SUCCESS),
                true
            ),
            $this->getMockedRequestDTO()
        );

        $this->assertInstanceOf(DTO::class, $result);
    }

    public function testCreateExchangeRatesDTOWhenIsWrongResponseData(): void
    {
        $factoryServiceMock = $this->getMockedFactoryService([
            $this->factoryMock
        ]);

        $this->expectException(NBPException::class);
        $this->expectExceptionCode(Response::HTTP_NOT_FOUND);
        $this->expectExceptionMessage('Bad response data from NBP API');

        $factoryServiceMock->createExchangeRatesDTO(
            ['wrong', 'response', 'data'],
            $this->getMockedRequestDTO()
        );
    }

    private function getMockedFactoryService(array $factories): FactoryService
    {
        return new FactoryService(
            $this->parameterBagMock,
            $factories
        );
    }

    private function getMockedRequestDTO(): RequestDTO
    {
        return (new RequestDTO())
            ->setDate(DateTime::createFromFormat(RequestDTO::DATE_FORMAT, '2024-08-07'));
    }

    protected function getTestFileContent(string $filepath): string
    {
        return file_get_contents(
            static::$kernel->getProjectDir() . DIRECTORY_SEPARATOR . 'tests/File' . $filepath
        );
    }
}