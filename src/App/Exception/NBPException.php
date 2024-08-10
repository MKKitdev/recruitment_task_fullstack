<?php

namespace App\Exception;

use Exception;
class NBPException extends Exception
{
    public function __construct(string $message = "", int $code = 0)
    {
        parent::__construct($message, $code);
    }
}