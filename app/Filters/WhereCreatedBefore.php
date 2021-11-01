<?php

namespace App\Filters;

use Carbon\Carbon;

class WhereCreatedBefore extends Filter{
    public $noParam = false;
    protected function applyFilter($builder){
        return $builder->whereDate('created_at', '<=', Carbon::parse($this->getRequestFilterValue()));
    }
}
