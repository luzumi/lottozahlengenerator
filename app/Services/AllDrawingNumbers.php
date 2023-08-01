<?php

namespace App\Services;

use App\Models\LottoNumber;
use Illuminate\Database\Eloquent\Collection;
use LaravelIdea\Helper\App\Models\_IH_LottoNumber_C;

class AllDrawingNumbers
{
    private array|_IH_LottoNumber_C|Collection $allDrawings;

    public function __construct()
    {
        $this->allDrawings = LottoNumber::with('drawing')->get();
    }

    /**
     * @return LottoNumber[]|Collection|_IH_LottoNumber_C
     */
    public function getAllDrawings(): Collection|_IH_LottoNumber_C|array
    {
        return $this->allDrawings;
    }


}
