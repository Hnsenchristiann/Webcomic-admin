<?php

namespace App\Services;

use App\Helpers\Uploader;
use App\Models\Page;

class PageService extends Service{
    public $model = Page::class;
    public $fields = [
        "chapter_id",
        "image_url",
        "config",
        "comic_id",
        "scene",
        "section",
    ];

    public function savePageImage($base64File){
        $file = Uploader::saveBase64File($base64File, 'storage/media/comics/');
        $this->data['image_url'] = $file['pathname'];
    }
}
