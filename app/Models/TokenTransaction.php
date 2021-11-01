<?php

namespace App\Models;

use App\Filters\Get;
use App\Filters\Limit;
use App\Filters\Search;
use App\Filters\SortByAsc;
use App\Filters\SortByDesc;
use App\Filters\TransactionsBelongToAuthor;
use App\Filters\TransactionsBelongToComic;
use App\Filters\TransactionsWhereChapter;
use App\Filters\TransactionsWhereType;
use App\Filters\WhereCreatedAfter;
use App\Filters\WhereCreatedBefore;
use App\Filters\WhereNotNull;
use App\Filters\WhereNull;
use App\Filters\WhereTransactionableId;
use App\Filters\WhereUserId;
use App\Filters\With;
use App\Traits\Pipeable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TokenTransaction extends Model
{
    use Pipeable;
    protected $guarded = [];

    public function pipeable(){
        return [
            Get::class,
            TransactionsWhereChapter::class,
            TransactionsWhereType::class,
            TransactionsBelongToComic::class,
            TransactionsBelongToAuthor::class,
            WhereNotNull::class,
            WhereNull::class,
            WhereTransactionableId::class,
            WhereCreatedAfter::class,
            WhereCreatedBefore::class,
            WhereUserId::class,
            SortByAsc::class,
            SortByDesc::class,
            With::class,
            Limit::class,
            Search::class,
        ];
    }

    public function transactionable(){
        return $this->morphTo();
    }

    public function authors(){
        return $this->belongsToMany(Author::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function getBucketDateAttribute()
    {
        return Carbon::parse($this->created_at)->format('d-m-Y');
    }

    public static function getTransactionsWithAuthorShare($authorId){
        return DB::table('token_transactions')
            ->select(DB::raw("
                *,
                token_transactions.token_amount * JSON_EXTRACT(token_transactions.descriptor, '$.\"author_split\".\"" . $authorId . "\"') as author_share
            "))->join('author_token_transaction', function ($q) {
                $q->on('author_token_transaction.token_transaction_id', '=', 'token_transactions.id');
            })->where('author_token_transaction.author_id', '=', $authorId)->first();
    }
}
