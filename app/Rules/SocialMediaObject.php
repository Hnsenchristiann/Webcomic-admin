<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class SocialMediaObject implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if(empty($value)){
            return false;
        }
        $decodedValue = json_decode($value, true);
        if(empty($decodedValue)){
            return false;
        }
        $keys = array_keys($decodedValue);
        foreach($keys as $key){
            if(empty($decodedValue[$key])){
                return false;
            }
            if(empty($key)){
                return false;
            }
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Social media object is wrong.';
    }
}
