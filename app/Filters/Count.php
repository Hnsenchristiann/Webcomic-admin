<?php

namespace App\Filters;

class Count extends Filter{
    public $noParam = true;
    protected function applyFilter($builder){
        return $builder->count();
    }
}
