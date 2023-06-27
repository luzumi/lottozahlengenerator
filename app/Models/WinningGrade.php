<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WinningGrade extends Model
{
    protected $fillable = [
        'Stufe',
        'Beschreibung',
    ];

    public function winnings(): HasMany
    {
        return $this->hasMany(Winning::class);
    }
}
