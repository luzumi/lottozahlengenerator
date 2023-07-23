<?php

namespace App\Services;

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
    public function getLongestAbsence(): array
    {
        $absenceNumbers = $this->getLastDrawnDates($this->numbers);
        asort($absenceNumbers);
        return array_keys(array_slice($absenceNumbers, 0, 6, true));
    }

    /**
     * gibt die letzten Ziehungsdaten für die Zahlen zurück
     *
     * @return array
     */
    private function getLastDrawnDates(): array
    {
        $absenceNumbers = [];

        foreach ($this->numbers as $number) {
            $lastDrawn = $this->getLastDrawnForNumber($number);

            if($lastDrawn) {
                $absenceNumbers[$number] = $lastDrawn;
            } else {
                $absenceNumbers[$number] = '0000-00-00';
            }
        }

        return $absenceNumbers;
    }

    /**
     * gibt das Datum der letzten Ziehung für die Zahl zurück
     *
     * @param int $number
     * @return string|null
     */
    private function getLastDrawnForNumber(int $number): ?string
    {
        $lastDrawn = \App\Models\LottoNumber::where('number_one', $number)
            ->orWhere('number_two', $number)
            ->orWhere('number_three', $number)
            ->orWhere('number_four', $number)
            ->orWhere('number_five', $number)
            ->orWhere('number_six', $number)
            ->join('draws', 'lotto_numbers.drawing_id', '=', 'draws.id')
            ->orderByRaw("STR_TO_DATE(CONCAT(draws.year, '-', REPLACE(draws.draw_date, '.', '-')), '%Y-%m-%d') desc")
            ->first();

        return $lastDrawn ? $lastDrawn->year . '-' . str_replace('.', '-', $lastDrawn->draw_date) : null;
    }
}
