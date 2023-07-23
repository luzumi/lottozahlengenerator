<?php

namespace App\Services;

class LottoNumberGenerator
{
    public function generateRandomNumbers(): array
    {
        return array_rand(array_flip(range(1, 49)), 6);
    }

}
