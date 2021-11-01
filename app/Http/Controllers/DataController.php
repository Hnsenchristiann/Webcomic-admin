<?php

namespace App\Http\Controllers;

use App\Filters\TransactionsWhereChapter;
use App\Filters\TransactionsWhereInChapter;
use App\Filters\TransactionsWhereType;
use App\Filters\WhereCreatedAfter;
use App\Filters\WhereCreatedBefore;
use App\Filters\WhereUserId;
use App\Models\Comic;
use App\Models\TokenTransaction;
use App\Services\DateService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DataController extends Controller
{
    private function aggregateTotalTokensTransactionData($pipeThrough){
        $transactionObject = TokenTransaction::pipe(null, $pipeThrough);
        $bucketObject = [];
        $transactionObject->each(function($item, $key)use(&$bucketObject){
            if(empty($bucketObject[$item->bucket_date])){
                $bucketObject[$item->bucket_date] = $item->token_amount;
            }else{
                $bucketObject[$item->bucket_date] += $item->token_amount;
            }
        });
        return $bucketObject;
    }

    private function aggregateTransactionData($pipeThrough){
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

    private function getLengthInDays($start, $end){
        $retVal = [];
        $len = (int)round(abs($end->timestamp - $start->timestamp) / (24 * 60 * 60));
        for($x = 0; $x < $len; $x++){
            $date = Carbon::parse($start)->tz('Asia/Jakarta')->addDays($x)->format('d-m-Y');
            $retVal[] = $date;
        }

        return $retVal;
    }

    public function getTotalTokensTransactionData(){
        $begin = request()->filled('where_created_after') ? Carbon::parse(request('where_created_after'))->tz('Asia/Jakarta') :
            Carbon::now()->tz('Asia/Jakarta')->addDays(-7);
        $end = request()->filled('where_created_before') ? Carbon::parse(request('where_created_before'))->tz('Asia/Jakarta') :
            Carbon::now()->tz('Asia/Jakarta');
        $transactionBucket = $this->aggregateTotalTokensTransactionData([]);
        $datesArray = $this->getLengthInDays($begin, $end);
        $transactionAmounts = [];
        foreach($datesArray as $key => $dateObj){
            if(!empty($transactionBucket[$dateObj])){
                $transactionAmounts[$key] = $transactionBucket[$dateObj];
            }else{
                $transactionAmounts[$key] = 0;
            }
        }

        return response()->json([
            'transaction_bucket' => $transactionAmounts,
            'dates' => $datesArray
        ], 200);
    }

    public function getDailyTransactionData($startDate = 'today - 7 days', $endDate = 'today'){
        $begin = Carbon::parse($startDate)->tz('Asia/Jakarta');
        $end = Carbon::parse($endDate)->tz('Asia/Jakarta');

        $purchaseTokenPipe = [
            WhereCreatedAfter::class => $begin,
            WhereCreatedBefore::class => $end,
            TransactionsWhereType::class => 'purchase_token'
        ];
        $purchaseComicPipe = [
            WhereCreatedAfter::class => $begin,
            WhereCreatedBefore::class => $end,
            TransactionsWhereType::class => 'purchase_comic'
        ];

        $purchaseTokenBucket = $this->aggregateTransactionData($purchaseTokenPipe);
        $purchaseComicBucket = $this->aggregateTransactionData($purchaseComicPipe);

        $datesArray = $this->getLengthInDays($begin, $end);

        $comicAmounts = [];
        $tokenAmounts = [];
        foreach($datesArray as $key => $dateObj){
            if(!empty($purchaseComicBucket[$dateObj])){
                $comicAmounts[$key] = $purchaseComicBucket[$dateObj];
            }else{
                $comicAmounts[$key] = 0;
            }
            if(!empty($purchaseTokenBucket[$dateObj])){
                $tokenAmounts[$key] = $purchaseTokenBucket[$dateObj];
            }else{
                $tokenAmounts[$key] = 0;
            }
        }

        return response()->json([
            'comic_bucket' => $comicAmounts,
            'token_bucket' => $tokenAmounts,
            'dates' => $datesArray
        ], 200);
    }

    public function getUserTransactionData($userId, $startDate = 'today - 7 days', $endDate = 'today'){
        $begin = Carbon::parse($startDate)->tz('Asia/Jakarta');
        $end = Carbon::parse($endDate)->tz('Asia/Jakarta');
        $purchaseComicPipe = [
            WhereCreatedAfter::class => $begin,
            WhereCreatedBefore::class => $end,
            WhereUserId::class => $userId,
            TransactionsWhereType::class => 'purchase_comic'
        ];
        $purchaseTokenPipe = [
            WhereCreatedAfter::class => $begin,
            WhereCreatedBefore::class => $end,
            WhereUserId::class => $userId,
            TransactionsWhereType::class => 'purchase_token'
        ];

        $purchaseTokenBucket = $this->aggregateTransactionData($purchaseTokenPipe);
        $purchaseComicBucket = $this->aggregateTransactionData($purchaseComicPipe);

        $datesArray = $this->getLengthInDays($begin, $end);

        $comicAmounts = [];
        $tokenAmounts = [];
        foreach($datesArray as $key => $dateObj){
            if(!empty($purchaseComicBucket[$dateObj])){
                $comicAmounts[$key] = $purchaseComicBucket[$dateObj];
            }else{
                $comicAmounts[$key] = 0;
            }
            if(!empty($purchaseTokenBucket[$dateObj])){
                $tokenAmounts[$key] = $purchaseTokenBucket[$dateObj];
            }else{
                $tokenAmounts[$key] = 0;
            }
        }

        return response()->json([
            'comic_bucket' => $comicAmounts,
            'token_bucket' => $tokenAmounts,
            'dates' => $datesArray
        ], 200);
    }

    public function getComicTransactionData($comicId, $startDate = 'today - 7 days', $endDate = 'today'){
        $chapters = Comic::with('chapters')->find($comicId)->chapters->pluck('id')->toArray();
        $begin = Carbon::parse($startDate)->tz('Asia/Jakarta');
        $end = Carbon::parse($endDate)->tz('Asia/Jakarta');
        $purchaseTokenPipe = [
            WhereCreatedAfter::class => $begin,
            WhereCreatedBefore::class => $end,
            TransactionsWhereInChapter::class => $chapters,
            TransactionsWhereType::class => 'purchase_comic'
        ];

        $purchaseComicBucket = $this->aggregateTransactionData($purchaseTokenPipe);

        $datesArray = $this->getLengthInDays($begin, $end);

        $comicAmounts = [];
        foreach($datesArray as $key => $dateObj){
            if(!empty($purchaseComicBucket[$dateObj])){
                $comicAmounts[$key] = $purchaseComicBucket[$dateObj];
            }else{
                $comicAmounts[$key] = 0;
            }
        }

        return response()->json([
            'comic_bucket' => $purchaseComicBucket,
            'dates' => $datesArray
        ], 200);
    }

    public function getChapterTransactionData($chapterId, $startDate = 'today - 7 days', $endDate = 'today'){
        $begin = Carbon::parse($startDate)->tz('Asia/Jakarta');
        $end = Carbon::parse($endDate)->tz('Asia/Jakarta');
        $purchaseTokenPipe = [
            WhereCreatedAfter::class => $begin,
            WhereCreatedBefore::class => $end,
            TransactionsWhereChapter::class => $chapterId,
            TransactionsWhereType::class => 'purchase_comic'
        ];

        $purchaseComicBucket = $this->aggregateTransactionData($purchaseTokenPipe);

        $datesArray = $this->getLengthInDays($begin, $end);

        $comicAmounts = [];
        foreach($datesArray as $key => $dateObj){
            if(!empty($purchaseComicBucket[$dateObj])){
                $comicAmounts[$key] = $purchaseComicBucket[$dateObj];
            }else{
                $comicAmounts[$key] = 0;
            }
        }

        return response()->json([
            'comic_bucket' => $purchaseComicBucket,
            'dates' => $datesArray
        ], 200);
    }
}
