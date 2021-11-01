<?php

namespace App\Filters;

class WhereUserId extends Filter{
    public $noParam = false;
    protected function applyFilter($builder){
        return $builder->where('user_id', $this->getRequestFilterValue());
    }
}
