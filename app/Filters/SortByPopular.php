<?php

namespace App\Filters;

class SortByPopular extends Filter{
    public $noParam = false;
    protected function applyFilter($builder){
        return $builder->orderByDesc('views');
    }
}
