<?php

namespace App\Http\Controllers;

use App\Jobs\CalculateLottoNumbers;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Welcome extends Controller
{
    public  function showWelcomePage(Request $request)
    {
        // Generate a unique ID and store it in the session
        $uniqueId = Str::random(10);
        $request->session()->put('uniqueId', $uniqueId);

        // Dispatch the job to calculate the lotto numbers
        CalculateLottoNumbers::dispatch($uniqueId);

        // Render the welcome page
        return view('welcome');
    }

}
