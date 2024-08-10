<?php

namespace Unit\App\Service\NBP\ExchangeRate\DTO\Factories;

use App\DTO\NBP\ExchangeRates\CurrencyDTO;
use App\DTO\NBP\ExchangeRates\DTO;
use App\Service\NBP\ExchangeRate\DTO\Factories\BuyableCurrencyFactory;
use DateTime;

class BuyableCurrencyFactoryTest extends AbstractFactoryTest
{
    public function testIsSupportedWhenIsNot(): void
    {
        $factoryMock = $this->getFactoryMock();

        $dtoMock = (new DTO())
            ->setDate(new DateTime())
            ->setBuyableCurrenciesConfig(['EUR'])
            ->setSupportedCurrenciesConfig(['USD', 'EUR']);

        $result = $factoryMock->isSupported(self::EXAMPLE_RATE_DATA, $dtoMock);

        $this->assertFalse($result);
    }

    public function testIsSupportedWhenIs(): void
    {
        $factoryMock = $this->getFactoryMock();

        $dtoMock = (new DTO())
            ->setDate(new DateTime())
            ->setBuyableCurrenciesConfig(['USD'])
            ->setSupportedCurrenciesConfig(['USD', 'EUR']);

        $result = $factoryMock->isSupported(self::EXAMPLE_RATE_DATA, $dtoMock);

        $this->assertTrue($result);
    }

    public function testAppendCurrencyDTOToDTO(): void
    {
        $factoryMock = $this->getFactoryMock();

        $dtoMock = (new DTO())
            ->setDate(new DateTime())
            ->setBuyableCurrenciesConfig(['USD'])
            ->setSupportedCurrenciesConfig(['USD', 'EUR']);

        $factoryMock->appendCurrencyDTOToDTO(self::EXAMPLE_RATE_DATA, $dtoMock);

        $this->assertCount(0, $dtoMock->getSupportedCurrencies());
        $this->assertCount(1, $dtoMock->getBuyableCurrencies());

        $this->assertInstanceOf(CurrencyDTO::class, $dtoMock->getBuyableCurrencies()[0]);
        $this->assertSame('USD', $dtoMock->getBuyableCurrencies()[0]->getCode());
        $this->assertSame('Dolar Amerykański', $dtoMock->getBuyableCurrencies()[0]->getName());
        $this->assertSame(4.0000, $dtoMock->getBuyableCurrencies()[0]->getMidRate());
        $this->assertSame(3.9500, $dtoMock->getBuyableCurrencies()[0]->getBuyPrice());
        $this->assertSame(4.0700, $dtoMock->getBuyableCurrencies()[0]->getSellPrice());
    }

    private function getFactoryMock(): BuyableCurrencyFactory
    {
        return new BuyableCurrencyFactory();
    }
}