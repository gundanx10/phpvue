<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoodsImgs extends Model
{
    use \App\Traits\ModelHelper;
    protected $hidden = [];
    protected $guarded = [];
    protected $fillable = [];
    protected $table = 'goods_imgs';

    public static function getImg($id)
    {
        $imgs = self::select('picture')->where('goods_id', $id)->where('type', 'goods')->get();

        return collect($imgs)->map(function ($value) {
            return config('app.url') . preg_replace('/small_/', '', $value['picture']);
        });
    }

    public static function getImgFirst($id = null)
    {
        $img = self::select('picture')->where('goods_id', $id)->where('type', 'goods')->orderBy('id', 'asc')->first();
        $small_pic = '';
        $big_pic = '';

        if(!empty($img)){
            $small_pic = ImageApp::smallImg($img->picture);
            $big_pic = config('app.url') . preg_replace('/small_/', '', $img->picture);
        }

        return ['small_pic' => $small_pic, 'big_pic' => $big_pic];
    }

    public static function addImg($info)
    {
        $image = new self();
        if (isset($info["goods_id"])) {
            $image->goods_id = $info["goods_id"];
        } elseif (isset($info["product_id"])) {
            $image->product_id = $info["product_id"];
        } else {
            return false;
        }

        $image->picture = $info["picture"];
        $image->type = $info["type"];

        $image->save();
    }
}
