<?php

namespace Unit\App\DTO\NBP\ExchangeRates;


use App\DTO\NBP\ExchangeRates\CurrencyDTO;
use App\DTO\NBP\ExchangeRates\DTO;
use DateTime;
use PHPUnit\Framework\TestCase;

class DTOTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        $dateMock = new DateTime();

        $dto = new DTO();

        $dto->setDate($dateMock);
        $this->assertInstanceOf(DateTime::class, $dto->getDate());
        $this->assertSame($dateMock, $dto->getDate());

        $dto->setBuyableCurrenciesConfig(['USD']);
        $this->assertIsArray($dto->getBuyableCurrenciesConfig());
        $this->assertSame('USD', $dto->getBuyableCurrenciesConfig()[0]);

        $dto->setSupportedCurrenciesConfig(['USD']);
        $this->assertIsArray($dto->getSupportedCurrenciesConfig());
        $this->assertSame('USD', $dto->getSupportedCurrenciesConfig()[0]);

        $dto->appendBuyableCurrency(new CurrencyDTO());
        $this->assertCount(1, $dto->getBuyableCurrencies());
        $this->assertInstanceOf(CurrencyDTO::class, $dto->getBuyableCurrencies()[0]);

        $dto->appendSupportedCurrency(new CurrencyDTO());
        $this->assertCount(1, $dto->getSupportedCurrencies());
        $this->assertInstanceOf(CurrencyDTO::class, $dto->getSupportedCurrencies()[0]);
    }

    public function testToArray(): void
    {
        $dateMock = new DateTime();

        $dto = (new DTO())
            ->setDate($dateMock);

        $result = $dto->toArray();

        $this->assertIsArray($result);
        $this->assertSame([
            'date' => $dateMock->format('Y-m-d'),
            'buyableCurrencies' => [],
            'supportedCurrencies' => [],
        ], $result);
    }

    public function testJsonSerialize(): void
    {
        $dateMock = DateTime::createFromFormat('Y-m-d', '2024-08-08');

        $dto = (new DTO())
            ->setDate($dateMock);

        $result = json_encode($dto->jsonSerialize());

        $this->assertJson($result);
        $this->assertSame(
            '{"date":"2024-08-08","buyableCurrencies":[],"supportedCurrencies":[]}',
            $result
        );
    }
}