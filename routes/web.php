<?php

use App\Http\Controllers\LottoFieldController;
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

Route::get('/lotto', function () {
    return view('welcome');
});

//Route::get('/generate', function () {
//    return view('generate');
//});

Route::get('/lotto/generate', [LottoFieldController::class, 'showLottoNumbers'])->name('generate');
Route::get('/lotto/frequentNumbers', [LottoFieldController::class, 'frequentNumbers'])->name('frequentNumbers');
