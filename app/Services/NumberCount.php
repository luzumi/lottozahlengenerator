<?php

namespace App\Services;

class NumberCount
{
    /**
     * Gibt die Zahlen mit der Anzahl der Ziehungen zurück
     *
     * @return array
     */
    public function getNumbersWithCount(): array
    {
        $numbers = range(1, 49);
        $frequentNumbers = [];

        foreach ($numbers as $number) {
            $count = \App\Models\LottoNumber::where('number_one', $number)
                ->orWhere('number_two', $number)
                ->orWhere('number_three', $number)
                ->orWhere('number_four', $number)
                ->orWhere('number_five', $number)
                ->orWhere('number_six', $number)
                ->count();
            $frequentNumbers[$number] = $count;
        }
        return $frequentNumbers;
    }
}
