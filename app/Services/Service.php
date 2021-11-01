<?php

namespace App\Services;

class Service{
    public $record, $model, $fields, $data;

    public function __construct(){

    }

    public static function checkFileType($object){
        $objType = gettype($object);
        if($objType != 'string'){
            return $objType;
        }
        if(substr($object, 0, 5) == 'data:'){
            return 'data_uri';
        }else{
            $slashes = explode('/', $object);
            if(count($slashes) > 1){
                return 'url';
            }
        }
        return 'unknown';
    }

    public function setDatum($field, $value){
        $this->data['field'] = $value;
    }

    public function setRecord($model){
        $this->record = gettype($model) === 'integer' ? $this->model::find($model) : $model;
    }

    public function getRecord(){
        return $this->record;
    }

    public function setData($data){
        foreach($this->fields as $field){
            if(!is_null($data[$field])){
                $this->data[$field] = $data[$field];
            }
        }
    }

    public function getData(){
        return $this->data;
    }

    public function create(){
        $this->record = $this->model::create($this->data);
    }

    public function update(){
        $this->record = tap($this->model::where('id', $this->record->id))->update($this->data)->first();
    }

    public function whereInDelete($ids){
        $this->model::whereIn('id', $ids)->delete();
    }
}
