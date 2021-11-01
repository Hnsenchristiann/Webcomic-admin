<?php

namespace App\Filters;

class WhereTransactionableId extends Filter{
    public $noParam = false;
    protected function applyFilter($builder){
        return $builder->where('transactionable_id', $this->getRequestFilterValue());
    }
}
