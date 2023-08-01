<?php

namespace App\Services;

class FrequentNumberAnalyzer
{
    public function getFrequentNumbers($allDrawingNumbers): array
    {
        $numberWithCount = new NumberCount();
        $frequentNumbers = $numberWithCount->getNumbersWithCount($allDrawingNumbers);
        $selectedNumbers = array_keys(collect($frequentNumbers)->sortDesc()->toArray());

        return array_slice($selectedNumbers, 0, 6);
    }
}
