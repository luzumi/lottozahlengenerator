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

    public function getFrequentNumbersWeighting($allDrawingNumbers): array
    {
        $numberWithCount = new NumberCount();
        $frequentNumbers = $numberWithCount->getNumbersWithCount($allDrawingNumbers);
        $selectedNumbers = array_keys(collect($frequentNumbers)->sortDesc()->toArray());

        $totalNumbers = count($selectedNumbers);
        $weightedNumbers = [];

        foreach ($selectedNumbers as $index => $number) {
            // Berechne die Gewichtung absteigend von 50 bis 0
            $weight = 50 - (50 / ($totalNumbers - 1)) * $index;
            $weightedNumbers[$number] = $weight;
        }

        return $weightedNumbers;
    }

}
