<?php

namespace App\Services;

use App\Helpers\Uploader;
use App\Models\Page;
use App\Models\Setting;

class SettingService extends Service{
    public $model = Page::class;
    public $fields = [
        "name",
        "values",
    ];

    public static function processValues($name, $values){
        switch($name){
            case 'dashboard.tags':
            case 'dashboard.genres':
                if(gettype($values) == 'string'){
                    return json_decode($values);
                }
                return $values;
            case 'dashboard.banners':
                // data structure:
                // {
                //     title: 'testing',
                //     route: {
                //         name: 'web.comic',
                //         params: {comic: 1}
                //     },
                //     image: '/storage/media/banners/test.jpg'
                // }
                if(gettype($values) == 'string'){
                    $values = json_decode($values);
                }
                foreach($values as $key => $banner){
                    $checkType = self::checkFileType($values[$key]['image']);
                    if($checkType == 'data_uri'){
                        $file = Uploader::saveBase64File($values[$key]['image'], 'storage/media/banners/');
                    }else if($checkType == 'url'){
                        $file = ['pathname' => $values[$key]['image']];
                    }else{
                        abort(400, "Bad request!");
                    }
                    $values[$key]["image"] = $file['pathname'];
                }
                return $values;
            case 'token.prices':
                if(gettype($values) == 'string'){
                    $values = json_decode($values);
                }
                return $values;
            default:
                return $values;
        }
    }

    public function saveBannerSetting($bannerObjs){
        if(gettype($bannerObjs) == 'string'){
            $bannerObjs = json_decode($bannerObjs, true);
        }
        foreach($bannerObjs as $idx => $banner){
            $bannerObjs[$idx]['image'] = $this->saveBannerImage($bannerObjs[$idx]['image']);
        }

        return Setting::updateOrCreate(
            ['name' => 'dashboard.banner'],
            [
                'values' => json_encode($bannerObjs, JSON_FORCE_OBJECT)
            ]
        );
    }

    public function saveBannerImage($base64File){
        $file = Uploader::saveBase64File($base64File, 'storage/media/banners/');
        return $file['pathname'];
    }
}

/*
$bannerObjs = [
    {
        title: 'testing',
        route: {
            name: 'web.comic',
            params: {comic: 1}
        },
        image: data uri => '/storage/media/banners/test.jpg'
    },
    {
        title: 'testing',
        route: {
            name: 'web.comic',
            params: {comic: 1}
        },
        image: '/storage/media/banners/test.jpg'
    },
]
*/
