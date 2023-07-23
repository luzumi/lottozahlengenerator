<?php

namespace App\Services;

class FrequentNumberAnalyzer
{
    public function getFrequentNumbers(): array
    {
        $numberWithCount = new NumberCount();
        $frequentNumbers = $numberWithCount->getNumbersWithCount(range(1, 49));

        $selectedNumbers = array_keys(collect($frequentNumbers)->sortDesc()->toArray());
        return array_slice($selectedNumbers, 0, 6);
    }
}
