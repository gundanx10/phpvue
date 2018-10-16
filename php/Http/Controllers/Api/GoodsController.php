<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AuthController;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\Api\ApiHelper;
use App\Models\Goods;
use App\Models\GoodsImgs;
use App\Models\Merchant;
use App\Models\MerchantService;
use App\Models\Brand;
use App\Models\Comments;
use App\Models\Specification;
use App\Models\GroupBuying;
use App\Models\GoodsGroupInfo;
use App\Models\Product;
use App\Models\Search;
use Log;
use App\Models\UserLookGoodsLog;
use Illuminate\Support\Facades\DB;
use App\Traits\ModelHelper;

class GoodsController extends AuthController
{
    protected $except = ['choiceness', 'faddishList', 'specification',
     'searchGoods', 'getSections', 'getRecommendGoods', 'goodsDetail',
     'getGroupBuying', 'panicBuyingList','hotGoods',
   ];
    protected $user = [];

    public function __construct()
    {
        parent::__construct();
        $this->user = auth()->user();
    }

    //精品推荐接口
    public function choiceness(Request $request)
    {
        $num = $request->input('num', 10);
        $goodsIdArray = Goods::groupByMerchant();
        $goodsList = Goods::whereIn('id', $goodsIdArray)
                          ->orderBy('group_num', 'desc')
                          ->orderBy('order_by', 'desc')
                          ->orderBy('created_at', 'desc')
                          ->paginate($num);

        foreach ($goodsList as &$goods) {
            $goods->brand = $goods->getBrand();
            $goods->getSpec();
            $goods->group_price = 0;
            $goods->getRectanglePicture();
            $goods->getGroupBuying();
        }

        return ApiHelper::return($goodsList->toArray());
    }

    //爆款接口
    public function faddishList(Request $request)
    {
        $num = $request->input('num', 10);
        $goodsList = Goods::where('status', 1)
                        ->where('group_num', '>', 1)
                        ->orderBy('group_num', 'desc')
                        ->paginate($num);
        foreach ($goodsList as &$goods) {
            $goods = $goods->getPicture()->getSpec();
        }

        return ApiHelper::return($goodsList->toArray());
    }

    //混团接口
    public function panicBuyingList(Request $request)
    {
        $num = $request->input('num', 10);
        $groups = GroupBuying::where('panic_buying_status', 1)
                    ->orderBy('end_time', 'desc')
                    ->paginate($num);
        foreach ($groups as $row) {
            $row->getUser();
            $row->getGoods();
        }

        return ApiHelper::return($groups);
    }

    //拼单详情接口
    public function getGroupBuying(Request $request)
    {
        $vData = $request->validate([
          'id' => 'required',
        ]);

        $groupBuying = GroupBuying::where('id', $vData['id'])
                                  ->first()
                                  ->fomartTime()
                                  ->getAllUser()
                                  ->getProduct()
                                  ->getService()
                                  ->getMerchant()
                                  ->getGoods()
                                  ->getOrderDeduction();

        return ApiHelper::return($groupBuying);
    }

    //商品详情接口
    public function goodsDetail(Request $request)
    {
        $vData = $request->validate([
          'id' => 'required',
        ]);

        $goods = Goods::where('status', 1)->where('id', $vData['id'])->first()
                      ->getPicture()
                      ->getFavoriteStatus()
                      ->getSpec()
                      ->getDetails()
                      ->toArray();

        $goodsGallery = GoodsImgs::getImg($vData['id'])->toArray();
        $merchant = Merchant::where('id', $goods['merchant_id'])
                            ->first()
                            ->getImage()
                            ->getBuyCount()
                            ->toArray();
        $merchantServer = MerchantService::where('merchant_id', 1)
                                          ->get()
                                          ->toArray();

        $comments = Comments::getComment($vData['id'], 'goods');

        $groupBuying = GroupBuying::getBuying($vData['id']);

        $groupInfo = GoodsGroupInfo::getInfo($vData['id']);
        $groupPrice = 1000000;
        foreach ($groupInfo as $group) {
            $groupPrice = $groupPrice > $group->price ? $group->price : $groupPrice;
        }
        $products = Product::getProduct($vData['id']);
        foreach ($products as &$product) {
            $product = $product->getPicture();
            $product = $product->getSpec();
        }

        //添加我浏览日志
        if (!empty(auth()->user())) {
            UserLookGoodsLog::addLog($vData['id']);
        }

        //店铺收藏状态和店铺粉丝数量
        $favorite = [
            'isFavorite' => $this->favoriteMerchant($goods['merchant_id']),
            'fans' => Favorite::favoriteNum($goods['merchant_id']),
        ];

        $goods = array_merge($goods, ['products' => $products->toArray()]);
        $goods = array_merge($goods, ['groupInfo' => $groupInfo->toArray()]);
        $goods = array_merge($goods, ['groupBuying' => $groupBuying]);
        $goods = array_merge($goods, ['gallery' => $goodsGallery]);
        $goods = array_merge($goods, ['comments' => $comments]);
        $goods = array_merge($goods, ['merchant' => $merchant]);
        $goods = array_merge($goods, ['merchant_server' => $merchantServer]);
        $goods = array_merge($goods, ['favorite' => $favorite]);
        $goods = array_merge($goods, ['groupPrice' => $groupPrice]);

        return ApiHelper::return($goods);
    }

