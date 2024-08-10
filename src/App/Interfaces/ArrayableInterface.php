<?php

namespace App\Interfaces;

interface ArrayableInterface
{
    /**
     * @return array<mixed>
     */
    public function toArray(): array;
}