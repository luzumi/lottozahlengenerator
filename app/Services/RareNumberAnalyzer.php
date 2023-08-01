<?php

namespace App\Services;

class RareNumberAnalyzer
{
    /**
     * Gibt die selten gezogenen Zahlen zurück
     *
     * @return array
     */
    public function getRareNumbers($allDrawingNumbers): array
    {
        $numberWithCount = new NumberCount();
        $frequentNumbers = $numberWithCount->getNumbersWithCount($allDrawingNumbers);

        $selectedNumbers = array_keys(collect($frequentNumbers)->sort()->toArray());
        return array_slice($selectedNumbers, 0, 6);
    }
}
