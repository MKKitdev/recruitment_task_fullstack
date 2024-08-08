<?php

namespace Unit\App\Service\NBP\ExchangeRate\DTO;

use App\DTO\NBP\ExchangeRates\DTO;
use App\DTO\NBP\ExchangeRates\RequestDTO;
use App\Service\NBP\ExchangeRate\DTO\Factories\FactoryInterface;
use App\Service\NBP\ExchangeRate\DTO\FactoryService;
use DateTime;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class FactoryServiceTest extends TestCase
{
    const NBP_API_RESPONSE_JSON_DATA_EXAMPLE = '[{"table":"A","no":"153/A/NBP/2024","effectiveDate":"2024-08-07","rates":[{"currency":"bat (Tajlandia)","code":"THB","mid":0.1109},{"currency":"dolar amerykański","code":"USD","mid":3.9526},{"currency":"dolar australijski","code":"AUD","mid":2.5927},{"currency":"dolar Hongkongu","code":"HKD","mid":0.5070},{"currency":"dolar kanadyjski","code":"CAD","mid":2.8739},{"currency":"dolar nowozelandzki","code":"NZD","mid":2.3777},{"currency":"dolar singapurski","code":"SGD","mid":2.9772},{"currency":"euro","code":"EUR","mid":4.3158},{"currency":"forint (Węgry)","code":"HUF","mid":0.010832},{"currency":"frank szwajcarski","code":"CHF","mid":4.6025},{"currency":"funt szterling","code":"GBP","mid":5.0204},{"currency":"hrywna (Ukraina)","code":"UAH","mid":0.0964},{"currency":"jen (Japonia)","code":"JPY","mid":0.026852},{"currency":"korona czeska","code":"CZK","mid":0.1707},{"currency":"korona duńska","code":"DKK","mid":0.5783},{"currency":"korona islandzka","code":"ISK","mid":0.0286},{"currency":"korona norweska","code":"NOK","mid":0.3655},{"currency":"korona szwedzka","code":"SEK","mid":0.3790},{"currency":"lej rumuński","code":"RON","mid":0.8672},{"currency":"lew (Bułgaria)","code":"BGN","mid":2.2066},{"currency":"lira turecka","code":"TRY","mid":0.1176},{"currency":"nowy izraelski szekel","code":"ILS","mid":1.0414},{"currency":"peso chilijskie","code":"CLP","mid":0.00418},{"currency":"peso filipińskie","code":"PHP","mid":0.0687},{"currency":"peso meksykańskie","code":"MXN","mid":0.2057},{"currency":"rand (Republika Południowej Afryki)","code":"ZAR","mid":0.2155},{"currency":"real (Brazylia)","code":"BRL","mid":0.6987},{"currency":"ringgit (Malezja)","code":"MYR","mid":0.8802},{"currency":"rupia indonezyjska","code":"IDR","mid":0.0002465},{"currency":"rupia indyjska","code":"INR","mid":0.047084},{"currency":"won południowokoreański","code":"KRW","mid":0.002876},{"currency":"yuan renminbi (Chiny)","code":"CNY","mid":0.5501},{"currency":"SDR (MFW)","code":"XDR","mid":5.2788}]}]'; // ToDo :: on file and read

    /** @var MockObject|ParameterBagInterface */
    private $parameterBagMock;

    /** @var FactoryInterface|MockObject */
    private $factoryMock;

    public function setUp(): void
    {
        parent::setUp();

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

        $factoryServiceMock = $this->getMockedFactoryService([
            $this->factoryMock
        ]);

        $result = $factoryServiceMock->createExchangeRatesDTO(
            json_decode(self::NBP_API_RESPONSE_JSON_DATA_EXAMPLE, true),
            $this->getMockedRequestDTO()
        );

        $this->assertInstanceOf(DTO::class, $result);
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
}