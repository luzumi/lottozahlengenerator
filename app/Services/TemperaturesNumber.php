<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class TemperaturesNumber
{
    /**
     * Gibt die Temperaturzahlen zurück
     *
     * @param $allDrawings
     * @return array
     */
    public function hotAndColdNumbers($allDrawingsService): array
    {
        $allDrawings = $allDrawingsService->getAllDrawings();
        $drawings = collect($allDrawings)->sortByDesc('id')->take(100);
        $numbersFrequency = $this->getNumbersFrequency($drawings);

        arsort($numbersFrequency);
        $hotNumbers = array_slice($numbersFrequency, 0, 6, true);
        $coldNumbers = array_slice($numbersFrequency, -6, 6, true);

        return [
            'hotNumbers' => array_keys($hotNumbers),
            'coldNumbers' => array_keys($coldNumbers)
        ];
    }


    /**
     * gibt die Häufigkeit der Zahlen zurück
     *
     * @param $drawings
     * @return array
     */
    private function getNumbersFrequency($drawings): array
    {
        $numbers = $drawings->flatMap(function ($drawing) {
            return [
                $drawing->number_one,
                $drawing->number_two,
                $drawing->number_three,
                $drawing->number_four,
                $drawing->number_five,
                $drawing->number_six
            ];
        });

        return $numbers->countBy()->all();
    }
}
