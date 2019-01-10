<?php

namespace App\Http\Controllers\WeChat;

use App\Models\Order;
use App\Models\VipCard;
use Carbon\Carbon;
use Illuminate\Http\Request;
use EasyWeChat\Factory;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\WeChat\VipCardController;


class BonusController extends BaseController
{
    protected $official_config;

    public function __construct(){
        parent::__construct();
        $this->official_config = config('wechat.official_account.default');
    }

    //获取用户积分余额
    public function getUserBonus(Request $request){
        $bonus = 0;
        $app = Factory::officialAccount($this->official_config);
        $user = $request->getUser;
        if ($user) {
            $bonus = VipCard::where('openid',$user['openid'])->value('bonus');
            $bonus = (int)$bonus;
        }
        return $bonus;
    }


    /**
     * 根据订单号更新会员积分
     * @param $order_no 订单号
     * @param bool $updated //是否更新(用于自定义开关)
     * @return bool
     */
    public function updateBonus($order_no,$is_update=true){

        //1.根据订单号查询订单信息
        $orders = Order::where('order_no',$order_no)->first();
        if(collect($orders)->isNotEmpty()){
            $orders = collect($orders)->all();
        }
        if(empty($orders)){
            return false;
        }

        //2.得到积分是否被更新过,更新了就不操作
        $is_bonus = $orders['is_bonus'];
        if($is_bonus == 1 || $is_update == false){
            return false;
        }

        //3.启动事务，更新订单表中变化的积分,更新会员卡积分余额，同步到微信会员卡
        DB::beginTransaction(); //启动事务
        try {
            $openid =  $orders['open_id'];
            $pay_fee = $orders['pay_fee'];
            $money = $pay_fee/100; //将分换成元
            $add_bonus = getBonus($money);

            //从会员卡表中获取会员卡code
            $vip= new VipCardController();
            $vip_info = $vip->getVipInfo($openid);
            isset($vip_info) && $code = $vip_info['code'];
            if(!isset($code)) return false;
            isset($vip_info) && $card_id = $vip_info['card_id'];
            if(!isset($card_id)) return false;

            //更新微信端用户信息,失败回滚
            $old_bonus = $vip_info['bonus'];//从表中拿到原有积分
            $new_bonus = $old_bonus +$add_bonus;
            $record_bonus  = '消费'.$money.'元，获得'.$add_bonus.'积分';
            $infos = [
                'code'  =>$code,
                'card_id'=>$card_id,
                'bonus'=> $new_bonus,
                'add_bonus'=>$add_bonus,
                'record_bonus'=>$record_bonus,
            ];

            if(false == $vip->updateWxMemberInfo($infos)){
                DB::rollback();
                return false;
            }

            //更新vipcard表的积分余额
            $updateVipResult  =  VipCard::where('code',$code)->update(['bonus'=>$new_bonus]);

            if(!$updateVipResult){
                DB::rollback();
                return false;
            }

            //更新订单表中信息
            $orderData = [
                'add_bonus' => $new_bonus,
                'record_bonus' => $record_bonus,
                'is_bonus' => 1,
            ];
            $updateOrderResult = Order::where('order_no',$order_no)->update($orderData);
            if(false == $updateOrderResult){
                DB::rollback();
                return false;
            }

        } catch(\Exception $e)
        {
            DB::rollback();
            return false;
        }

        DB::commit(); //提交事务
        return true;
    }


}
