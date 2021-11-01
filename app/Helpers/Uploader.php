<?php

namespace App\Helpers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Uploader{
    public static function saveBase64File($base64Str, $pathName, $filename = null, $url = true){
        if(empty($filename)){
            $filename = Str::random(32);
        }
        list($ext, $data) = explode(';', $base64Str);
        list(, $data) = explode(',', $data);
        $data = base64_decode($data);

        $fileExt = Helpers::mime2ext(mime_content_type($base64Str));
        $put = File::put(public_path($pathName . $filename . '.' . $fileExt), $data);
        return [
            'pathname' => $url ? Storage::url(implode('/', array_diff(explode('/', $pathName . $filename . '.' . $fileExt), ['storage']))) : public_path($pathName . $filename . '.' . $fileExt),
            'status' => $put
        ];
    }
}
