<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Drawing;
use App\Models\LottoNumber;
use App\Models\Winning;
use App\Models\WinningGrade;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $winningGrades = [
            [
                'Stufe' => 'Gewinnklasse I',
                'Beschreibung' => '6 Treffer und Zusatzzahl',
            ],
            [
                'Stufe' => 'Gewinnklasse II',
                'Beschreibung' => '6 Treffer',
            ],
            [
                'Stufe' => 'Gewinnklasse III',
                'Beschreibung' => '5 Treffer und Zusatzzahl',
            ],
            [
                'Stufe' => 'Gewinnklasse IV',
                'Beschreibung' => '5 Treffer',
            ],
            [
                'Stufe' => 'Gewinnklasse V',
                'Beschreibung' => '4 Treffer und Zusatzzahl',
            ]  ,
            [
                'Stufe' => 'Gewinnklasse VI',
                'Beschreibung' => '4 Treffer',
            ],
            [
                'Stufe' => 'Gewinnklasse VII',
                'Beschreibung' => '3 Treffer und Zusatzzahl',
            ],
            [
                'Stufe' => 'Gewinnklasse VIII',
                'Beschreibung' => '3 Treffer',
            ],
            [
                'Stufe' => 'Gewinnklasse IX',
                'Beschreibung' => '2 Treffer und Zusatzzahl',
            ],
        ];

        foreach ($winningGrades as $winningGradeData) {
            WinningGrade::create($winningGradeData);
        }

        // Lade die JSON-Datei
        $json = Storage::get('ziehungen.json');

        // Konvertiere die JSON-Datei in ein Array
        $drawings = json_decode($json, true);

        // Überprüfen Sie, ob die Datei erfolgreich konvertiert wurde
        if (!is_array($drawings)) {
            $this->command->error('Kann die Datei ziehungen.json nicht lesen oder sie ist leer.');
            return;
        }

        // Gehe durch jedes Element im Array
        foreach ($drawings as $drawingData) {
            // Erstelle einen neuen Zeichnungseintrag
            $drawing = Drawing::create([
                'year' => $drawingData['Jahr'],
                'draw_date' => $drawingData['Datum'],
                'calendar_week' => $drawingData['Woche'],
                'draw_type' => $drawingData['Tag'],
            ]);

            // Erstelle einen neuen LottoNumber-Eintrag
            LottoNumber::create([
                'drawing_id' => $drawing->id,
                'number_one' => $drawingData['Zahl1'],
                'number_two' => $drawingData['Zahl2'],
                'number_three' => $drawingData['Zahl3'],
                'number_four' => $drawingData['Zahl4'],
                'number_five' => $drawingData['Zahl5'],
                'number_six' => $drawingData['Zahl6'],
                'superzahl' => isset($drawingData['Superzahl']) ? intval($drawingData['Superzahl']) : null,
                'zusatzzahl' => isset($drawingData['Zusatzzahl']) ? intval($drawingData['Zusatzzahl']) : null,
            ]);

            // Füge Gewinninformationen hinzu
            for ($i = 1; $i <= 9; $i++) {
                $winningGrade = 10 - $i;

                if ($winningGrade === null) {
                    continue; // This would skip the current iteration of the loop.
                }

                Winning::create([
                    'drawing_id' => $drawing->id,
                    'winning_grade_id' => $winningGrade,
                    'winners' => $drawingData['Gewinnklasse' . $i . '-Anzahl'],
                    'payout' => $i!=9 ? $drawingData['Gewinnklasse' . $i . '-Auszahlung'] : 5,
                ]);

            }
        }
    }
}
