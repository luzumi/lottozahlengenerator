<?php

namespace App\Jobs;

use App\Services\AllDrawingNumbers;
use App\Services\XRapidApi;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateLottoResultsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private mixed $startDate;
    private mixed $endDate;
    private XRapidApi $xRapidApi;

    /**
     * Create a new job instance.
     */
    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->xRapidApi = new XRapidApi();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $results = $this->fetchDataFromAPI($this->startDate, $this->endDate);
        $this->saveResultsToDatabase($results);
    }

// Methoden zum Abrufen der Daten von der API und Speichern in einer JSON-Datei
    protected function fetchDataFromAPI($startDate, $endDate): array
    {
        $date = Carbon::createFromFormat('d.m.Y', $startDate);
        $end = Carbon::createFromFormat('d.m.Y', $endDate);
        $results = [];
Log::notice('LottoUpdater: ' . $date->toDateString() . ' - ' . $end->toDateString());
        while ($date->lte($end)) {
            Log::notice('LottoUpdater: ' . $date->toDateString());
            if ($date->dayOfWeek == CarbonInterface::WEDNESDAY || $date->dayOfWeek == CarbonInterface::SATURDAY) {
                try {
                    $result = $this->xRapidApi->getLottoResults($date->toDateString());
                    if (isset($result->numbers)) {
                        $results[] = $result;
                    }
                } catch (GuzzleException $e) {
                    Log::alert('LottoUpdater: ' . $e->getMessage());
                }
            }
            $date->addDay();
        }
        return $results;
    }

    protected function saveResultsToDatabase($results): void
    {
        // Pfad zur JSON-Datei
        $filePath = storage_path('result.json');

        // Konvertieren Sie die Ergebnisse in einen JSON-String
        $jsonData = json_encode($results);

        // Speichern Sie den JSON-String in der Datei
        file_put_contents($filePath, $jsonData);
    }
}
