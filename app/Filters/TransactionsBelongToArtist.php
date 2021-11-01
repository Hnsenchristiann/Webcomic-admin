<?php

namespace App\Filters;

class TransactionsBelongToArtist extends Filter{
    public $noParam = false;
    protected $onlyModels = [
        TokenTransaction::class,
    ];
    protected function applyFilter($builder){
        $qv = $this->getRequestFilterValue();
        return $builder->whereHasMorph('transactionable',
            [Chapter::class],
            function($q)use($qv){
                $q->whereHas('comic', function($qb)use($qv){
                    $qb->whereHas('authors', function($qd)use($qv){
                        $qd->where('authors.id', $qv);
                    });
                });
            }
        );
    }
}
