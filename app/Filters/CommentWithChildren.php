<?php

namespace App\Filters;

use App\Models\Comment;

class CommentWithChildren extends Filter{
    public $noParam = false;
    protected $onlyModels = [
        Comment::class,
    ];
    protected function applyFilter($builder){
        return $builder->with('allChildrenWithCommenter');
    }
}
