<?php

namespace App\Filters;

use App\Models\Chapter;
use App\Models\TokenTransaction;

class TransactionsWhereChapter extends Filter{
    public $noParam = false;
    protected $onlyModels = [
        TokenTransaction::class,
    ];
    protected function applyFilter($builder){
        return $builder
            ->where('transactionable_type', Chapter::class)
            ->where('transactionable_id', $this->getRequestFilterValue());
    }
}
