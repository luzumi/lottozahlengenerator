<?php

namespace App\Services;

use GuzzleHttp\Exception\GuzzleException;

class XRapidApi
{
    public function __construct()
    {
    }

    /**
     * @throws GuzzleException
     */
    public function getLottoResults(string $date)
    {
        $client = new \GuzzleHttp\Client();

        $response = $client->request('GET',
            'https://lotto-draw-results-global.p.rapidapi.com/get_result/de/lotto/' . $date, [
                'headers' => [
                    'X-RapidAPI-Host' => 'lotto-draw-results-global.p.rapidapi.com',
                    'X-RapidAPI-Key' => '4af0666576msh00427f239ee54adp179683jsn6f25a6ddb604',
                ],
                'verify' => false,
            ]);

        //make response to json
        return json_decode($response->getBody()->getContents());
    }
}
