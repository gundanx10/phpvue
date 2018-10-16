<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class WechatAuthController extends Controller
{
    protected $except = [];

    /**
     * Create a new AuthController instance.
     */
    public function __construct()
    {
        $except = $this->except;
        $this->middleware('wechat.oauth:snsapi_userinfo', ['except' => $except]);
    }
}
