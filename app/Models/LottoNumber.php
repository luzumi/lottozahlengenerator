<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LottoNumber extends Model
{
    protected $fillable = [
        'draw_id',
        'number_one',
        'number_two',
        'number_three',
        'number_four',
        'number_five',
        'number_six',
        'superzahl',
        'zusatzzahl',
    ];

    public function drawing(): BelongsTo
    {
        return $this->belongsTo(Drawing::class);
    }

}
