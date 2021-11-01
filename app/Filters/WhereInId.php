<?php

namespace App\Filters;

class WhereInId extends Filter{
    public $noParam = false;
    protected function applyFilter($builder){
        return $builder->whereIn('id', $this->getRequestFilterValue());
    }
}
