<?php

namespace App\Filters;

class Select extends Filter{
    public $noParam = false;
    protected function applyFilter($builder){
        return $builder->select($this->getRequestFilterValue());
    }
}
