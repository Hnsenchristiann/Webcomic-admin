<?php

namespace App\Services;

use App\Helpers\Uploader;
use App\Models\Author;
use Carbon\Carbon;

class AuthorService extends Service{
    public $pages = [];
    public $model = Author::class;
    public $fields = [
        "name",
        "description",
        "social_media_links",
        "email",
        "profile_picture_url",
        "user_id"
    ];

    public function saveProfilePicture($base64File){
        $file = Uploader::saveBase64File($base64File, 'storage/media/authors/');
        $this->data['profile_picture_url'] = $file['pathname'];
    }

    public function setData($data){
        foreach($this->fields as $field){
            if(!empty($data[$field])){
                if($field === 'release_date'){
                    $data[$field] = Carbon::parse($data[$field]);
                }
                $this->data[$field] = $data[$field];
            }
        }
    }

    public function create(){
        if(self::checkFileType($this->data['profile_picture_url']) == 'data_uri'){
            $this->saveProfilePicture($this->data['profile_picture_url']);
        }
        parent::create();
    }

    public function update(){
        if(self::checkFileType($this->data['profile_picture_url']) == 'data_uri'){
            $this->saveProfilePicture($this->data['profile_picture_url']);
        }
        parent::update();
    }
}
