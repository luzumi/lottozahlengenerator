<?php

namespace App\Http\Controllers;

use App\Models\Drawing;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class LottoFieldController extends Controller
{
    /**
     * @return Application|Factory|View|\Illuminate\Foundation\Application
     */
    public function showLottoNumbers()
    {
        $allNumbers = range(1, 49);

        // Hier könnte Ihre Logik zur Generierung der Lottozahlen stehen
        $selectedNumbers['Zufallszahlen'] = $this->generateLottoNumbers($allNumbers);
        $selectedNumbers['am meisten gezogen'] = $this->frequentNumbers($allNumbers);
        $selectedNumbers['am seltensten gezogen'] = $this->leastOftenDrawn($allNumbers);
        $selectedNumbers['Längste Abwesenheit'] = $this->longestAbsence($allNumbers);
        $selectedNumbers['häufigste Paare'] = $this->frequentPairs();
        $selectedNumbers['häufigste Trilling'] = $this->frequentTrios();
        $selectedNumbers['heisse Zahlen in den letzten 100 Ziehungen'] = $this->hotAndColdNumbers()['hotNumbers'];
        $selectedNumbers['kalte Zahlen in den letzten 100 Ziehungen'] = $this->hotAndColdNumbers()['coldNumbers'];

        return view('generate')->with(compact('selectedNumbers'));
    }

    /**
     * @param array $numbers
     * @return array
     */
    private function generateLottoNumbers(array $numbers): array
    {
        return array_rand(array_flip($numbers), 6);
    }

    /**
     * @param array $numbers
     * @return array
     */
    public function frequentNumbers(array $numbers): array
    {
        $frequentNumbers = $this->getNumbersWithCount($numbers);

        $selectedNumbers = array_keys(collect($frequentNumbers)->sortDesc()->toArray());
        return array_slice($selectedNumbers, 0, 6);
    }

    /**
     * @param array $numbers
     * @return array
     */
    private function leastOftenDrawn(array $numbers): array
    {
        $frequentNumbers = $this->getNumbersWithCount($numbers);

        $selectedNumbers = array_keys(collect($frequentNumbers)->sort()->toArray());
        return array_slice($selectedNumbers, 0, 6);
    }

    /**
     * @param array $numbers
     * @return array
     */
    public function getNumbersWithCount(array $numbers): array
    {
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

    /**
     * @param array $numbers
     * @return array
     */
    private function longestAbsence(array $numbers): array
    {
        $absenceNumbers = $this->getLastDrawnDates($numbers);
        asort($absenceNumbers);
        $selectedNumbers = array_keys(array_slice($absenceNumbers, 0, 6, true));

        return $selectedNumbers;
    }

    public function getLastDrawnDates(array $numbers): array
    {
        $absenceNumbers = [];

        foreach ($numbers as $number) {
            $lastDrawn = $this->getLastDrawnForNumber($number);

            if($lastDrawn) {
                $absenceNumbers[$number] = $lastDrawn;
            } else {
                $absenceNumbers[$number] = '0000-00-00';
            }
        }

        return $absenceNumbers;
    }

    private function getLastDrawnForNumber(int $number)
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

    private function frequentPairs(): array
    {
        $allDrawings = \App\Models\LottoNumber::all();
        $pairsCount = $this->getCombinationCounts($allDrawings, 2);

        // Sort the pairs by count descending
        arsort($pairsCount);
        $topThreePairs = array_slice($pairsCount, 0, 3, true);

        $numbers = [];
        foreach ($topThreePairs as $pair => $frequency) {
            $numbers = array_merge($numbers, explode("-", $pair));
        }

        return $numbers;
    }

    private function frequentTrios(): array
    {
        $allDrawings = \App\Models\LottoNumber::all();
        $triosCount = $this->getCombinationCounts($allDrawings, 3);

        // Sort combinations by frequency
        arsort($triosCount);

        // Get the first 6 combinations
        $topCombinations = array_slice($triosCount, 0, 6, true);

        $selectedNumbers = [];

        // Split each combination into individual numbers
        foreach($topCombinations as $combination => $frequency) {
            $selectedNumbers = array_merge($selectedNumbers, explode('-', $combination));
            if (count(array_unique($selectedNumbers)) >= 6) {
                break;
            }
        }

        // Make sure there are only unique numbers
        return $selectedNumbers;
    }

    private function getCombinationCounts($drawings, $combinationSize)
    {
        $combinationCounts = [];

        foreach ($drawings as $drawing) {
            $numbers = [
                $drawing->number_one,
                $drawing->number_two,
                $drawing->number_three,
                $drawing->number_four,
                $drawing->number_five,
                $drawing->number_six
            ];

            $combinations = $this->combinations($numbers, $combinationSize);

            foreach ($combinations as $combination) {
                sort($combination);
                $key = implode('-', $combination);

                if (!isset($combinationCounts[$key])) {
                    $combinationCounts[$key] = 0;
                }
                $combinationCounts[$key]++;
            }
        }

        return $combinationCounts;
    }

    public function combinations($array, $n)
    {
        if ($n === 0) {
            return [[]];
        }

        if (count($array) === 0) {
            return [];
        }

        $head = $array[0];
        $tail = array_slice($array, 1);

        $combsWithHead = array_map(function ($comb) use ($head) {
            return array_merge([$head], $comb);
        }, $this->combinations($tail, $n - 1));

        $combsWithoutHead = $this->combinations($tail, $n);

        return array_merge($combsWithHead, $combsWithoutHead);
    }

    private function hotAndColdNumbers(): array
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

    private function getNumbersFrequency($drawings): array
    {
        $numbersFrequency = [];

        foreach ($drawings as $drawing) {
            $numbers = [$drawing->number_one, $drawing->number_two, $drawing->number_three, $drawing->number_four, $drawing->number_five, $drawing->number_six];

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
