<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Cache;

class GoodsGroupInfo extends Model
{
    use \App\Traits\ModelHelper;
    protected $hidden = [];
    protected $guarded = [];
    protected $fillable = [];
    protected $table = 'goods_group_info';

    public static function getInfo($id)
    {
        $key = 'group_info_id_' . $id;
        if (Cache::has($key)) {
            $info = Cache::get($key);
        } else {
            $info = self::where('goods_id', $id)->orderBy('num', 'asc')->get();
            Cache::put($key, $info, 1440);
        }

        return $info;
    }
}
