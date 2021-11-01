<?php

namespace App\Filters;

use App\Models\Chapter;
use App\Models\TokenTransaction;

class TransactionsBelongToComic extends Filter{
    public $noParam = false;
    protected $onlyModels = [
        TokenTransaction::class,
    ];
    protected function applyFilter($builder){
        $qv = $this->getRequestFilterValue();
        return $builder->whereHasMorph('transactionable',
            [Chapter::class],
            function($q)use($qv){
                $q->where('comic_id', $qv);
            }
        );
    }
}
