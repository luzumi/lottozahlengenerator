<?php

namespace App\Services;

class NumberCount
{
    /**
     * Gibt die Zahlen mit der Anzahl der Ziehungen zurück
     *
     * @return array
     */
    public function getNumbersWithCount($allDrawingNumbers): array
    {
        $numbers = range(1, 49);
        $frequentNumbers = array_fill_keys($numbers, 0); // Initialize all keys with 0
        $allDrawings = $allDrawingNumbers->getAllDrawings();
        $keys = ['number_one', 'number_two', 'number_three', 'number_four', 'number_five', 'number_six'];

        foreach ($allDrawings as $drawing) {
            // Increment the count for each number in the drawing
            for ($i = 1; $i <= 6; $i++) {
                $number = $drawing[$keys[$i - 1]];

                $frequentNumbers[$number]++;
            }
        }

        return $frequentNumbers;
    }

}
