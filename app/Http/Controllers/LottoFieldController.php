<?php

namespace App\Http\Controllers;

use App\Services\AllDrawingNumbers;
use App\Services\FrequentedNumbers;
use App\Services\FrequentNumberAnalyzer;
use App\Services\LongestAbsenceAnalyzer;
use App\Services\LottoNumberGenerator;
use App\Services\RareNumberAnalyzer;
use App\Services\TemperaturesNumber;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;

class LottoFieldController extends Controller
{
    private LottoNumberGenerator $lottoNumberGenerator;
    private FrequentNumberAnalyzer $frequentNumberAnalyzer;
    private RareNumberAnalyzer $rareNumberAnalyzer;
    private LongestAbsenceAnalyzer $longestAbsenceAnalyzer;
    private FrequentedNumbers $frequentedNumbers;
    private TemperaturesNumber $temperatureNumber;
    private AllDrawingNumbers $allDrawingNumbers;

    public function __construct(AllDrawingNumbers   $allDrawingNumbers,
                                LottoNumberGenerator   $lottoNumberGenerator,
                                FrequentedNumbers      $frequentedNumbers,
                                TemperaturesNumber     $temperaturesNumber,
                                RareNumberAnalyzer     $rareNumberAnalyzer,
                                FrequentNumberAnalyzer $frequentNumberAnalyzer,
                                LongestAbsenceAnalyzer $longestAbsenceAnalyzer)
    {
        $this->allDrawingNumbers = $allDrawingNumbers;
        $this->lottoNumberGenerator = $lottoNumberGenerator;
        $this->frequentNumberAnalyzer = $frequentNumberAnalyzer;
        $this->rareNumberAnalyzer = $rareNumberAnalyzer;
        $this->longestAbsenceAnalyzer = $longestAbsenceAnalyzer;
        $this->frequentedNumbers = $frequentedNumbers;
        $this->temperatureNumber = $temperaturesNumber;
    }

    /**
     * zeigt die Zufallszahlen und die Zahlen, die am häufigsten und am seltensten gezogen wurden
     *
     * @return Application|Factory|View|\Illuminate\Foundation\Application
     */
    public function showLottoNumbers()
    {
        $selectedNumbers = Cache::get('lottoNumbers');

        $selectedNumbers['Zufallszahlen'] =
            $this->lottoNumberGenerator->generateRandomNumbers($this->allDrawingNumbers);
//
        if (!isset($selectedNumbers['am meisten gezogen'])) {
            $selectedNumbers['am meisten gezogen'] =
                $this->frequentNumberAnalyzer->getFrequentNumbers($this->allDrawingNumbers);
        }
        if (!isset($selectedNumbers['am seltensten gezogen'])) {
            $selectedNumbers['am seltensten gezogen'] =
                $this->rareNumberAnalyzer->getRareNumbers($this->allDrawingNumbers);
        }
        if (!isset($selectedNumbers['Längste Abwesenheit'])) {
            $selectedNumbers['Längste Abwesenheit'] =
                $this->longestAbsenceAnalyzer->getLongestAbsence($this->allDrawingNumbers);
        }
        if (!isset($selectedNumbers['häufigste Paare'])) {
            $selectedNumbers['häufigste Paare'] =
                $this->frequentedNumbers->frequentPairs($this->allDrawingNumbers->getAllDrawings());
        }
        if (!isset($selectedNumbers['häufigste Trilling'])) {
            $selectedNumbers['häufigste Trilling'] =
                $this->frequentedNumbers->frequentTrios($this->allDrawingNumbers->getAllDrawings());
        }
        if (!isset($selectedNumbers['heisse Zahlen in den letzten 100 Ziehungen']) || !isset($selectedNumbers['kalte Zahlen in den letzten 100 Ziehungen'])) {
            $hotAndColdNumbers = $this->temperatureNumber->hotAndColdNumbers($this->allDrawingNumbers);
            $selectedNumbers['heisse Zahlen in den letzten 100 Ziehungen'] = $hotAndColdNumbers['hotNumbers'];
            $selectedNumbers['kalte Zahlen in den letzten 100 Ziehungen'] = $hotAndColdNumbers['coldNumbers'];
        }

        return view('generate')->with(compact('selectedNumbers'));
    }


}
