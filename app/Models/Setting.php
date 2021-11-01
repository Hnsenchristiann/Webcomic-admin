<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $guarded = [];

    public static function setValue($name, $value){
        return self::updateOrCreate(
            ['name' => $name],
            ['values' => json_encode($value)]
        );
    }

    public static function getValue($name){
        return json_decode(self::where('name', $name)->first()->values, true);
    }

    public static function getValues($names){
        $settingsArr = Setting::whereIn('name', $names)->get()->map->only('values', 'name');
        $retVal = [];
        foreach($settingsArr->all() as $idx => $val){
            $retVal[$val['name']] = json_decode($val['values'], true);
        }
        return $retVal;
    }
}
