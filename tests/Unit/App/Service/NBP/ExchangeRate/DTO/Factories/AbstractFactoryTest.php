<?php

namespace Unit\App\Service\NBP\ExchangeRate\DTO\Factories;

use PHPUnit\Framework\TestCase;

abstract class AbstractFactoryTest extends TestCase
{
    protected const EXAMPLE_RATE_DATA = [
        'code' => 'USD',
        'currency' => 'Dolar Amerykański',
        'mid' => 4, 0000,
    ];
}