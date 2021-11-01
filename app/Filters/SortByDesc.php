<?php

namespace App\Filters;

class SortByDesc extends Filter{
    public $noParam = false;
    protected function applyFilter($builder){
        return $builder->orderByDesc($this->getRequestFilterValue());
    }
}
