<?php

namespace App\Services;

use App\Models\Drawing;
use App\Models\LottoNumber;
use App\Models\Winning;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use function PHPUnit\Framework\isEmpty;

class LottoUpdater
{
    protected XRapidApi $xRapidApi;

    public function __construct(XRapidApi $xRapidApi)
    {
        $this->xRapidApi = $xRapidApi;
    }

    public function updateDatabase(AllDrawingNumbers $allDrawingNumbers): void
    {
        // Ermittle das letzte vorhandene Ziehungsdatum in der Datenbank
        $lastDrawDate = $this->getLastDrawDateInDatabase($allDrawingNumbers);

        // Starte am Tag nach dem letzten vorhandenen Ziehungsdatum
        $date = $lastDrawDate->addDay();

        // Heutiges Datum
        $today = Carbon::today();

        // Iteriere über die Daten
        while ($date->lte($today)) {
            // Überprüfe, ob das Datum ein Mittwoch oder Samstag ist
            if ($date->dayOfWeek == CarbonInterface::WEDNESDAY || $date->dayOfWeek == CarbonInterface::SATURDAY) {

                // Überprüfe, ob das Datum bereits in der Datenbank vorhanden ist
                if (!$this->isDateInDatabase($date)) {
                    try {
                        // Lotto-Ergebnisse abrufen
                        $results = $this->xRapidApi->getLottoResults($date->toDateString());
//                        dd($date->dayOfWeek,
//                            CarbonInterface::SATURDAY,
//                            $date,
//                            $date->lte($today),
//                            $today,
//                            $this->isDateInDatabase($date),
//                            $results,
//                        );
                        // Ergebnisse in der Datenbank speichern
                        if(!isset($results->numbers)) {
                            $date->addDay();
                            continue;
                        }

                        $this->saveResultsToDatabase($results, $date);
                    } catch (GuzzleException $e) {
                        Log::alert('LottoUpdater: ' . $e->getMessage());
                    }
                }
            }

            // Zum nächsten Tag gehen
            $date->addDay();
        }
    }

    protected function getLastDrawDateInDatabase(AllDrawingNumbers $allDrawingNumbers): Carbon
    {
        // Verwende die Methode getAllDrawings, um alle Ziehungen abzurufen
        $allDrawings = $allDrawingNumbers->getAllDrawings();

        // Ermittle das letzte Ziehungsdatum und das zugehörige Jahr
        $lastDraw = $allDrawings->last();
        $lastDrawDate = $lastDraw->drawing->draw_date; // Format: dd.mm
        $lastDrawYear = $lastDraw->drawing->year;

        // Ersetze den Punkt durch einen Bindestrich und füge das Jahr hinzu
        $fullDate = $lastDrawYear . '-' . str_replace('.', '-', $lastDrawDate);

        // Konvertiere den String in ein Carbon-Objekt
        return Carbon::createFromFormat('Y-m-d', $fullDate);
    }


    protected function isDateInDatabase(Carbon $date): bool
    {
// Logik, um zu überprüfen, ob das Datum bereits in der Datenbank vorhanden ist
// Zum Beispiel:
        return DB::table('draws')->where('draw_date', $date->toDateString())->exists();
    }


    // Logik zum Speichern der Ergebnisse in der Datenbank
    protected function saveResultsToDatabase($results, $date): void
    {
        $jsonContent = json_encode($results);
        $file_path = '../storage/app/result.json'; // Pfad zur gewünschten Speicherstelle
        file_put_contents($file_path, $jsonContent);


        // Speichere den Eintrag in der Tabelle "draws"
        $drawing = new Drawing();
        $drawing->year = $date->year;
        $drawing->draw_date = $date->format('d.m');
        $drawing->calendar_week = $this->getCalendarWeek();
        $drawing->draw_type = $this->getCalendarWeek()->dayOfWeek == CarbonInterface::WEDNESDAY ? 'MI' : 'SA';
        $drawing->save();


        // Speichere den Eintrag in der Tabelle "lotto_numbers"
        $drawNumbersCollection = $results->drawNumbersCollection;
        $lottoNumbers = [];

        foreach ($drawNumbersCollection as $drawNumber) {
            $lottoNumbers[] = $drawNumber->drawNumber;
        }

        $lottoNumbersModel = new LottoNumber();
        $lottoNumbersModel->drawing_id = $drawing->id;
        $lottoNumbersModel->number_one = $lottoNumbers[0];
        $lottoNumbersModel->number_two = $lottoNumbers[1];
        $lottoNumbersModel->number_three = $lottoNumbers[2];
        $lottoNumbersModel->number_four = $lottoNumbers[3];
        $lottoNumbersModel->number_five = $lottoNumbers[4];
        $lottoNumbersModel->number_six = $lottoNumbers[5];
        $lottoNumbersModel->superzahl = $results->superNumber;
        $lottoNumbersModel->save();


        // Speichere die Einträge in der Tabelle "winnings" (falls vorhanden)
        // Hier musst du die entsprechenden Daten aus der API-Antwort extrahieren
        // Beispiel:
        $oddsCollection = $results->oddsCollection;

        foreach ($oddsCollection as $odds) {
            $winning = new Winning();
            $winning->drawing_id = $drawing->id;
            $winning->winning_grade_id = 10 - $odds->winningClassDescription->winningClassDescriptionPK->winningClass; // Die ID korrekt zuordnen
            $winning->winners = $odds->numberOfWinners;
            $winning->payout = $odds->odds; // Dies könnte anders sein, je nachdem, wie Sie die Auszahlung speichern möchten
            $winning->save();
        }
    }

    /**
     * @return Carbon
     */
    public function getCalendarWeek(): Carbon
    {
        $timestampInMilliseconds = 1609542000000;
        $timestampInSeconds = $timestampInMilliseconds / 1000;

        return Carbon::createFromTimestamp($timestampInSeconds);
    }

}
