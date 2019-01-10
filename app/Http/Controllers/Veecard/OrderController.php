<?php

namespace App\Http\Controllers\Veecard;


use App\Http\Controllers\Controller;

use App\Models\OrderLog;
use App\Models\Order;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\WeChat\BonusController;



class OrderController extends Controller
{

    /**
     * 保存订单日志
     * @param array $data
     */
    public  function saveOrder($data=[])
    {
        try{
            $Order = new order();
            $order = createObect($Order, $data, $filter=null);
            $order->order_at = time();
            $order->status = 0;
            $order->save();
            return true;
        }catch (\Exception $e)
        {
            \Log::info('saveOrderError:'.$e->getMessage());
            return false;
        }
    }

    /**
     * 保存订单日志
     * @param array $data
     */
    public  function saveOrderLog($data=[])
    {
        try{
            $orderLog = new OrderLog();
            $filter = ['version', 'signType', 'signature','resultSubMsg'];
            $orderLog = createObect($orderLog, $data, $filter);
            if(empty($orderLog->payInfo)){
                $orderLog->payInfo = null;
            }elseif(is_array($orderLog->payInfo)){
                $orderLog->payInfo = json_encode($orderLog->payInfo,true);
            }else{
                $orderLog->payInfo = $orderLog->payInfo;
            }
            $orderLog->payTime = null;
            $orderLog->updatedTs = date('Y-m-d H:i:s');
            $orderLog->save();
            return true;
        }catch (\Exception $e)
        {
            \Log::info('saveOrderError:'.$e->getMessage());
            return false;
        }
    }

    /**
     * 修改订单数据
     * @param array $data
     */
    public function updateOrder($data)
    {
        if(empty($data['outTransNo'])){
            return false;
        }
        try{
           $order =  Order::where('order_no',$data['outTransNo'])->get();
           if(collect($order)->isNotEmpty()){

               //更新订单状态
               $result = Order::where('order_no',$data['outTransNo'])->update([
                   'status'=>$data['status'],
               ]);
              if($result == false){return false;}

               //更新会员卡积分
               $bonus = new  BonusController();
               $bonus_result = $bonus->updateBonus($data['outTransNo']);
               if(false == $bonus_result){
                   //存入缓存
                   $arr = [];
                   if(Cache::has('bonus_order_no')){
                       $arr = Cache::get('bonus_order_no');
                   }
                   array_push($arr,$data['outTransNo']);
                   Cache::forever('bonus_order_no',$arr);
               }
               return $result;
           }

        }catch (\Exception $e){
            \Log::info($e->getMessage());
          return false;
        }

    }

}

