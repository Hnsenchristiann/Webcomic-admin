<?php

namespace App\Filters;

class WhereNull extends Filter{
    public $noParam = false;
    protected function applyFilter($builder){
        return $builder->whereNull($this->getRequestFilterValue());
    }
}
