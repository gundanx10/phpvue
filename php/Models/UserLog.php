<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserLog extends Model
{
    use \App\Traits\ModelHelper;
    protected $table = 'user_log';
    protected $hidden = [];
    protected $type = [
      'logistics' => '物流消息',
      'chatGroup' => '聊天进组消息',
      'quitGroup' => '退组消息',
      'joinResult' => '返回加入信息',
      'groupBuying' => '拼团信息',
      'event' => '活动消息',
      'panicBuying' => '混团消息',
      'system' => '系统消息',
      'groupBalance' => '家乡分红',
      'myGroupBalance' => '家族分红',
      'shaikhBalance' => '家族购物',
      'balance' => '分红',
      'commission' => '佣金',
      'applyFamily' => '申请家族',
    ];

    public static function addUserLog($type, $user_id, $item_id = 0, $msg = '', $result = '', $description='')
    {
        $Log = new self();
        $Log->msg_type = $type;
        $Log->msg = $msg;
        $Log->user_id = $user_id;
        $Log->item_id = $item_id;
        $Log->description = $description;
        $Log->result = $result;
        $Log->save();
    }

    /**
     * @param $user_id 加入人 申请 主动
     * @param $pid 父级
     * @param string $status
     * @param accord_apply_success 主动申请成功 accord_apply_fail 主动申请失败 accept_apply_success 接受申请成功 accept_apply_fail 接受申请失败
     */
    public static function joinMyFamily($user_id, $pid, $status = 'success')
    {
        $my = User::where('id', $pid)->first();
        $join_user = User::where('id', $user_id)->first();

        switch ($status) {
            case 'success':
                $mymsg = $join_user->nickname . '已成功加入您的家族';
                self::addUserLog('applyFamily', $pid, '0', $mymsg, '', $join_user->id);
                $joinmsg = '您已成功加入' . $my->nickname . '的家族';
                self::addUserLog('applyFamily', $user_id, '0', $joinmsg,'',$my->id);
                break;

            case 'fail':
                $msg = $join_user->nickname . '已有家族，无法再加入您的家族';
                self::addUserLog('applyFamily', $pid, '0', $msg,'',$join_user->id);
                break;
        }

        return new self();
    }

    public function getDetailInfo()
    {
        $functionName = $this->msg_type . 'GetDetail';

        return $this->$functionName();
    }


    public function panicBuyingGetDetail()
    {
        $user = User::where('id',$this->user_id)->first();
        $group = GroupBuying::where('id',$this->item_id)->first();
        $mainUser = User::where('id', $group->sponsor_uid)->first();
        $goods = Goods::where('id',$group->goods_id)->first();
        if(!empty($goods)){
            $goods->getPicture();
        }
        $api_description = [
            'type' => $this->type[$this->msg_type],
            'result' => '混团获取佣金',
            'user' => empty($user) ? '' : $user->head,
            'brokerage' => $this->result,
            'describe'  => '拼主'.$mainUser->nickname.'支付给您'.$group->owner_paid.'元',
            'image' => empty($goods->small_pic) ? '' : $goods->small_pic,
        ];
        $this->api_description = $api_description;

        return $this;
    }


    public function groupBalanceGetDetail()
    {
        $api_description = [
          'type' => $this->type[$this->msg_type],
            'result' => '',
            'user' => '',
            'brokerage' => '',
            'describe' => '',
            'image' => '',
            'is_read' => $this->is_read,
        ];
        $this->api_description = $api_description;

        return $this;
    }

    public function applyFamilyGetDetail()
    {
        $user = User::where('id',$this->description)
            ->first();
        $api_description = [
          'type' => $this->type[$this->msg_type],
            'result' => '家族申请通知',
            'user' => '0',
            'brokerage' => '0',
            'describe' => $this->msg,
            'image' => $user->hrad,
            'is_read' => $this->is_read,
        ];
        $this->api_description = $api_description;

        return $this;
    }

    public function myGroupBalanceGetDetail()
    {
        $api_description = [
            'type' => $this->type[$this->msg_type],
            'result' => '',
            'user' => '',
            'brokerage' => '',
            'describe' => '',
            'image' => '',
            'is_read' => $this->is_read,
        ];
        $this->api_description = $api_description;

        return $this;
    }

    public function joinResultGetDetail()
    {
        $api_description = [
          'type' => $this->type[$this->msg_type],
            'result' => '',
            'user' => '',
            'brokerage' => '',
            'describe' => '',
            'image' => '',
            'is_read' => $this->is_read,
        ];
        $this->api_description = $api_description;

        return $this;
    }

    public function groupBuyingGetDetail()
    {
        $api_description = [
          'type' => $this->type[$this->msg_type],
            'result' => '',
            'user' => '',
            'brokerage' => '',
            'describe' => '',
            'image' => '',
            'is_read' => $this->is_read,
        ];
        $this->api_description = $api_description;

        return $this;
    }

    public function shaikhBalanceGetDetail()
    {
        $order = Order::where('order_id',$this->description)
            ->first();
        $user = User::where('id',$order->user_id)
            ->first();
        $goods = Goods::where('id',$order->goods_id)
            ->first();
        $goods->getPicture();

        $describe = empty($user) ? '' : $user->nickname;
        $api_description = [
            'type' => $this->type[$this->msg_type],
            'result' => '家族购物成功！获得佣金',
            'user' => empty($user) ? '' : $user->head,
            'brokerage' => $this->result,
            'describe' => '族员“'.$describe.'”已成功购物',
            'image' => $goods->small_pic,
        ];
        $this->api_description = $api_description;
        return $this;
    }

    //拼团成功
    public function groupBuying()
    {
        $api_description = [
          'type' => $this->type[$this->msg_type],
            'result' => '拼团对象',
            'user' => '',
            'brokerage' => '',
            'describe' => '',
            'image' => '',
            'is_read' => $this->is_read,
        ];
        $group_buying = GroupBuying::getById($this->item_id)->getGoods();
        $this->group_buying = $group_buying;

        return $this;
    }

    //处理混团日志
    public function panicBuying()
    {
        $api_description = [
            'type' => $this->type[$this->msg_type],
            'user' => '拼团团长用户对象',
            'result' => '拼团对象',
            'brokerage' => '',
            'describe' => '',
            'image' => '',
            'is_read' => $this->is_read,
        ];
        $group_buying = GroupBuying::getById($this->item_id)->getGoods();
        $user = User::getById($group_buying->sponsor_uid);

        $this->group_buying = $group_buying;
        $this->user = $user;

        return $this;
    }

    //团长分红日志
    public function shaikhBalance()
    {
        $api_description = [
            'type' => $this->type[$this->msg_type],
            'user' => '抽佣用户对象',
            'result' => '商品对象',
            'brokerage' => '',
            'describe' => '',
            'image' => '',
            'is_read' => $this->is_read,
        ];

        $user = User::getById($this->msg);
        $this->goods = Goods::getById($this->item_id)->getPicture();
        $this->user = $user;

        return $this;
    }

    public function chatGroupGetDetail()
    {
        $api_description = [
            'type' => $this->type[$this->msg_type],
            'user' => '用户对象',
            'result' => $this->result,
            'status' => 'join_confirm由你来确认释放是否加入你的群,join你所申请加入群的信息',
            'image' => '',
            'brokerage' => '',
            'is_read' => $this->is_read,
        ];
        if ($this->user_id == auth()->user()->id) {
            $user = User::getById($this->item_id)->baseInfo();
            $status = 'join_confirm';
        } else {
            $user = User::getById($this->user_id)->baseInfo();
            $status = 'join';
        }
        $this->api_description = $api_description;
        $this->user = $user;
        $this->status = $status;

        return $this;
    }

    public function getDefaultDetailInfo()
    {
        $api_description = [
            'type' => 'chatGroup',
            'user' => '用户对象',
            'result' => '',
            'status' => 'join_confirm由你来确认释放是否加入你的群,join你所申请加入群的信息,none Android默认空字符',
            'image' => '',
            'brokerage' => '',
            'is_read' => '',
        ];

        $this->api_description = $api_description;
        $user = new User();
        $this->user = $user->getNullValue();
        $this->status = 'none';
        $this->type = 'chatGroup';
        $this->is_read = 0;
        $this->result = -1;

        return $this;
    }

    public static function systemLog($user_id, $item_id, $msg)
    {
        // self::addUserLog('joinResult', $item_id, $user_id, $msg);
        // return new self;
    }

    public static function commission($user_id, $item_id, $buy_user_id, $balance)
    {
        self::addUserLog('shaikhBalance', $user_id, $item_id, $buy_user_id, $balance);
        // return new self;
    }

    public static function joinGroup($group_id, $user_id)
    {
        $old_join = self::where('msg_type', 'chatGroup')
                        ->where('user_id', $group_id)
                        ->where('item_id', $user_id)
                        ->first();
        if ($old_join) {
            $old_join->is_read = 0;
            $old_join->result = '';
            $old_join->created_at = Carbon::now();
            $old_join->save();
        } else {
            self::addUserLog('chatGroup', $group_id, $user_id);
        }

        // return new self;
    }

    public static function addPanicBuyingLog($user_id, $item_id, $balance)
    {
        self::addUserLog('panicBuying', $user_id, $item_id, '', $balance);
        // return new self;
    }

    public static function addgroupBuyingFinishLog($user_id, $item_id)
    {
        self::addUserLog('groupBuying', $user_id, $item_id, '', 'success');
        // return new self;
    }

    public static function joinGroupConfirm($vData)
    {
        $log = self::where('id', $vData['log_id'])->first();
        if ($vData['status'] == 1) {
            $guser = User::where('id', $log['user_id'])->first();
            $user = User::where('id', $log['item_id'])->first();
            $user->referral_id = $log->user_id;
            $user->save();
            $guser->chat_num = $guser->chat_num + 1;
            $guser->save();
            $log->is_read = 1;
            $log->save();
            self::where('msg_type', 'chatGroup')
                ->where('is_read', 0)
                ->where('item_id', $log['item_id'])
                ->delete();
            self::systemLog($log['item_id'], $log->user_id, '欢迎加入家族');
            $log->result = 'pass';
        } else {
            self::systemLog($log['item_id'], $log->user_id, '拒绝了您的加入');
            $log->result = 'deny';
        }
        $log->is_read = 1;
        $log->save();

        return $log;
    }
}
