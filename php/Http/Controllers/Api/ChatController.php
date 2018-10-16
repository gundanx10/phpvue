<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\Api\ApiHelper;
use App\Models\User;
use App\Models\UserLog;
use App\Models\UploadFile;
use App\Models\Search;
use Log;

class ChatController extends AuthController
{
    protected $except = ['syntheticImg'];
    protected $user = [];

    public function __construct()
    {
        parent::__construct();
        $this->user = auth()->user();
    }

    public function userLists(Request $request)
    {
        $params = $request->all();
        $num = $request->input('num', 10);
        $user = new User();
        if (!empty($params['nickname'])) {
            Search::addKeyword($params['nickname'], 'family');
            $user = $user->where('nickname', 'like', '%' . $params['nickname'] . '%')
                         ->orWhere('chat_name', 'like', '%' . $params['nickname'] . '%');
        }

        $usersList = $user->where('nickname', '!=', '')
                        ->orderBy('chat_num', 'desc')
                        ->paginate($num);

        foreach ($usersList as $user) {
            $user->chatInfo();
        }

        return ApiHelper::return($usersList);
    }

    public function joinCheck(Request $request)
    {
        $vData = $request->validate([
          'gid' => 'required',
        ]);
        if ($this->user->referral_id == $vData['gid']) {
            return ApiHelper::ErrorMsg('It is already in the group');
        }
        if ($this->user->id == $vData['gid']) {
            return ApiHelper::ErrorMsg('Can not join your own group');
        }
        if ($this->user->referral_id > 0) {
            return $this->groupInfo($this->user->referral_id);
        } else {
            UserLog::joinGroup($vData['gid'], $this->user->id);

            return ApiHelper::Successfully();
        }
    }

    public function AndroidJoinCheck(Request $request)
    {
        $vData = $request->validate([
          'gid' => 'required',
        ]);
        if ($this->user->referral_id == $vData['gid']) {
            return ApiHelper::ErrorMsg('It is already in the group');
        }
        if ($this->user->id == $vData['gid']) {
            return ApiHelper::ErrorMsg('Can not join your own group');
        }
        if ($this->user->referral_id > 0) {
            return ApiHelper::ErrorMsg('confirm exit from group');
        } else {
            UserLog::joinGroup($vData['gid'], $this->user->id);

            return ApiHelper::Successfully();
        }
    }

    public function AndroidGroup(Request $request)
    {
        if ($this->user->referral_id) {
            return $this->groupInfo($this->user->referral_id);
        }
    }

    public function forcedJoin(Request $request)
    {
        $vData = $request->validate([
          'gid' => 'required',
        ]);
        if ($this->user->id == $vData['gid']) {
            return ApiHelper::ErrorMsg('Can not join your own group');
        }

        $this->outOfGroupFunction($vData);

        UserLog::joinGroup($vData['gid'], $this->user->id);

        return ApiHelper::Successfully();
    }

    public function joinGroupConfirm(Request $request)
    {
        $vData = $request->validate([
          'log_id' => 'required',
          'status' => 'required',
        ]);
        UserLog::joinGroupConfirm($vData);

        return ApiHelper::Successfully();
    }

    public function outOfGroup(Request $request)
    {
        $vData = $request->validate([
          'gid' => 'required',
        ]);

        return $this->outOfGroupFunction($vData);
    }

    public function outOfGroupFunction($vData)
    {
        $guser = User::getById($vData['gid']);
        $guser->chat_num = $guser->chat_num - 1;
        $guser->save();
        $this->user->referral_id = 0;
        $this->user->save();
        UserLog::where('msg_type', 'chatGroup')
                ->where('item_id', $this->user->id)
                ->delete();

        return ApiHelper::Successfully();
    }

    public function myGroup()
    {
        return $this->groupInfo($this->user->id);
    }

    public function upChatGroup(Request $request)
    {
        $params = $request->all();
        $this->user->updateSave($params);

        return $this->groupInfo($this->user->id);
    }

    public function syntheticImg(Request $request)
    {
        $vData = $request->validate([
          'id' => 'required',
        ]);
        $users = User::where('referral_id', $vData['id'])->limit(9)->get();
        $imgs = [];
        foreach ($users as $user) {
            array_push($imgs, $user->head);
        }
        $headerUrl = UploadFile::synthetic($imgs);
        $gUser = User::getById($vData['id']);
        $gUser->chat_head = $headerUrl;
        $gUser->save();

        return ApiHelper::Successfully();
    }

    public function groupById(Request $request)
    {
        $vData = $request->validate([
          'gid' => 'required',
        ]);

        return $this->groupInfo($vData['gid']);
    }

    public function groupInfo($uid)
    {
        $user = User::getById($uid);
        $user->chatInfo();
        $user->chatUserList();
        $userLog = UserLog::where('msg_type', 'chatGroup')
                            ->where('item_id', $this->user->id)
                            ->where('user_id', $uid)
                            ->first();

        if ($userLog) {
            $userLog->getDetailInfo();
        } else {
            $userLog = [];
        }
        $user->userLog = $userLog;

        return ApiHelper::return($user);
    }
}
