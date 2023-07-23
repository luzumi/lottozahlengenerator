<?php

namespace App\Services;

class TemperaturesNumber
{
    /**
     * Gibt die am meisten und am seltensten gezogenen Zahlen zurück
     *
     * @return array
     */
    public function hotAndColdNumbers(): array
    {
        $drawings = \App\Models\LottoNumber::orderBy('id', 'desc')->take(100)->get();
        $numbersFrequency = $this->getNumbersFrequency($drawings);

        arsort($numbersFrequency);
        $hotNumbers = array_slice($numbersFrequency, 0, 6, true);

        asort($numbersFrequency);
        $coldNumbers = array_slice($numbersFrequency, 0, 6, true);

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
        $numbersFrequency = [];

        foreach ($drawings as $drawing) {
            $numbers = [$drawing->number_one,
                $drawing->number_two,
                $drawing->number_three,
                $drawing->number_four,
                $drawing->number_five,
                $drawing->number_six];

            foreach ($numbers as $number) {
                if (!isset($numbersFrequency[$number])) {
                    $numbersFrequency[$number] = 0;
                }
                $numbersFrequency[$number]++;
            }
        }

        return $numbersFrequency;
    }
}
