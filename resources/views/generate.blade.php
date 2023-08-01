@extends('layouts.app')

@section('content')
    <div id="settings-container">
        <div class="settings-overlay" id="settings-overlay">
            <!-- Einstellungselemente -->
            <div class="settings-header">
                <h2>Einstellungen</h2>
                <button id="close-button">Schließen</button>
            </div>
            <div class="settings-content">
                <div class="settings-section">
                    <h3>Wie viele Reihen sollen generiert werden?</h3>
                    <div class="settings-input">
                        <label for="row-count"></label><input type="number" id="row-count" value="1" min="1" max="8">
                    </div>
                </div>
                <div class="settings-section">
                    <h3>Wie viele Zahlen sollen maximal pro Reihe ausgewählt werden?</h3>
                    <div class="settings-input">
                        <label for="max-selected-numbers"></label><input type="number" id="max-selected-numbers"
                                                                         value="1" min="1" max="49">
                    </div>
                </div>
                <div class="settings-section">
                    <h3>Wie viele Zahlen sollen minimal pro Reihe ausgewählt werden?</h3>
                    <div class="settings-input">
                        <label for="min-selected-numbers"></label><input type="number" id="min-selected-numbers"
                                                                         value="1" min="1" max="49">
                    </div>
                </div>
                <div class="settings-section">
                    <h3>Sollen die Zahlen sortiert werden?</h3>
                    <div class="settings-input">
                        <label for="sort-numbers"></label><input type="checkbox" id="sort-numbers">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <button id="toggle-button" class="toggle-button"></button>

    <div class="lotto-fields-container" id="lotto-fields">
        @foreach($selectedNumbers as $key => $numbers)
            <div class="lotto-section">
                <h2 class="lotto-grid-h2"> {{ $key }}</h2>
                <div class="lotto-grid">
                    <div class="number-row">
                        @php
                            $count = 0;
                            $totalNumbers = 49;
                            $numbersPerRow = 7;
                            $remainingNumbers = $totalNumbers;
                        @endphp
                        @for($i = 1; $i <= $totalNumbers; $i++)
                            <div class="number-cell {{ in_array($i, $numbers) ? 'selected' : '' }}">
                                {{ $i }}
                            </div>
                            @php
                                $count++;
                                $remainingNumbers--;
                                if ($count % $numbersPerRow == 0 && $remainingNumbers > 0) {
                                    echo '</div><div class="number-row">';
                                }
                            @endphp
                        @endfor
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
@endsection
