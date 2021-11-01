<?php

namespace App\Filters;

class With extends Filter{
    public $noParam = false;
    protected function applyFilter($builder){
        return $builder->with($this->getRequestFilterValue());
    }
}
