<?php

namespace App\Models;

use App\Filters\Get;
use App\Filters\Limit;
use App\Filters\Search;
use App\Filters\Select;
use App\Filters\With;
use App\Traits\Pipeable;
use Illuminate\Database\Eloquent\Model;
use tizis\laraComments\Contracts\ICommentable;
use tizis\laraComments\Traits\Commentable;

class Comic extends Model implements ICommentable
{
    use Commentable;
    use Pipeable;
    protected $guarded = [];

    public function pipeable(){
        return [
            Get::class,
            With::class,
            Limit::class,
            Search::class,
            Select::class,
        ];
    }

    public function transactions(){
        return $this->hasManyThrough(TokenTransaction::class, Chapter::class, 'comic_id' , 'transactionable_id', 'id', 'id');
    }

    public function authors(){
        return $this->belongsToMany(Author::class);
    }

    public function pages(){
        return $this->hasMany(Page::class);
    }

    public function chapters(){
        return $this->hasMany(Chapter::class);
    }

    public function purchasedBy(){
        $whereName = implode('->', ['purchase_history', $this->id, 'id']);
        return User::where($whereName, $this->id)->get();
    }

    public function getTotalTokensAttribute(){
        $chapters = $this->chapters->pluck('id');
        return TokenTransaction::where('transactionable_type', Chapter::class)->whereIn('transactionable_id', $chapters)->get()->sum('token_amount');
    }

    // public function comments(){
    //     return $this->hasMany(Comment::class);
    // }

    public function favorited(){
        return $this->belongsToMany(User::class);
    }

    protected static function boot(){
        parent::boot();
        parent::deleting(function($comic){
            $comic->chapters->map(function($cpt){$cpt->delete();});
            $comic->comments->map(function($cmt){$cmt->delete();});
            $comic->authors()->detach();
        });
    }
}
