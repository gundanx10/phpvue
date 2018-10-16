<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\WechatAuthController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class WechatController extends WechatAuthController
{
    protected $except = [];

    public function init()
    {
        $wechatUserInfo = session('wechat.oauth_user'); // 拿到授权用户资料
        $baseInfo = $wechatUserInfo['default']->original;
        $user = new User();
        unset($baseInfo['privilege']);
        unset($baseInfo['language']);
        foreach ($baseInfo as $k => $v) {
            if ('headimgurl' === $k) {
                $k = 'head';
            }
            $user->$k = $v;
        }
        $newUser = $user->saveUser();
        $token = auth()->login($newUser);

        return view('index', ['token' => $token]);
    }

    public function knowledge()
    {
        return redirect('/#/knowledge');
    }

    public function summary()
    {
        return redirect('/#/summary');
    }

    public function router(Request $request)
    {
        $url = $request->input('url', '');

        return redirect('/#/' . $url);
    }
}
