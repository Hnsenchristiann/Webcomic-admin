<?php

namespace App\Filters;

use App\Helpers\Query;

class Search extends Filter{
    public $noParam = false;
    private $columns = [
        "App\Models\Comic" => [
            'title', 'tags', 'genres', ['authors' => 'name'],
        ],
        "App\Models\Author" => [
            'name'
        ],
        "App\Models\User" => [
            'name', 'email'
        ],
        "App\Models\Comment" => [
            'comment', ['commenter' => 'name'], ['commenter' => 'email']
        ],
        "App\Models\TokenTransaction" => [
            ['user' => 'name'], ['user' => 'email']
        ]
    ];
    protected function applyFilter($builder){
        $modelName = get_class($builder->getModel());
        return Query::buildWheresBuilder($builder, $this->getRequestFilterValue(), $this->columns[$modelName]);
    }
}
