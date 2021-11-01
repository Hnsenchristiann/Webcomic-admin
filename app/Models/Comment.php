<?php

namespace App\Models;

use App\Filters\CommentParentless;
use App\Filters\CommentWithChildren;
use App\Filters\Get;
use App\Filters\Limit;
use App\Filters\Search;
use App\Filters\WhereCommenterId;
use App\Filters\WhereCreatedAfter;
use App\Filters\WhereCreatedBefore;
use App\Filters\WhereUserId;
use App\Filters\With;
use App\Traits\Pipeable;
use Illuminate\Database\Eloquent\Model;
use tizis\laraComments\Entity\Comment as laraComment;

class Comment extends laraComment
{
    use Pipeable;

    public function pipeable(){
        return [
            Get::class,
            With::class,
            CommentParentless::class,
            CommentWithChildren::class,
            WhereCommenterId::class,
            WhereCreatedAfter::class,
            WhereCreatedBefore::class,
            Limit::class,
            Search::class,
        ];
    }

    public function canUserDelete(User $user){
        return true;
    }

    protected static function boot(){
        parent::boot();
        parent::deleting(function($comment){
            $comment->children->map(function($cmt){$cmt->delete();});
        });
    }

    /**
     * Recursive version of comments with commenter relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function allChildrenWithCommenter()
    {
        return $this->hasMany(config('comments.models.comment'), 'child_id')
            ->with('allChildrenWithCommenter', 'commenter', 'commentable');
    }
}
