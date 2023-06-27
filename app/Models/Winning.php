<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Winning extends Model
{
    protected $fillable = [
        'draw_id',
        'winning_grade_id',
        'winners',
        'payout',
    ];

    public function drawing(): BelongsTo
    {
        return $this->belongsTo(Drawing::class);
    }

    public function winningGrade(): BelongsTo
    {
        return $this->belongsTo(WinningGrade::class);
    }

}
