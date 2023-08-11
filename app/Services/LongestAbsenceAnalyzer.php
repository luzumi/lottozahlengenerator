<?php

namespace App\Services;

use App\Models\LottoNumber;

class LongestAbsenceAnalyzer
{
    private array $numbers;

    public function __construct()
    {
        $this->numbers = range(1, 49);
    }

    /**
     * gibt die 6 Zahlen zurück, die am längsten nicht gezogen wurden
     *
     * @return array
     */
    public function getLongestAbsence($allDrawingNumbers): array
    {
        $absenceNumbers = $this->getLastDrawnDates($allDrawingNumbers);
        asort($absenceNumbers);
        return array_keys(array_slice($absenceNumbers, true));
    }

    /**
     * gibt die letzten Ziehungsdaten für die Zahlen zurück
     *
     * @return array
     */
    private function getLastDrawnDates($allDrawingNumbers): array
    {
        $absenceNumbers = [];
        $lastDrawnDates = $this->getLastDrawnForAllNumbers($allDrawingNumbers);


        foreach ($this->numbers as $number) {
            $absenceNumbers[$number] = $lastDrawnDates[$number] ?? '0000-00-00';
        }
        return $absenceNumbers;
    }

    /**
     * gibt das Datum der letzten Ziehung für die Zahl zurück
     *
     * @return array
     */
    private function getLastDrawnForAllNumbers($allDrawingNumbers): array
    {
        // Initialize an array to store the last drawn date for each number
        $lastDrawn = array_fill(1, 49, null);
        $keys = ['number_one', 'number_two', 'number_three', 'number_four', 'number_five', 'number_six'];
        $drawings = $allDrawingNumbers->getAllDrawings();
        // Iterate over all drawings
        foreach ($drawings as $drawing) {
            // Check each number in the drawing
            for ($i = 1; $i <= 6; $i++) {
                $number = $drawing[$keys[$i - 1]];
                $date = $drawing->drawing['draw_date'] . '.' . $drawing->drawing['year'];
                // Update the last drawn date for the number if it's more recent
                if (!isset($lastDrawn[$number]) || $date > $lastDrawn[$number]) {
                    $lastDrawn[$number] = str_replace('-','.', $date);
                }
            }
        }
        // Now $lastDrawn contains the last drawn date for each number
        return $lastDrawn;
    }



}
