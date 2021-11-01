<?php

namespace App\Filters;

class WhereNotNull extends Filter{
    public $noParam = false;
    protected function applyFilter($builder){
        return $builder->whereNotNull($this->getRequestFilterValue());
    }
}