    /**
     * 根据商品id 获取用户是否关注该商品的店铺.
     *
     * @param $goods_id
     */
    public function favoriteMerchant($merchant_id)
    {
        $favoritestatus = false;
        if (empty(auth()->user()->id)) {
            return $favoritestatus;
        }

        $favorite = Favorite::where('merchant_id', $merchant_id)
                        ->where('user_id', auth()->user()->id)
                        ->where('type', 'merchant')
                        ->count();
        if ($favorite > 0) {
            $favoritestatus = true;
        }

        return $favoritestatus;
    }

    //商品规格接口
    public function specification(Request $request)
    {
        $vData = $request->validate([
            'goods_id' => 'required',
        ]);
        $list = Specification::getSpecification($vData['goods_id']);

        return ApiHelper::return($list);
    }

    //全部商品搜索
    public function searchGoods(Request $request)
    {
        $vData = $request->validate([
            'default_sort' => 'string',
            'price_sort' => 'string',
            'group_num_sort' => 'string',
        ]);
        $keyword = $request->input('keyword', null);
        $priceSection = $request->input('price_section', 0);
        $num = $request->input('num', 10);
        //添加搜索记录
        if (!empty($keyword)) {
            Search::addKeyword($keyword);
        }
        $rows = Goods::where('name', 'like', '%' . $keyword . '%')->where('status', 1);
        if (!empty($priceSection)) {
            $priceSectionArray = explode('-', $priceSection);
            $rows->whereBetween('price', $priceSectionArray);
        }
        if (!empty($vData['default_sort'])) {
            $rows->orderBy('order_by', $vData['default_sort']);
        }
        if (!empty($vData['price_sort'])) {
            $rows->orderBy('price', $vData['price_sort']);
        }
        if (!empty($vData['group_num_sort'])) {
            $rows->orderBy('group_num', $vData['group_num_sort']);
        }

        $listRow = $rows->paginate($num);
        foreach ($listRow as $row) {
            $row->getPicture();
        }

        return ApiHelper::return($listRow);
    }

    //获取区间
    public function getSections(Request $request)
    {
        $keyword = $request->input('keyword', null);
        $lists = Goods::getAvgPrice($keyword);

        return ApiHelper::return(['sections' => $lists]);
    }

    //获取推荐商品
    public function getRecommendGoods(Request $request)
    {
        $num = $request->input('num', 10);

        //如果用户没有登录
//        if (empty($this->user)) {
//            $lists = Goods::recommendGoodsNoUser($num);
//        } else {
//            $lists = Goods::recommendGoods($num);
//        }

        $goods_id = $request->input('goods_id', '0');
        if (!empty($goods_id)) {
            $lists = Goods::recommendGoodsClass($goods_id, $num);
        } else {
            $lists = Goods::recommendGoodsBuy($num);
        }

        return ApiHelper::return($lists);
    }

    //热门商品
    public function hotGoods(Request $request){
        $num = $request->input('num', 10);
        $goodsList = Goods::where('status',1)
            ->orderBy('group_num','DESC')
            ->orderBy('recommend_num','DESC')
            ->paginate($num);

        foreach ($goodsList as &$goods) {
            $goods->brand = $goods->getBrand();
            $goods->getSpec();
            $goods->group_price = 0;
            $goods->getRectanglePicture();
            $goods->getGroupBuying();
        }

        return ApiHelper::return($goodsList->toArray());
    }
}
















