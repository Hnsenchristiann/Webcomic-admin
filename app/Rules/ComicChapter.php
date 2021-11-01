<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class ComicChapter implements Rule
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
        $rules = [
            'chapter' => ['required', 'integer'],
            'token_price' => ['required', 'integer'],
            'token_price_ar' => ['integer', 'nullable'],
            'image_url' => ['required', 'is_uri_or_url'],
            'pages' => [new ChapterPage()]
        ];

        foreach($value as $val){
            $validator = Validator::make($val, $rules);
            if($validator->fails()){
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
        return 'Chapter object is fucked up somewhere.';
    }
}
