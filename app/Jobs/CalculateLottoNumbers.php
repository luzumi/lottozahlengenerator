<?php

namespace App\Jobs;

use App\Services\AllDrawingNumbers;
use App\Services\FrequentedNumbers;
use App\Services\FrequentNumberAnalyzer;
use App\Services\LongestAbsenceAnalyzer;
use App\Services\LottoNumberGenerator;
use App\Services\RareNumberAnalyzer;
use App\Services\TemperaturesNumber;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class CalculateLottoNumbers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $uniqueId;

    public function __construct(string $uniqueId)
    {
        $this->uniqueId = $uniqueId;
    }

    public function handle(
        AllDrawingNumbers      $allDrawingNumbers,
        FrequentNumberAnalyzer $frequentNumberAnalyzer,
        RareNumberAnalyzer     $rareNumberAnalyzer,
        LongestAbsenceAnalyzer $longestAbsenceAnalyzer,
        FrequentedNumbers      $frequentedNumbers,
        TemperaturesNumber     $temperaturesNumber,

    ) {
        $results = [];

        $results['am meisten gezogen'] = $frequentNumberAnalyzer->getFrequentNumbers($allDrawingNumbers);
        $results['am seltensten gezogen'] = $rareNumberAnalyzer->getRareNumbers($allDrawingNumbers);
        $results['Längste Abwesenheit'] = $longestAbsenceAnalyzer->getLongestAbsence($allDrawingNumbers);
        $results['häufigste Paare'] = $frequentedNumbers->frequentPairs($allDrawingNumbers->getAllDrawings());
        $results['häufigste Trilling'] = $frequentedNumbers->frequentTrios($allDrawingNumbers->getAllDrawings());

        $hotAndColdNumbers = $temperaturesNumber->hotAndColdNumbers($allDrawingNumbers);
        $results['heisse Zahlen in den letzten 100 Ziehungen'] = $hotAndColdNumbers['hotNumbers'];
        $results['kalte Zahlen in den letzten 100 Ziehungen'] = $hotAndColdNumbers['coldNumbers'];

        Cache::put('lottoNumbers', $results, 60 * 60 * 24); // Cache the results for 24 hours

    }
}
