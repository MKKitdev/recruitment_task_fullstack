<?php

namespace Unit\App\Service\NBP\ExchangeRate\DTO\Factories;

use App\DTO\NBP\ExchangeRates\CurrencyDTO;
use App\DTO\NBP\ExchangeRates\DTO;
use App\Service\NBP\ExchangeRate\DTO\Factories\SupportedCurrencyFactory;
use DateTime;

class SupportedCurrencyFactoryTest extends AbstractFactoryTest
{
    public function testIsSupportedWhenIsNot(): void
    {
        $factoryMock = $this->getFactoryMock();

        $dtoMock = (new DTO())
            ->setDate(new DateTime())
            ->setBuyableCurrenciesConfig(['EUR'])
            ->setSupportedCurrenciesConfig(['USD', 'EUR']);

        $result = $factoryMock->isSupported(self::EXAMPLE_RATE_DATA, $dtoMock);

        $this->assertTrue($result);
    }

    public function testIsSupportedWhenIs(): void
    {
        $factoryMock = $this->getFactoryMock();

        $dtoMock = (new DTO())
            ->setDate(new DateTime())
            ->setBuyableCurrenciesConfig(['USD'])
            ->setSupportedCurrenciesConfig(['USD', 'EUR']);

        $result = $factoryMock->isSupported(self::EXAMPLE_RATE_DATA, $dtoMock);

        $this->assertFalse($result);
    }

    public function testAppendCurrencyDTOToDTO(): void
    {
        $factoryMock = $this->getFactoryMock();

        $dtoMock = (new DTO())
            ->setDate(new DateTime())
            ->setBuyableCurrenciesConfig(['USD'])
            ->setSupportedCurrenciesConfig(['USD', 'EUR']);

        $factoryMock->appendCurrencyDTOToDTO(self::EXAMPLE_RATE_DATA, $dtoMock);

        $this->assertCount(0, $dtoMock->getBuyableCurrencies());
        $this->assertCount(1, $dtoMock->getSupportedCurrencies());

        $this->assertInstanceOf(CurrencyDTO::class, $dtoMock->getSupportedCurrencies()[0]);
        $this->assertSame('USD', $dtoMock->getSupportedCurrencies()[0]->getCode());
        $this->assertSame('Dolar Amerykański', $dtoMock->getSupportedCurrencies()[0]->getName());
        $this->assertSame(4.0000, $dtoMock->getSupportedCurrencies()[0]->getMidRate());
        $this->assertSame(null, $dtoMock->getSupportedCurrencies()[0]->getBuyPrice());
        $this->assertSame(4.1500, $dtoMock->getSupportedCurrencies()[0]->getSellPrice());
    }

    private function getFactoryMock(): SupportedCurrencyFactory
    {
        return new SupportedCurrencyFactory();
    }
}