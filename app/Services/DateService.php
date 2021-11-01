<?php

namespace App\Services;

use Carbon\Carbon;

class DateService{
    public $date;
    public function __construct()
    {
        $this->date = Carbon::now();
    }
}
