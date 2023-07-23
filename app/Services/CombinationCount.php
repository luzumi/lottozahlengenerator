<?php

namespace App\Services;

class CombinationCount
{
    /**
     * Gibt die Häufigkeit der Kombinationen zurück
     *
     * @param $drawings
     * @param $combinationSize
     * @return array
     */
    public function getCombinationCounts($drawings, $combinationSize): array
    {
        $combinationCounts = [];

        foreach ($drawings as $drawing) {
            $numbers = $this->getNumbersFromDrawing($drawing);
            $combinations = $this->combinations($numbers, $combinationSize);

            $this->updateCombinationCounts($combinations, $combinationCounts);
        }

        return $combinationCounts;
    }

    /**
     * Gibt die Zahlen aus der Ziehung zurück
     *
     * @param $drawing
     * @return array
     */
    private function getNumbersFromDrawing($drawing): array
    {
        return [
            $drawing->number_one,
            $drawing->number_two,
            $drawing->number_three,
            $drawing->number_four,
            $drawing->number_five,
            $drawing->number_six
        ];
    }

    /**
     * Gibt die Kombinationen zurück
     *
     * @param $array
     * @param $n
     * @return array
     */
    public function combinations($array, $n): array
    {
//        if ($n === 0) {
//            return [[]];
//        }
//
//        if (count($array) === 0) {
//            return [];
//        }
//
//        $head = $array[0];
//        $tail = array_slice($array, 1);
//
//        $combsWithHead = array_map(function ($comb) use ($head) {
//            return array_merge([$head], $comb);
//        }, $this->combinations($tail, $n - 1));
//
//        $combsWithoutHead = $this->combinations($tail, $n);
//
//        return array_merge($combsWithHead, $combsWithoutHead);


        $result = [];
        $count = count($array);
        $indices = range(0, $n - 1);

        while ($indices[0] < $count - $n + 1) {
            $result[] = array_intersect_key($array, array_flip($indices));

            for ($i = $n - 1; $i >= 0; $i--) {
                if ($indices[$i] < $count - $n + $i) {
                    $indices[$i]++;
                    for ($j = $i + 1; $j < $n; $j++) {
                        $indices[$j] = $indices[$j - 1] + 1;
                    }
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * Aktualisiert die Häufigkeit der Kombinationen
     *
     * @param $combinations
     * @param $combinationCounts
     * @return void
     */
    private function updateCombinationCounts($combinations, &$combinationCounts): void
    {
        foreach ($combinations as $combination) {
            sort($combination);
            $key = implode('-', $combination);

            if (!isset($combinationCounts[$key])) {
                $combinationCounts[$key] = 0;
            }
            $combinationCounts[$key]++;
        }
    }
}
