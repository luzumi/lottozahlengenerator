<?php

namespace App\Http\Controllers;

use App\Jobs\UpdateLottoResultsJob;
use App\Services\AllDrawingNumbers;
use Illuminate\Http\Request;
use App\Services\LottoUpdater;
use App\Models\Drawing;

class LottoUpdateController extends Controller
{
    public function updateDatabase()
    {

        \File::put(storage_path('logs/laravel.log'), '');
        // Start- und Enddatum festlegen
        $startDate = '01.01.2021';
        $endDate = now()->toDateString();

        // Job in die Queue stellen
        UpdateLottoResultsJob::dispatch($startDate, $endDate);

        // Hole die neuesten Einträge (oder alle Einträge, je nach Bedarf)
        $draws = Drawing::latest()->get();

        // Gibt die Einträge an eine Ansicht weiter
        return view('welcome', compact('draws'));
    }

}
