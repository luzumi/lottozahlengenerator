<?php

namespace App\Http\Controllers;

use App\Services\FrequentedNumbers;
use App\Services\FrequentNumberAnalyzer;
use App\Services\LongestAbsenceAnalyzer;
use App\Services\LottoNumberGenerator;
use App\Services\RareNumberAnalyzer;
use App\Services\TemperaturesNumber;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class LottoFieldController extends Controller
{
    private LottoNumberGenerator $lottoNumberGenerator;
    private FrequentNumberAnalyzer $frequentNumberAnalyzer;
    private RareNumberAnalyzer $rareNumberAnalyzer;
    private LongestAbsenceAnalyzer $longestAbsenceAnalyzer;
    private FrequentedNumbers $frequentedNumbers;
    private TemperaturesNumber $temperatureNumber;

    public function __construct(LottoNumberGenerator   $lottoNumberGenerator,
                                FrequentedNumbers      $frequentedNumbers,
                                TemperaturesNumber     $temperaturesNumber,
                                RareNumberAnalyzer     $rareNumberAnalyzer,
                                FrequentNumberAnalyzer $frequentNumberAnalyzer,
                                LongestAbsenceAnalyzer $longestAbsenceAnalyzer,)
    {
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
        $selectedNumbers['Zufallszahlen'] = $this->lottoNumberGenerator->generateRandomNumbers();
        $selectedNumbers['am meisten gezogen'] = $this->frequentNumberAnalyzer->getFrequentNumbers();
        $selectedNumbers['am seltensten gezogen'] = $this->rareNumberAnalyzer->getRareNumbers();
        $selectedNumbers['Längste Abwesenheit'] = $this->longestAbsenceAnalyzer->getLongestAbsence();
        $selectedNumbers['häufigste Paare'] = $this->frequentedNumbers->frequentPairs();
        $selectedNumbers['häufigste Trilling'] = $this->frequentedNumbers->frequentTrios();
        $selectedNumbers['heisse Zahlen in den letzten 100 Ziehungen'] = $this->temperatureNumber->hotAndColdNumbers()['hotNumbers'];
        $selectedNumbers['kalte Zahlen in den letzten 100 Ziehungen'] = $this->temperatureNumber->hotAndColdNumbers()['coldNumbers'];


        return view('generate')->with(compact('selectedNumbers'));
    }


}
