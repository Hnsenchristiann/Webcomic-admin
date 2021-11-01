<?php
namespace App\Helpers;

use tizis\laraComments\Contracts\Comment;

class FloatingServices{
    public static function ratingRecalculation(Comment $comment): int
    {
        $rating = 0;

        foreach ($comment->votes as $vote) {
            $rating += $vote->commenter_vote === 0 ? 0 : 1;
        }

        $comment->rating = $rating;
        $comment->save();

        return $rating;
    }

    public static function normalRandom($min, $max, $repeat = 2){
        $randVal = 0;
        for($x = 0; $x < $repeat; $x++){
            $randVal += mt_rand($min, $max);
        }
        return (int)round($randVal / $repeat);
    }

    public static function testNormalRandom($sample, $repeat){
        $arr = [];
        for($x = 0; $x < $sample; $x++){
            $rand = self::normalRandom(0, 100, $repeat);
            if(empty($arr[$rand])){
                $arr[$rand] = 1;
            }else{
                $arr[$rand]++;
            }
        }
        ksort($arr);
        return $arr;
    }
}
