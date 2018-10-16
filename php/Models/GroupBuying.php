<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Cache;

class GroupBuying extends Model
{
    use \App\Traits\ModelHelper;
    protected $hidden = [];
    protected $guarded = [];
    protected $fillable = [];
    protected $table = 'group_buying';

    public static function getBuying($id, $field = 'goods_id', $num = 9)
    {
        $count = self::where($field, $id)
                    ->where('mixed_status', '<', 3)
                    ->where('mixed_status', '>', 0)
                    ->where('end_time', '>', Carbon::now())
                    ->count();
        $buyings = self::where($field, $id)
                        ->where('mixed_status', '<', 3)
                        ->where('mixed_status', '>', 0)
                        ->where('end_time', '>', Carbon::now())
                        ->take($num)
                        ->OrderBy('id', 'DESC')
                        ->get();
        foreach ($buyings as &$buy) {
            $buy->need_num = $buy->need_person_num - $buy->payed_num;
            $buy->end_time = strtotime($buy->end_time);
            $buy->end_microtime = $buy->end_time * 1000;
            if ($buy->end_time - time() > 0) {
                $buy->time_difference = $buy->end_time - time();
            } else {
                $buy->time_difference = 0;
            }
            $buy->getUser();
            $buy->getJoinUser();
            $buy->getGoods();
        }

        return [
          'count' => $count,
          'data' => $buyings,
        ];
    }

    public function getUser()
    {
        $user = User::where('id', $this->sponsor_uid)->first();
        $this->nickname = $user->nickname;
        if ($user->head) {
            $this->head = $user->head;
        } else {
            $this->head = config('app.url') . '/header.png';
        }

        return $this;
    }

    public function getJoinUser()
    {
        $orders = Order::where('group_buying_id', $this->id)
                      ->where('type', 'join_group_buying')
                      ->where('pay_status', '1')
                      ->get();
        $users = [];
        foreach ($orders as $order) {
            array_push($users, $order->user_id);
        }
        $this->joinUid = $users;

        return $this;
    }

    public function fomartTime()
    {
        $this->end_time = strtotime($this->end_time);
        $this->end_microtime = $this->end_time * 1000;

        return $this;
    }

    public function getGoods()
    {
        // $key = 'goods_' . $this->goods_id;
        // if (Cache::has($key)) {
        //     $goods = Cache::get($key);
        // } else {
        $goods = Goods::where('id', $this->goods_id)->first();
        if ($goods) {
            $goods->getSpec();
            $goods->getPicture();
            // Cache::put($key, $goods, 1440);
        }
        // }
        $this->goods = $goods;

        return $this;
    }

    public function getOrderDeduction()
    {
        $key = 'order_deduction_' . $this->id;
        $order_deduction = 0;
        if (Cache::has($key)) {
            $order_deduction = Cache::get($key);
        } else {
            $order = Order::where('type', 'group_buying')
                          ->where('group_buying_id', $this->id)
                          ->first();
            if ($order) {
                $order_deduction = $order->group_leader_deduction;
                Cache::put($key, $order_deduction, 1440);
            }
        }
        $this->order_deduction = $order_deduction;

        return $this;
    }

    public function getService()
    {
        $key = 'service_1';
        if (Cache::has($key)) {
            $service = Cache::get($key);
        } else {
            $service = MerchantService::where('merchant_id', 1)->get();
            Cache::put($key, $service, 1440);
        }
        $this->service = $service;

        return $this;
    }

    public function getMerchant()
    {
        $key = 'merchant_' . $this->merchant_id;
        if (Cache::has($key)) {
            $merchant = Cache::get($key);
        } else {
            $merchant = Merchant::where('id', $this->merchant_id)->first()->getImage();
            Cache::put($key, $merchant, 1440);
        }
        $this->merchant = $merchant;

        return $this;
    }

    public function getProduct()
    {
        $key = 'product_' . $this->product_id;
        if (Cache::has($key)) {
            $product = Cache::get($key);
        } else {
            $product = Product::where('id', $this->product_id)->first();
            $product->getPicture();
            $product->getSpec();
            Cache::put($key, $product, 1440);
        }

        $this->product = $product;

        return $this;
    }

    public function getAllUser()
    {
        $orders = Order::where('group_buying_id', $this->id)
                        ->where('pay_status', '>', '0')
                        ->where('type', '!=', 'panic_buying')
                        ->orderBy('created_at', 'asc')
                        ->get();
        $userArray = [];
        foreach ($orders as $order) {
            array_push($userArray, $order->user_id);
        }
        $users = User::whereIn('id', $userArray)->get();

        $usersSort = [];
        $bought = false;
        foreach ($userArray as $userId) {
            if (auth()->user() && $userId == auth()->user()->id) {
                $bought = true;
            }
            foreach ($users as $user) {
                if ($user['id'] == $userId) {
                    if (!$user->head) {
                        $user->head = config('app.url') . '/header.png';
                    }
                    if (!$user->nickname) {
                        $user->nickname = $user->phone;
                    }
                    array_push($usersSort, $user);
                }
            }
        }
        $this->users = $usersSort;
        $this->user_buy_status = $bought;

        return $this;
    }
}
