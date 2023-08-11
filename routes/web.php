<?php

use App\Http\Controllers\CalculationController;
use App\Http\Controllers\LottoFieldController;
use App\Http\Controllers\LottoUpdateController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
if (App::environment('local')) {
    Debugbar::enable();
}

Route::get('/', [\App\Http\Controllers\Welcome::class, 'showWelcomePage'])->name('welcome');

//Route::get('/generate', function () {
//    return view('generate');
//});

Route::get('/generate', [LottoFieldController::class, 'showLottoNumbers'])->name('generate');
Route::get('/frequentNumbers', [LottoFieldController::class, 'frequentNumbers'])->name('frequentNumbers');
Route::get('/update-draws', [LottoUpdateController::class, 'updateDatabase']);
Route::get('/api/last-draw', [LottoUpdateController::class, 'lastDraw'])->name('last-draw');

Route::post('/api/calculate', [LottoFieldController::class, 'calculate'])->name('calculate');

Route::get('/csrf-token', function() {
        return response()->json([
        'csrfToken' => csrf_token(),
    ]);
});

