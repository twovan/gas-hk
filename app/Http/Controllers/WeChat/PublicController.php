<?php

namespace App\Http\Controllers\WeChat;
use App\Models\VipCard;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use EasyWeChat\Factory;

class PublicController extends Controller
{
    public function serve()
    {
        $app = app('wechat.official_account');
        $app->server->push(function($message){
            $msg = null;
            switch ($message['MsgType']) {
                case 'event':
                    $msg = $this->responseEvent($message);
                    break;
            }
            if ($msg){
                return $msg;
            }
        });
        return $app->server->serve();
    }

    //相关事件
    protected function responseEvent($message){
        //关注
        if (strtolower($message['Event']) == 'subscribe'){
            return '欢迎关注汉口加油站';
        }

        //用户填写资料后，会员卡激活
        if (strtolower($message['Event']) == 'submit_membercard_user_info'){
            return $this->membercardActive($message);
        }
        return false;
    }

    //会员卡激活
    protected function membercardActive($message){
        $openid = $message['FromUserName'];
        $active_ts = $message['CreateTime'];
        $card_id = $message['CardId'];
        $code = $message['UserCardCode'];

        //激活
        $info = [
            'membership_number'        => $code,
            'code'                     => $code,
            'activate_begin_time'      => time(),
            'activate_end_time'        => '',
            'init_bonus'               => '0', //初始积分，不填为0。
            'init_balance'             => '0', //初始余额，不填为0。
        ];

        //将事件的内容记录到日志
        $responses = json_encode($info,true);
        $mylog = storage_path('logs/vipcard.log');
        if(file_exists($mylog)){
            file_put_contents($mylog,$responses."\n",FILE_APPEND);
        }else{
            \Log::info($responses);
        }

        $vipcard = new VipCardController();
        $active = $vipcard->active($info);
        if($active['errcode']==0){
            //插入会员卡信息
            $result = $vipcard->info($card_id,$code);
        }

        return $result;
    }


}
