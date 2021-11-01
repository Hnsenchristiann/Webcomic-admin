<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class Settings implements Rule
{
    private $setting;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($setting)
    {
        $this->setting = $setting;
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
        switch($this->setting){
            case 'site.social_media_links':
                $socMeds = array_keys($value);
                if(count($socMeds) < 1){
                    return false;
                }
                $myRules = [];
                foreach($socMeds as $socMed){
                    $myRules[$socMed] = ['required', 'url'];
                }
                $validator = Validator::make($value, $myRules);
                if($validator->fails()){
                    return false;
                }
                break;
            case 'dashboard.banners':
                foreach($value as $banner){
                    $keys = array_keys($banner);
                    $requiredKeys = ['title', 'route', 'image'];
                    $keysValid = count(array_diff($keys, $requiredKeys)) + count(array_diff($requiredKeys, $keys));
                    if($keysValid > 0){
                        return false;
                    }
                    $route = $banner['route'];
                    unset($banner['route']);

                    $routeRules = [
                        'name' => ['required', 'string'],
                        'params' => ['nullable', 'array']
                    ];
                    $routeValidator = Validator::make($route, $routeRules);
                    if($routeValidator->fails()){
                        return false;
                    }

                    $myRules = [
                        'image' => ['required', 'is_uri_or_url'],
                        'title' => ['required', 'string']
                    ];
                    $validator = Validator::make($banner, $myRules);
                    if($validator->fails()){
                        return false;
                    }
                }
                break;
            case 'dashboard.tags':
                if(count($value) < 1){
                    return false;
                }
                break;
            case 'token.prices':
                $myRules = [
                    'price' => ['required', 'numeric'],
                    'amount' => ['required', 'integer'],
                    'special_tag' => ['nullable', 'string']
                ];
                foreach($value as $price){
                    $priceObjKeys = array_keys($price);
                    $keysValid = array_diff($priceObjKeys, ['price', 'amount', 'special_tag']);
                    $validKeys = array_diff(['price', 'amount'], $priceObjKeys);
                    if(count($keysValid) > 0 || count($validKeys) > 0){
                        return false;
                    }
                    $validator = Validator::make($price, $myRules);
                    if($validator->fails()){
                        return false;
                    }
                }
                break;
            default:
                return false;
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
        return 'The validation error message.';
    }
}
