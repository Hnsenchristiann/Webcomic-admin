<?php

namespace App\Models;

use App\Filters\Get;
use App\Filters\Select;
use App\Filters\WhereComicId;
use App\Traits\Pipeable;
use Illuminate\Database\Eloquent\Model;
use tizis\laraComments\Contracts\ICommentable;
use tizis\laraComments\Traits\Commentable;

class Chapter extends Model implements ICommentable
{
    use Commentable;
    use Pipeable;
    protected $guarded = [];

    protected function pipeable(){
        return [
            Get::class,
            WhereComicId::class,
            Select::class,
        ];
    }

    public function comic(){
        return $this->belongsTo(Comic::class);
    }

    public function pages(){
        return $this->hasMany(Page::class);
    }

    public function transactions(){
        return $this->morphMany(TokenTransaction::class, 'transactionable');
    }

    protected static function boot(){
        parent::boot();
        parent::deleting(function($chapter){
            $chapter->pages->map(function($pg){$pg->delete();});
            $chapter->comments->map(function($cmt){$cmt->delete();});
        });
    }
}
