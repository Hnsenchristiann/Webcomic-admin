<?php

namespace App\Filters;

class Limit extends Filter{
    public $noParam = false;
    protected function applyFilter($builder){
        return $builder->limit($this->getRequestFilterValue());
    }
}
