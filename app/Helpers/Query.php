<?php

namespace App\Helpers;

class Query{
    public static function buildWheresBuilder($builder, $query, $columns){
        return $builder->where(function($q)use($query, $columns){
            foreach($columns as $idx => $column){
                if($idx == 0){
                    if(gettype($column) === 'string'){
                        $q->where([[$column, 'LIKE', "%{$query}%"]]);
                    }else if(gettype($column) === 'array'){
                        $rel = array_keys($column)[0];
                        $relCol = $column[$rel];
                        $q->whereHas($rel, function($q)use($relCol, $query){
                            $q->where([[$relCol, 'LIKE', "%{$query}%"]]);
                        });
                    }
                }else{
                    if(gettype($column) === 'string'){
                        $q->orWhere([[$column, 'LIKE', "%{$query}%"]]);
                    }else if(gettype($column) === 'array'){
                        $rel = array_keys($column)[0];
                        $relCol = $column[$rel];
                        $q->orWhereHas($rel, function($q)use($relCol, $query){
                            $q->where([[$relCol, 'LIKE', "%{$query}%"]]);
                        });
                    }
                }
            }
        });
    }

    public static function buildWheres($model, $query, $columns){
        return $model::where(function($q)use($query, $columns){
            foreach($columns as $idx => $column){
                if($idx == 0){
                    if(gettype($column) === 'string'){
                        $q->where([[$column, 'LIKE', "%{$query}%"]]);
                    }else if(gettype($column) === 'array'){
                        $rel = array_keys($column)[0];
                        $relCol = $column[$rel];
                        $q->whereHas($rel, function($q)use($relCol, $query){
                            $q->where([[$relCol, 'LIKE', "%{$query}%"]]);
                        });
                    }
                }else{
                    if(gettype($column) === 'string'){
                        $q->orWhere([[$column, 'LIKE', "%{$query}%"]]);
                    }else if(gettype($column) === 'array'){
                        $rel = array_keys($column)[0];
                        $relCol = $column[$rel];
                        $q->orWhereHas($rel, function($q)use($relCol, $query){
                            $q->where([[$relCol, 'LIKE', "%{$query}%"]]);
                        });

                    }
                }
            }
        });
    }
}
