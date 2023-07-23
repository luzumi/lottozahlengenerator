<?php

namespace App\Services;

class FrequentedNumbers
{
    private CombinationCount $combinationCount;

    public function __construct()
    {
        $this->combinationCount = new CombinationCount();
    }

    /**
     * gibt die am häufigsten gezogenen Paare zurück
     *
     * @return array
     */
    public function frequentPairs(): array
    {
        $allDrawings = \App\Models\LottoNumber::all();
        $pairsCount = $this->combinationCount->getCombinationCounts($allDrawings, 2);

        // Sort the pairs by count descending
        arsort($pairsCount);
        $topThreePairs = array_slice($pairsCount, 0, 3, true);

        $numbers = [];
        foreach ($topThreePairs as $pair => $frequency) {
            $numbers = array_merge($numbers, explode("-", $pair));
        }

        return $numbers;
    }

    /**
     * gibt die am häufigsten gezogenen Trios zurück
     *
     * @return array
     */
    public function frequentTrios(): array
    {
        $allDrawings = \App\Models\LottoNumber::all();
        $triosCount = $this->combinationCount->getCombinationCounts($allDrawings, 3);

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
}
