<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\Api\ApiHelper;
use App\Models\Chat;
use App\Models\User;
use App\Models\UserLog;
use App\Models\Favorite;
use App\Models\GroupChatRead;
use Log;
use App\Events\Notice;

class ChatMsgController extends AuthController
{
    protected $except = [''];
    protected $user = [];

    public function __construct()
    {
        parent::__construct();
        $this->user = auth()->user();
    }

    public function chatList(Request $request)
    {
        $num = $request->input('num', 20);
        $page = $request->input('page', 1);

        $chatList = Favorite::where('user_id', $this->user->id)
        ->where('type', 'merchant')
        ->paginate($num);

        foreach ($chatList as $chat) {
            $chat->getMerchant();
            $chat->getUnread();
        }

        $chatList = $chatList->toArray();
        $groupJoin = UserLog::where('msg_type', 'chatGroup')
                    ->where('user_id', $this->user->id)
                    ->where('is_read', 0)
                    ->count();
        if ($page < 2) {
            $mygroup = $this->user->chatInfo()->getUnread();
            $userDB = new User();
            $joingroup = $userDB->getNullValue();
            if ($this->user->referral_id != 0) {
                $joingroup = User::where('id', $this->user->referral_id)
                ->first()
                ->chatInfo()
                ->getUnread();
            }
            $chatList['mygroup'] = $mygroup;
            $chatList['joingroup'] = $joingroup;
            $chatList['notice'] = $groupJoin;
        }

        return ApiHelper::return($chatList);
    }

    public function shareItem(Request $request)
    {
        $vData = $request->validate([
          'type' => 'required',
          'share_type' => 'required',
          'item_id' => 'required',
        ]);
        if ($vData['share_type'] == 'family') {
            $to_user_id = $this->user->id;
        } else {
            $to_user_id = $this->user->referral_id;
        }
        if ($to_user_id == 0) {
            return ApiHelper::ErrorMsg('you need join a hometown');
        }
        $data = [
          'chat_type' => 1,
          'type' => $vData['type'],
          'user_id' => $this->user->id,
          'to_user_id' => $to_user_id,
          'item_id' => $vData['item_id'],
        ];
        $chat = new Chat();
        $rs = $chat->saveData($data)->getUser();

        return ApiHelper::return($rs);
    }

    public function chatAddMsg(Request $request)
    {
        $vData = $request->validate([
          'type' => 'required',
          'id' => 'required',
        ]);
        $chat_type = $request->input('chat_type', 0);
        $item_id = $request->input('item_id', 0);
        $msg = $request->input('msg', '');
        $data = [
          'chat_type' => $chat_type,
          'type' => $vData['type'],
          'user_id' => $this->user->id,
          'to_user_id' => $vData['id'],
          'msg' => $msg,
          'item_id' => $item_id,
        ];
        $chat = new Chat();
        $rs = $chat->saveData($data)->getUser()->toArray();
        // 关闭socket
        // broadcast(new Notice($rs, 'room_' . $chat_type . '_' . $vData['id']));

        return ApiHelper::return($rs);
    }

    public function chatMsgList(Request $request)
    {
        $vData = $request->validate([
          'chat_type' => 'required',
          'id' => 'required',
        ]);
        $num = $request->input('num', 20);
        $last_msg_id = $request->input('last_msg_id');
        $prev_msg_id = $request->input('prev_msg_id');
        $uid = $this->user->id;
        $toUid = $vData['id'];
        if ($vData['chat_type'] == 0) {
            $chatMsgList = Chat::where('chat_type', $vData['chat_type'])
                            ->where(function ($query) use ($toUid,$uid) {
                                $query->where('user_id', $toUid)
                                      ->where('to_user_id', $uid);
                            })
                            ->Orwhere(function ($query) use ($toUid,$uid) {
                                $query->where('user_id', $uid)
                                      ->where('to_user_id', $toUid);
                            })
                            ->orderBy('created_at', 'desc');
        }
        if ($vData['chat_type'] > 0) {
            $chatMsgList = Chat::where('chat_type', $vData['chat_type'])
                            ->where('to_user_id', $vData['id'])
                            ->orderBy('created_at', 'desc');
        }
        if ($last_msg_id) {
            $chatMsgList->where('id', '>', $last_msg_id);
        }
        if ($prev_msg_id) {
            $chatMsgList->where('id', '<', $prev_msg_id);
        }
        $chatMsgList = $chatMsgList->paginate($num);
        $readMax_id = GroupChatRead::getMaxId($vData['id'], $vData['chat_type']);
        $max_id = 0;
        $min_id = 1000000000;
        foreach ($chatMsgList as $chat) {
            $max_id = $chat->id > $max_id ? $chat->id : $max_id;
            $min_id = $chat->id > $min_id ? $min_id : $chat->id;
            $chat->getUser();
            $chat->getItem();
        }
        if ($max_id > $readMax_id) {
            GroupChatRead::setMaxId($vData['id'], $vData['chat_type'], $max_id);
        }
        $chatMsgList = $chatMsgList->toArray();
        $chatMsgList['max_msg_id'] = $max_id;
        $chatMsgList['min_msg_id'] = $min_id;

        return ApiHelper::return($chatMsgList);
    }
}
