<?php

namespace App\DTO\NBP\ExchangeRates;

use DateTime;

class RequestDTO
{
    const DATE_FORMAT = 'Y-m-d';

    /** @var DateTime $date */
    private $date;

    public function getDate(): DateTime
    {
        return $this->date;
    }

    public function setDate(DateTime $date): self
    {
        $this->date = $date;

        return $this;
    }
}