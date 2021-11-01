<?php

namespace App\Filters;

use App\Models\TokenTransaction;

class TransactionsWhereInChapter extends Filter{
    public $noParam = false;
    protected $onlyModels = [
        TokenTransaction::class,
    ];
    protected function applyFilter($builder){
        $chapters = $this->getRequestFilterValue();
        $chapters = gettype($chapters) == 'string' ? json_decode($chapters) : $chapters;
        return $builder->whereIn('transactionable_id', $chapters);
    }
}
