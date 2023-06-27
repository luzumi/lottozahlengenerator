<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Drawing extends Model
{
    protected $table = 'draws';

    protected $fillable = [
        'year',
        'draw_date',
        'calendar_week',
        'draw_type',
    ];

    public function winnings(): HasMany
    {
        return $this->hasMany(Winning::class);
    }

    public function lottoNumbers(): HasMany
    {
        return $this->hasMany(LottoNumber::class);
    }
}
