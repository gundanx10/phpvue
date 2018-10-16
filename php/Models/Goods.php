<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Cache;

class Goods extends Model
{
    use \App\Traits\ModelHelper;
    protected $hidden = [];
    protected $guarded = [];
    protected $fillable = [];
    protected $table = 'goods';
    protected $casts = [
        'spec' => 'array',
        'group_price' => 'array',
    ];

    public function category()
    {
        return $this->hasOne('App\Models\Category', 'id', 'cat_id');
    }

    public function merchant()
    {
        return $this->hasOne('App\Models\Merchant', 'id', 'merchant_id');
    }

    public function groupInfo()
    {
        return $this->hasMany('App\Models\GoodsGroupInfo', 'goods_id', 'id');
    }

    public function getBrand()
    {
        $baseBrand = Brand::getBase($this->brand_id);

        return $baseBrand;
    }

    public static function groupByMerchant()
    {
        $key = 'groupByMerchant';
        if (Cache::has($key)) {
            $goodsIdArray = Cache::get($key);
        } else {
            $goodsIds = self::select(\DB::raw('max(id) as id, merchant_id'))
               ->where('status', 1)
               ->groupBy('merchant_id')
               ->get()
               ->toArray();

            $goodsIdArray = [];
            foreach ($goodsIds as $ids) {
                array_push($goodsIdArray, $ids['id']);
            }
            Cache::put($key, $goodsIdArray, 1440);
        }

        return $goodsIdArray;
    }

    public function updateStatus($status)
    {
        if (in_array($status, [-1, 1])) {
            $this->status = $status;
            $this->save();

            return true;
        }

        return false;
    }

    public function getSpecList()
    {
        $arr = [];
        $products = Product::where("goods_id", $this->id)->where("status", ">", 0)->get();
        foreach ($products as $product) {
            $arr[] = [
                "spec" => $product->spec,
                "custom_code" => $product->custom_code,
                "store" => $product->store,
                "price" => $product->price,
                "image" => $product->picture,
                "group_price" => $product->group_price,
            ];
        }

        return $arr;
    }

    public function getGoodsEditData()
    {
        $arr = [
            "id" => $this->id,
            "name" => $this->name,
            "description" => $this->description,
            "spec_list" => $this->getSpecList(),
            "carriage_template_id" => $this->carriage_template_id,
            "category_id" => $this->cat_id,
            "goods_images" => [],
            "detail_images" => [],
            "promotion_images" => [],
            "group_price" => $this->group_price,
            "common" => [
                "commission" => $this->recommend_brokerage,
                "deduction" => $this->recommend_deduction,
                "sale_price" => $this->sale_price,
            ],
        ];

        if ($this->spec == ["默认" => "默认"]) {
            $arr["specs"] = [];
            $default_spec = $arr["spec_list"][0];
            $arr["common"]["store"] = $default_spec["store"];
            $arr["common"]["price"] = $default_spec["price"];
            $arr["common"]["group_price"] = $default_spec["group_price"];
            $arr["common"]["custom_code"] = $default_spec["custom_code"];
            $arr["spec_list"] = [];
        } else {
            $arr["specs"] = $this->spec;
        }

        return $arr;
    }

    public function getPicture()
    {
        $key = 'img_' . $this->id;
        if (Cache::has($key)) {
            $img = Cache::get($key);
        } else {
            $img = GoodsImgs::getImgFirst($this->id);
            Cache::put($key, $img, 1440);
        }

        $this->small_pic = $img['small_pic'];
        $this->big_pic = $img['big_pic'];

        return $this;
    }

    public function getRectanglePicture()
    {
        $this->small_pic = '';
        $this->big_pic = '';

        if (!empty($this->picture)) {
            $this->small_pic = ImageApp::smallImg($this->picture);
            $this->big_pic = config('app.url') . preg_replace('/small_/', '', $this->picture);
        }

        return $this;
    }

    public function getDetails()
    {
        $images = GoodsImgs::where('goods_id', $this->id)
                            ->where('type', 'detail')
                            ->get()->toArray();
        $details = [];
        $detailsX = [];
        foreach ($images as $image) {
            $imageInfo = ImageApp::Info($image['picture']);
            array_push($detailsX, [
              'src' => config('app.url') . preg_replace('/small_/', '', $image['picture']),
              'width' => $imageInfo['width'],
              'height' => $imageInfo['height'],
            ]);
            array_push($details, config('app.url') . preg_replace('/small_/', '', $image['picture']));
        }
        $this->details = $details;
        $this->detailsX = $detailsX;

        return $this;
    }

    public function getSpec()
    {
        if ($this->spec == ["默认" => "默认"]) {
            $this->spec = [];
        }
        if (is_array($this->spec) && !empty($this->spec)) {
            $newArray = [];
//            dd($this->spec);
            foreach ($this->spec as $k => $v) {
                $rs = '';
                if (!empty($v['values'])) {
                    $rs = collect($v['values'])->map(function ($value) {
                        return ['name' => $value];
                    });
                }

                $newArray[] = [
                'key' => $v['key'],
                'value' => $rs,
              ];
            }
            $this->spec = $newArray;
        }

        return $this;
    }

