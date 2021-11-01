<?php

namespace App\Services;

use App\Helpers\Uploader;
use App\Models\Chapter;
use App\Models\Comic;
use App\Models\Page;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ComicService extends Service{
    public $chapters = [];
    public $model = Comic::class;
    public $fields = [
        "title",
        "description",
        "tags",
        "genres",
        "cover_url",
        "release_date",
        "is_draft",
        'author_split',
    ];
    public $relationships = [
        'chapters',
        'pages',
        'authors'
    ];

    // public static function validateData($data){
    //     $fields = (new static)->fields;
    //     $relationships = (new static)->relationships;
    //     $error = [];
    //     foreach($fields as $field){
    //         if(empty($data[$field])){
    //             $error[$field] = 'Field empty';
    //         }
    //     }
    //     foreach($relationships as $relationship){
    //         if(empty($data[$relationship])){
    //             $error[$relationship] = 'Relationship empty';
    //         }
    //     }

    //     if(!empty($error)){
    //         throw ValidationException::withMessages($error);
    //     }

    //     return true;
    // }
    private function normalizeSplit($splitObj){
        $sum = array_sum($splitObj);
        return array_map(function($v)use($sum){return round($v / $sum, 3);}, $splitObj);
    }

    public function saveCoverImage($base64File){
        $file = Uploader::saveBase64File($base64File, 'storage/media/covers/');
        $this->data['cover_url'] = $file['pathname'];
    }

    public function setData($data){
        foreach($this->fields as $field){
            if(!is_null($data[$field])){
                if(($field == 'tags' || $field == 'genres')
                    && (gettype($data[$field]) == 'array' || gettype($data[$field]) == 'object')){
                    $data[$field] = json_encode(array_values($data[$field]));
                }else if($field == 'author_split'){
                    if(gettype($data[$field]) == 'string'){
                        $data[$field] = json_decode($data[$field], true);
                    }
                    $data[$field] = json_encode($this->normalizeSplit($data[$field]));
                }else if($field === 'release_date'){
                    $data[$field] = Carbon::parse($data[$field]);
                }
                $this->data[$field] = $data[$field];
            }
        }
    }

    public function updateComicWithChaptersPages($data){
        DB::beginTransaction();
        try{
            $this->setRecord($data['id']);
            $this->record->authors()->sync($data['authors']);
            $this->setData($data);
            if(self::checkFileType($data['cover_url']) == 'data_uri'){
                $this->saveCoverImage($data['cover_url']);
            }
            $this->update();
            $currentCpt = $this->record->chapters->pluck('id')->toArray();
            $currentPg = $this->record->pages->pluck('id')->toArray();
            $chapterIds = [];
            $pageIds = [];
            $chapters = $data['chapters'];
            foreach($chapters as $chapter){
                $cptSvc = new ChapterService();
                $cptSvc->setData($chapter);
                if($cptSvc::checkFileType($chapter['image_url']) == 'data_uri'){
                    $cptSvc->savePreviewImage($chapter['image_url']);
                }
                if(empty($chapter['id'])){
                    $cptSvc->setDatum('comic_id', $this->record->id);
                    $cptSvc->create();
                }else{
                    $chapterIds[] = $chapter['id'];
                    $cptSvc->setRecord($chapter['id']);
                    $cptSvc->update();
                }
                $pages = $chapter['pages'];
                foreach($pages as $page){
                    $pgSvc = new PageService();
                    $pgSvc->setData($page);
                    if($pgSvc::checkFileType($chapter['image_url']) == 'data_uri'){
                        $pgSvc->savePageImage($chapter['image_url']);
                    }
                    if(empty($page['id'])){
                        $pgSvc->setDatum('chapter_id', $cptSvc->record->id);
                        $pgSvc->setDatum('comic_id', $this->record->id);
                        $pgSvc->create();
                    }else{
                        $pageIds[] = $page['id'];
                        $pgSvc->setRecord($page['id']);
                        $pgSvc->update();
                    }
                    $cptSvc->pages[] = $pgSvc;
                }
                $this->chapters[] = $cptSvc;
            }
            $cptToDelete = array_diff($currentCpt, $chapterIds);
            $pgToDelete = array_diff($currentPg, $pageIds);
            if(!empty($cptToDelete)){
                Chapter::whereIn('id', $cptToDelete)->delete();
            }
            if(!empty($pgToDelete)){
                Page::whereIn('id', $pgToDelete)->delete();
            }
            DB::commit();
        }catch(QueryException $e){
            DB::rollBack();
            throw $e;
        }
        return [
            'status' => 'success'
        ];
    }

    public function createComicWithChaptersPages($data){
        //we create the comic first
        DB::beginTransaction();
        try{
            $this->setData($data);
            // if(!empty($data['base64_cover'])){
            //     $this->saveCoverImage($data['base64_cover']);
            // }
            if(self::checkFileType($data['cover_url']) == 'data_uri'){
                $this->saveCoverImage($data['cover_url']);
            }
            $this->create();
            $this->record->authors()->sync($data['authors']);
            $chapters = $data['chapters'];
            foreach($chapters as $chapter){
                $cptSvc = new ChapterService();
                $chapter['comic_id'] = $this->record->id;
                $cptSvc->setData($chapter);
                // if(!empty($chapter['base64_preview'])){
                //     $cptSvc->savePreviewImage($chapter['base64_preview']);
                // }
                if($cptSvc::checkFileType($chapter['image_url']) == 'data_uri'){
                    $cptSvc->savePreviewImage($chapter['image_url']);
                }
                $cptSvc->create();
                $pages = $chapter['pages'];
                foreach($pages as $page){
                    $pgSvc = new PageService();
                    $page['chapter_id'] = $cptSvc->record->id;
                    $page['comic_id'] = $this->record->id;
                    $pgSvc->setData($page);
                    // if(!empty($page['base64_page'])){
                    //     $pgSvc->savePageImage($page['base64_page']);
                    // }
                    if($pgSvc::checkFileType($chapter['image_url']) == 'data_uri'){
                        $pgSvc->savePageImage($chapter['image_url']);
                    }
                    $pgSvc->create();
                    $cptSvc->pages[] = $pgSvc;
                }
                $this->chapters[] = $cptSvc;
            }
            DB::commit();
        }catch(QueryException $e){
            DB::rollBack();
            throw $e;
            // return [
            //     'status' => 'error',
            //     'error' => $e
            // ];
        }
        return [
            'status' => 'success'
        ];
    }
}
