<?php

namespace App\Services;

use App\Models\LottoNumber;

class LottoNumberGenerator
{
    public function generateRandomNumbers(): array
    {
        $data = LottoNumber::select(
            'number_one',
            'number_two',
            'number_three',
            'number_four',
            'number_five',
            'number_six'
        )->get()->toArray();

        $number_keys = ['number_one', 'number_two', 'number_three', 'number_four', 'number_five', 'number_six'];
        $frequency_dict = array_fill_keys($number_keys, []);

        foreach ($data as $entry) {
            foreach ($number_keys as $key) {
                $number = $entry[$key];
                if (!isset($frequency_dict[$key][$number])) {
                    $frequency_dict[$key][$number] = 0;
                }
                $frequency_dict[$key][$number]++;
            }
        }

        $probability_dict = array_fill_keys($number_keys, []);
        $total_draws = count($data);

        foreach ($frequency_dict as $position => $frequencies) {
            foreach ($frequencies as $number => $frequency) {
                $probability_dict[$position][$number] = $frequency / $total_draws;
            }
        }

        return $this->draw_numbers($probability_dict, $number_keys);
    }

    private function draw_number($position, $probability_dict) {
        $numbers = array_keys($probability_dict[$position]);
        $probabilities = array_values($probability_dict[$position]);

        $cumulative_probabilities = [];
        $accumulator = 0.0;
        foreach ($probabilities as $probability) {
            $accumulator += $probability;
            $cumulative_probabilities[] = $accumulator;
        }

        $rand = mt_rand() / mt_getrandmax();
        foreach ($cumulative_probabilities as $i => $cumulative_probability) {
            if ($rand < $cumulative_probability) {
                return $numbers[$i];
            }
        }

        return null;
    }

    private function draw_numbers($probability_dict, $number_keys) {
        $drawn_numbers = [];
        foreach ($number_keys as $key) {
            $drawn_number = $this->draw_number($key, $probability_dict);
            $drawn_numbers[] = $drawn_number;
        }

        return $drawn_numbers;
    }

}
