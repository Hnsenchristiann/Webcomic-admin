<?php
namespace App\Traits;

use App\Filters\Count;
use App\Filters\Get;
use App\Filters\With;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Str;

trait Pipeable{
    protected $pipeableThrough = [
        Get::class,
        With::class,
    ];

    public function pipeable(){
        return $this->pipeableThrough;
    }

    public static function pipeCount(){
        $pipe = (new static)->pipeable();
        $pipe[0] = Count::class;

        return self::buildPipeline($pipe, self::query());
    }

    public static function pipe($pipeableObject = null, $queryParams = []){
        if(empty($pipeableObject)){
            $pipeableObject = self::query();
        }

        if(!empty($queryParams)){
            foreach($queryParams as $key => $value){
                $requestName = class_exists($key) ? Str::snake(class_basename($key)) : $key;
                request()->merge([$requestName => $value]);
            }
        }

        $self = new static;
        return self::buildPipeline($self->pipeable(), $pipeableObject);
    }

    private static function buildPipeline($pipe, $pipedObject){
        return app(Pipeline::class)
            ->send($pipedObject)
            ->through($pipe)
            ->thenReturn();
    }

}
