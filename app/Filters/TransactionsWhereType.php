<?php

namespace App\Filters;

use App\Models\TokenTransaction;

class TransactionsWhereType extends Filter{
    public $noParam = false;
    protected $onlyModels = [
        TokenTransaction::class,
    ];
    protected function applyFilter($builder){
        // try{
        //     $retVal = $builder->whereJsonContains('descriptor->type', $this->getRequestFilterValue());
        // }catch(\Exception $e){
        //     $retVal = $builder->where('descriptor', 'like', '%' . $this->getRequestFilterValue() . '%');
        // }
        return $builder->whereJsonContains('descriptor->type', $this->getRequestFilterValue());
    }
}
