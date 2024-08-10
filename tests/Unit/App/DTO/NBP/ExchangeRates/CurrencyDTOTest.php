<?php

namespace Unit\App\DTO\NBP\ExchangeRates;

use App\DTO\NBP\ExchangeRates\CurrencyDTO;
use PHPUnit\Framework\TestCase;

class CurrencyDTOTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        $currencyDTOMock = $this->getCurrencyDTOMock();


    }

    private function getCurrencyDTOMock(): CurrencyDTO
    {
        return new CurrencyDTO();
    }
}