    public function getGroupBuying()
    {
        $this->groupBuying = GroupBuying::getBuying($this->id);

        return $this;
    }

    public function getFavoriteStatus()
    {
        $this->favoriteStatus = Favorite::getFavoriteStatus($this->id, 'goods');

        return $this;
    }

    /**
     * 返回商品列表.
     *
     * @param $merchant_id 商家id
     * @param $status 商品状态 0未发布 1发布中 2发布完成
     */
    public static function getGoodsLists($merchant_id, $goods_id, $goods_name, $status = 0)
    {
        $pagesize = 30;
        $query = self::where('merchant_id', '=', $merchant_id);

        if ($goods_id) {
            $query->where('id', '=', $goods_id);
        }

        if ($goods_name) {
            $query->where('name', 'like', "%{$goods_name}%");
        }

        $data = $query->orderBy("id", "desc")
            ->paginate($pagesize);

        return $data;
    }

    /**
     * 获取产品分类 模拟数据.
     *
     * @param $goods_id
     */
    public function getCategory()
    {
        $this->goodsCategory = '时尚男装';

        return $this;
    }

    /**
     * get all avilable specs for goods.
     *
     * @return array
     */
    public static function getAllSpecs()
    {
        return [
            "口味", "款式", "颜色", "尺码", "尺寸", "容量", "器型", "花型",
            "香型", "功效", "型号", "套餐", "运营商", "材质", "成份", "版本",
            "色号", "货号", "类别", "属性", "适用年龄", "重量", "适用人群",
            "组合", "品类", "度数", "时间", "地点", "地区",
        ];
    }

