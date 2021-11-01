<?php

namespace App\Filters;

class WhereCommenterId extends Filter{
    public $noParam = false;
    protected function applyFilter($builder){
        return $builder->where('commenter_id', $this->getRequestFilterValue());
    }
}
