<?php

namespace Unit\App\DTO\NBP\ExchangeRates;

use App\DTO\NBP\ExchangeRates\RequestDTO;
use PHPUnit\Framework\TestCase;
use DateTime;

class RequestDTOTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        $requestDTOMock = $this->getRequestDTOMock();

        $testDate = new DateTime();

        $requestDTOMock->setDate($testDate);
        $this->assertSame($testDate, $requestDTOMock->getDate());
    }

    private function getRequestDTOMock(): RequestDTO
    {
        return new RequestDTO();
    }
}