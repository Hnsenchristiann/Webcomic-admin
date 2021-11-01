<?php

namespace App\Services;

use App\Helpers\Uploader;
use App\Models\Chapter;
use Carbon\Carbon;

class ChapterService extends Service{
    public $pages = [];
    public $model = Chapter::class;
    public $fields = [
        "image_url",
        "comic_id",
        "chapter",
        "token_price",
        "token_price_ar",
        "release_date",
    ];

    public function savePreviewImage($base64File){
        $file = Uploader::saveBase64File($base64File, 'storage/media/previews/');
        $this->data['image_url'] = $file['pathname'];
    }

    public function setData($data){
        foreach($this->fields as $field){
            if(!is_null($data[$field])){
                if($field === 'release_date'){
                    $data[$field] = Carbon::parse($data[$field]);
                }
                $this->data[$field] = $data[$field];
            }
        }
    }
}
