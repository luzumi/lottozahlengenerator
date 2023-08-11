<?php

namespace App\Http\Controllers;

use App\Services\AllDrawingNumbers;
use App\Services\FrequentedNumbers;
use App\Services\FrequentNumberAnalyzer;
use App\Services\LongestAbsenceAnalyzer;
use App\Services\LottoNumberGenerator;
use App\Services\NumberCount;
use App\Services\RareNumberAnalyzer;
use App\Services\TemperaturesNumber;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
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

    public function __construct(AllDrawingNumbers      $allDrawingNumbers,
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

    public function calculate(Request $request, NumberCount $numberCount, RareNumberAnalyzer $rareNumberAnalyzer, LongestAbsenceAnalyzer $longestAbsenceAnalyzer, FrequentedNumbers $frequentedNumbers)
    {

        $allDrawingNumbers = new AllDrawingNumbers();

        // Häufigste Zahlen berechnen
        $frequentNumbers = $numberCount->getNumbersWithCount($allDrawingNumbers);
        $frequentRanks = $this->convertToRanks($frequentNumbers);

        // Seltenste Zahlen berechnen
        $rareNumbersCounts = $rareNumberAnalyzer->getRareNumbers($allDrawingNumbers); // Anpassen, wenn nötig
        $rareRanks = $this->convertToRanks($rareNumbersCounts);

        // Längste Abwesenheit berechnen
        $longestAbsenceCounts = $longestAbsenceAnalyzer->getLongestAbsence($allDrawingNumbers); // Anpassen, wenn nötig
        $longestAbsenceRanks = $this->convertToRanks($longestAbsenceCounts);

        // Am häufigsten gezogene Paare berechnen
        $allDrawings = $allDrawingNumbers->getAllDrawings();
        $frequentPairs = $frequentedNumbers->frequentPairs($allDrawings);

        // Initialisiere die Gesamtwertigkeiten
        $totalValues = array_fill(1, 49, 0);

        // Addiere die Wertigkeiten aus jeder Rubrik zu den Gesamtwertigkeiten
        foreach ($totalValues as $number => &$value) {
            if (isset($frequentRanks[$number])) {
                $value += $frequentRanks[$number];
            }
            if (isset($rareRanks[$number])) {
                $value += $rareRanks[$number];
            }
            if (isset($longestAbsenceRanks[$number])) {
                $value += $longestAbsenceRanks[$number];
            }
        }

        $rank = count($frequentPairs); // Starte mit der maximalen Anzahl an Paaren
        foreach ($frequentPairs as $pair) {
            $numbers = explode('-', $pair);
            foreach ($numbers as $number) {
                // Zuweisung der Wertigkeit basierend auf der aktuellen Rangposition
                $totalValues[$number] += $rank;
            }
            $rank--; // Reduziere den Rang für das nächste Paar
        }

        // Jetzt enthält $totalValues die Gesamtwertigkeiten für jede Zahl von 1 bis 49
        // Erstelle einen Pool, in dem jede Zahl entsprechend ihrer Wertigkeit dupliziert wird
        $numberPool = [];
        unset($value);
        foreach ($totalValues as $number => $value) {
            $numberPool = array_merge($numberPool, array_fill(0, $value, $number));
        }

        // Mische den Pool, um eine zufällige Reihenfolge sicherzustellen
        shuffle($numberPool);

        // Wähle die ersten 6 Zahlen aus dem gemischten Pool
        $selectedNumbers = [];
        while (count($selectedNumbers) < 6) {
            // Wähle die ersten 6 Zahlen aus dem gemischten Pool
            $potentialNumbers = array_slice($numberPool, 0, 6);

            // Füge nur eindeutige Zahlen zu den ausgewählten Zahlen hinzu
            foreach ($potentialNumbers as $number) {
                if (!in_array($number, $selectedNumbers)) {
                    $selectedNumbers[] = $number;
                }
            }

            // Wenn weniger als 6 eindeutige Zahlen ausgewählt wurden, entferne die bereits ausgewählten
            // Zahlen aus dem Pool und fahre fort
            if (count($selectedNumbers) < 6) {
                $numberPool = array_diff($numberPool, $selectedNumbers);
                shuffle($numberPool);
            }
        }

        // Sortiere die ausgewählten Zahlen
        sort($selectedNumbers);
        $selectedNumbers = array_slice($selectedNumbers, 0, 6);

        // Ausgabe der ausgewählten Zahlen
        return response()->json($selectedNumbers);
    }

    private function convertToRanks($counts)
    {
        arsort($counts);
        $ranks = [];
        $rank = 1;
        foreach ($counts as $key => $count) {
            $ranks[$key] = $rank++;
        }
        return $ranks;
    }

}
