<?php

namespace App\Services;

use App\Models\TokenTransaction;

class DateService{
    public $date;

    public static function aggregateTransactionData($pipeThrough)
    {
        $transactionObject = TokenTransaction::pipe(null, $pipeThrough);
        $bucketObject = [];
        $transactionObject->each(function($item, $key)use(&$bucketObject){
            if(empty($bucketObject[$item->bucket_date])){
                $bucketObject[$item->bucket_date] = 1;
            }else{
                $bucketObject[$item->bucket_date] += 1;
            }
        });
        return $bucketObject;
    }
}
