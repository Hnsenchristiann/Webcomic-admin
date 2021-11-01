<?php

namespace App\Models;

use App\Traits\Pipeable;
use Illuminate\Database\Eloquent\Model;
use tizis\laraComments\Contracts\ICommentable;
use tizis\laraComments\Traits\Commentable;
use Illuminate\Support\Facades\URL;

class Page extends Model
{
    use Commentable;
    use Pipeable;
    protected $guarded = [];

    public function comic(){
        return $this->belongsTo(Comic::class);
    }

    public function chapter(){
        return $this->belongsTo(Chapter::class);
    }

    public function getImageFileName(){
        $boom = array_values(explode('/', $this->image_url));
        return end($boom);
    }

    public function getSignedImageUrl($lifetime = 30){
        return URL::temporarySignedRoute('api.image.fetch', now()->addSeconds($lifetime), ['filename' => $this->getImageFileName()]);
    }
}
