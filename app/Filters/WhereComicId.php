<?php

namespace App\Filters;

class WhereComicId extends Filter{
    public $noParam = false;
    protected function applyFilter($builder){
        return $builder->where('comic_id', $this->getRequestFilterValue());
    }
}