    public static function create($info)
    {
        DB::beginTransaction();
        try {
            if (isset($info["id"])) {
                $goods = self::find($info["id"]);
            } else {
                $goods = new self();
            }
            $goods->name = $info["name"];
            $goods->cat_id = $info["category_id"];
            $goods->merchant_id = $info["merchant_id"];
            $goods->description = $info["description"];
            $goods->sale_price = $info["common"]["sale_price"] ?? null;
            $goods->recommend_brokerage = $info["common"]["commission"] ?? null;
            $goods->recommend_deduction = $info["common"]["deduction"] ?? null;
            $goods->spec = $info["specs"] ?? ["默认" => "默认"];
            $goods->group_price = $info["group_price"];
            $goods->carriage_template_id = $info["carriage_template_id"];
            $goods->status = 0;
            if ($goods->recommend_brokerage) {
                $goods->family_leader_brokerage = $goods->recommend_brokerage * 0.6;
                $goods->family_person_brokerage = $goods->recommend_brokerage * 0.4;
            }
            if ($goods->recommend_deduction) {
                $goods->group_leader_deduction = $goods->recommend_deduction * 0.6;
                $goods->group_deduction = $goods->recommend_deduction * 0.4;
            }
            $goods->save();

            //图片
            foreach (["goods", "promotion", "detail"] as $item) {
                $image_key = $item . "_images";
                if (isset($info[$image_key]) && count($info[$image_key]) > 0) {
                    if ($goods->id) {
                        //先删除老记录
                        GoodsImgs::where("goods_id", $goods->id)
                            ->where("type", $item)
                            ->delete();
                    }
                    foreach ($info[$image_key] as $index => $image) {
                        GoodsImgs::addImg([
                            "goods_id" => $goods->id,
                            "type" => $item,
                            "picture" => $image,
                        ]);

                        if ($item == "promotion" && $index == 0) {
                            $goods->picture = $image;
                            $goods->save();
                        }
                    }
                }
            }

            //规格
            if (isset($info["spec_list"]) && $info["spec_list"]) {
                Product::where("goods_id", $goods->id)
                    ->update(['status' => -1]);
                foreach ($info["spec_list"] as $index => $spec) {
                    if ($index == 0) {
                        //goods.price
                        $goods->price = $spec["price"];
                        $goods->save();
                    }
                    Product::generate($goods, $spec);
                }
            } else {
                // 处理规格为空的情况
                $spec_default = [
                    "store" => $info["common"]["store"] ?? null,
                    "price" => $info["common"]["price"] ?? null,
                    "group_price" => $info["common"]["group_price"] ?? null,
                    "custom_code" => $info["common"]["custom_code"] ?? null,
                    "picture" => "",
                    "spec" => ["默认" => "默认"],
                ];

                //goods.price
                $goods->price = $info["common"]["price"];
                $goods->save();
                Product::generate($goods, $spec_default);
            }

            //团购价格
            if ($info["group_price"]) {
                if (isset($info["spec_list"]) && $info["spec_list"]) {
                    $info["group_price"]["p2"] = $info["spec_list"][0]["group_price"];
                } else {
                    $info["group_price"]["p2"] = $info["common"]["group_price"];
                }

                foreach ($info["group_price"] as $key => $price_value) {
                    if ($price_value) {
                        $group_price = GoodsGroupInfo::firstOrNew([
                            "goods_id" => $goods->id,
                            "num" => str_replace("p", "", $key),
                        ]);

                        $group_price->price = $price_value;

                        $group_price->save();
                    }
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            $error = $e->getMessage();
            \Log::error("goods create error!!!!");
            \Log::error($error);
            DB::rollBack();
        }

        return isset($goods) && isset($goods->id) ? $goods : $error;
    }

    public function getImagesByType($type)
    {
        return GoodsImgs::where("goods_id", $this->id)->where(["type" => $type])->get();
    }

    //获取avg
    public static function getAvgPrice($keyword)
    {
        $avg = self::where('name', 'like', '%' . $keyword . '%')->avg('price');
        $half = ceil($avg / 2);
        $half2 = ceil($avg);
        $half3 = ceil($avg) + $half;
        $data = [
            "0-{$half}",
            "{$half}-{$half2}",
            "{$half2}-{$half3}",
            "{$half3}-more",
        ];

        return $data;
    }

    //获取单个商品信息
    public static function getOneGoods($goods_id)
    {
        $goods = self::where('id', $goods_id)->first();
        if (!empty($goods)) {
            $goods->getPicture()->getSpec()->carriage_template();
        }

        $goods = $goods ?? (new Goods())->getNullValue();

        return $goods;
    }

    public function spec()
    {
        if ($this->spec == ["默认" => "默认"]) {
            $this->spec = [];
        }

        return $this;
    }

    //根据浏览历史推荐商品-猜您想兜
    public static function recommendGoods($num)
    {
        //根据分类推荐
        $catIdLook = array_column(UserLookGoodsLog::lookHistory()->toarray(), 'cat_id');
        //获取我的购买
        $catIdOrder = array_column(User::myOrder()->toArray(), 'cat_id');

        $catId = array_unique(array_merge($catIdLook, $catIdOrder));
        $goods = self::whereIn('cat_id', $catId)->paginate($num);

        foreach ($goods as $row) {
            $row->getPicture();
            $row->spec();
        }

        return $goods;
    }

    //用户为空的情况-猜您想兜
    public static function recommendGoodsNoUser($num)
    {
        $goods = self::orderBy('created_at', 'DESC')
            ->where('status', '1')
            ->paginate($num);
        foreach ($goods as $row) {
            $row->getPicture();
            $row->spec();
            $row->groupBuying = GroupBuying::getBuying($row->id);
        }

        return $goods;
    }

    //根据商品分类推荐商品
    public static function recommendGoodsClass($goods_id, $num)
    {
        $goods = self::where('id', $goods_id)->first();
        $list = self::where('cat_id', $goods->cat_id)
            ->where('status', '1')
            ->paginate($num);
        foreach ($list as $row) {
            $row->getPicture();
            $row->spec();
            $row->groupBuying = GroupBuying::getBuying($row->id);
        }

        return $list;
    }

    //根据用户购买订单推荐
    public static function recommendGoodsBuy($num)
    {
        //用户未登陆推荐商品计算方式
        if (!auth()->user()) {
            return [];
        }
        $myOrder = Order::where('user_id', auth()->user()->id)
            ->get()
            ->toArray();
        $goods_id = self::twoToOne($myOrder, 'goods_id');

        $goods = self::whereIn('id', $goods_id)->get()->toArray();
        $cats_id = self::twoToOne($goods, 'cat_id');
//        dd($goods);
        $list = self::whereIn('cat_id', $cats_id)
            ->where('status', '1')
            ->paginate($num);
        foreach ($list as $row) {
            $row->getPicture();
            $row->spec();
            $row->groupBuying = GroupBuying::getBuying($row->id);
        }

        return $list;
    }

    /**
     *   获取订单模板信息.
     */
    public function carriage_template()
    {
        $template = CarriageTemplate::where('id', $this->carriage_template_id)->first();
        if (!empty($template)) {
            $template->free_area = json_decode($template->free_area, true);
            $template->cost_area = json_decode($template->cost_area, true);
        }
        $this->template = $template ?? (new CarriageTemplate())->getNullValue();

        return $this;
    }

    public static function getCountByMerchantIdAndStatus($merchant_id, $status = null)
    {
        $query = self::where("merchant_id", $merchant_id);
        if ($status != null) {
            $query->where("status", $status);
        }

        return $query->count();
    }

    public function approve($status)
    {
        if ($status != 1) {
            $status = -2;
        }
        $this->status = $status;
        $this->save();
    }

    public function delete()
    {
        //删除规格
        Product::where("goods_id", $this->id)->delete();
        //删除图片
        GoodsImgs::where("goods_id", $this->id)->delete();
        //删除 goods
        Goods::where("id", $this->id)->delete();
    }

    public function getGroupPriceInfo()
    {
        return GoodsGroupInfo::where("goods_id", $this->id)->get();
    }
}
