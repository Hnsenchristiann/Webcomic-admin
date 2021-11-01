<?php

namespace App\Filters;

use App\Models\TokenTransaction;
use Illuminate\Support\Facades\DB;

class TransactionsBelongToAuthor extends Filter{
    public $noParam = false;
    protected $onlyModels = [
        TokenTransaction::class,
    ];
    protected function applyFilter($builder){
        $qv = $this->getRequestFilterValue();
        return $builder->select(DB::raw("*, FLOOR(token_transactions.token_amount * JSON_EXTRACT(token_transactions.descriptor, '$.\"author_split\".\"" . $qv . "\"')) as token_amount"))
        ->whereHas('authors', function($q)use($qv){
            $q->where('authors.id', $qv);
        });
    }
}
