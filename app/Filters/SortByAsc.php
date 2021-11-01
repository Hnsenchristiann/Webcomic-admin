<?php

namespace App\Filters;

class SortByAsc extends Filter{
    public $noParam = false;
    protected function applyFilter($builder){
        return $builder->orderBy($this->getRequestFilterValue());
    }
}
