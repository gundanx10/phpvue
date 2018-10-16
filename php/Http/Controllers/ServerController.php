<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use EasyWeChat\Kernel\Messages\Transfer;
use Log;
use App\Models\User;
use App\Models\CustomerMsg;

class ServerController extends Controller
{
    private $defaultMsg = "";

    public function serve()
    {
        $app = app('wechat.official_account');
        $app->server->push(function ($message) {
            switch ($message['MsgType']) {
              case 'event':
                  switch ($message['Event']) {
                    case 'subscribe':
                        //关注事件
                        $this->subscribe($message->FromUserName);

                        return $this->defaultMsg;
                        break;
                    case 'unsubscribe':
                        //取关事件
                        $this->unSubscribe($message->FromUserName);
                        break;
                    case 'SCAN':
                        //扫码事件
                        $this->scanevent($message, 'SCAN');

                        return $this->defaultMsg;
                        break;
                  }
                  break;
              case 'text':
                  //转客服处理
                  return new Transfer();
                  break;
              default:
                  return $this->defaultMsg;
            break;
            }
        });

        return $app->server->serve();
    }

    public function appServer()
    {
        $app = app('wechat.mini_program');
        $app->server->push(function ($message) {
            Log::info($message);
            $app = app('wechat.mini_program');
            $msg = new CustomerMsg();
            $msg->openid = $message['FromUserName'];
            $msg->msg = $message['Content'] ?? '';
            $msg->createTime = $message['CreateTime'];
            $msg->msgType = $message['MsgType'];
            $msg->event = $message['Event'] ?? '';
            $msg->sessionFrom = $message['SessionFrom'] ?? '';
            $msg->save();
            if ($message['MsgType'] == 'text') {
                $app->customer_service
                  ->message('已收到您的留言,我们尽快会回复您的问题')
                  ->to($message['FromUserName'])
                  ->send();
            }
        });

        return $app->server->serve();
    }

    // 扫描关注
    private function scanevent($event, $type = 'subscribe')
    {
        //扫码二维码带事件key，可以定位到父级
        if (isset($event->EventKey) && $event->EventKey) {
            Log::info($event);
            if ('subscribe' == $type) {
                $res = explode('_', $event->EventKey);
                if (is_numeric($res[1])) {
                    $referral_id = $res[1];
                }
                $this->subscribe($event->FromUserName, $referral_id);
            } elseif ('SCAN' == $type) {
                //已关注,单扫描事件
            }
        } else {
            //默认扫码关注事件
            $this->subscribe($event->FromUserName);
        }

        return true;
    }

    // 关注获取用户信息
    private function subscribe($openid, $referral_id = 0)
    {
        $wechat = app('wechat.official_account');
        $baseInfo = $wechat->user->get($openid);
        $user = new User();
        $user->openid = $userInfo->openid;
        $user->nickname = $userInfo->nickname;
        $user->head = $userInfo->headimgurl;
        $user->sex = $userInfo->sex;
        $user->city = $userInfo->city;
        $user->country = $userInfo->country;
        $user->province = $userInfo->province;
        $user->subscribe_time = $userInfo->subscribe_time;
        $user->unionid = $userInfo->unionid ?? '';
        $user->referral_id = $referral_id;
        $user->subscribe = 1;
        //user model 处理保存事件
        $user->saveUser();
    }

    // 取消关注
    private function unSubscribe($openid)
    {
        $user = User::where('openid', '=', $openid)->first();
        if ($user) {
            $user->subscribe = 0;
            $user->save();
        }
    }

    // 创建菜单
    public function CreateMenu()
    {
        $wechat = app('wechat.official_account');
        $buttons = [
            [
                "type" => "view",
                "name" => "",
                "url" => Config('app.url') . '/',
            ],
            [
                "type" => "view",
                "name" => "",
                "url" => Config('app.url') . '/knowledge',
            ],
            [
                "type" => "view",
                "name" => "",
                "url" => Config('app.url') . '/summary',
            ],
        ];

        return $wechat->menu->add($buttons);
    }

    // 删除菜单
    public function DeleteMenu()
    {
        $wechat = app('wechat.official_account');

        return $wechat->menu->destroy();
    }
}
