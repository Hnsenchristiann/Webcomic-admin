<?php

namespace App\Filters;

use Closure;
use Illuminate\Support\Str;

abstract class Filter{
    public $noParam;
    protected $onlyModels = [];

    public function handle($request, Closure $next){
        $builder = $next($request);
        if(!request()->filled($this->filterName()) && !$this->noParam){
            return $builder;
        }
        $modelName = get_class($builder->getModel());
        if(empty($this->onlyModels) || in_array($modelName, $this->onlyModels)){
            return $this->applyFilter($builder);
        }else{
            return $builder;
        }
    }

    protected abstract function applyFilter($builder);

    protected function filterName(){
        return Str::snake(class_basename($this));
    }

    protected function getRequestFilterValue(){
        return request($this->filterName());
    }
}
