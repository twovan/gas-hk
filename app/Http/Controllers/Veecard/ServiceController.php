<?php

namespace App\Http\Controllers\Veecard;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use App\Libraries\Pay\RsaClass;
use App\Libraries\Pay\UniformOrderClass;
use App\Models\VeecardNotify;



class ServiceController extends Controller
{

    //public $model;

    //异步接受数据
    public function notify(Request $request)
    {
        //验证数据有效性
        $validator =Validator::make($request->all(),[
            'requestNo' => 'required',
            'transId' => 'required',
            'mchNo' => 'required',
            'productId' => 'required',
            'transDate' => 'required',
            'outTransNo' => 'required',
            'signature' => 'required',

        ]);

        $responses = json_encode($request->all(), true);
        $mylog = storage_path('logs/callback.log');
        if(file_exists($mylog)){
            file_put_contents($mylog,$responses."\n",FILE_APPEND);
        }else{
            \Log::info(json_encode($request->all(), true));
        }

        // dump($post);exit();
        if ($validator->fails()) {
           // \Log::info($validator->errors());
            return 'error ';
        }

        $post = $request->input();
        if(isset($post['s'])){
            unset($post['s']);
        }
        //验证答名
        $bool = RsaClass:: verifySign($post, $post['signature']);
        if ($bool == false) {
            \Log::info('signature is failed');
            return 'ERROR:signature is failed';
        }



        //更新订单中的数据
        $orderData = $post;
        if($post['resultCode'] == '0000'){
            $orderData['status'] =1;
        }else{
            $orderData['status'] = 0;
        }
        $order = new OrderController();
       if(false === $order->updateOrder($orderData)){
           return 'ERROR:update order is failed';
       }
        //保存回调数据
        if(false==$this->saveNotify($post)){
            return 'ERROR:save nofify is failed';
        }
        return 'SUCCESS';


    }


    /**
     * 统一下单示例
     */
    public function order(Request $request)
    {

        $post = $request->all();
        $validator = Validator::make($request->all(), [
            'open_id' => 'required',
            'pay_fee'=> 'required',
            'pay_type'=> 'required',
        ]);

        if ($validator->fails()) {
            return 'params is error ';
        }
        //先生成订单
        $data = $post;
        $data['order_no']= buidOrderNo();
        $data['queue_no']= buidGuid();
        $order  = new OrderController();
        if(isset($data['s'])){
            unset($data['s']);
        }

        if(false == $order->saveOrder($data)){
            return 'bild order is error ';
        }


        $openid = $request->input('open_id');
        $transAmount = $request->input('pay_fee');
        $result = '';
        $data = UniformOrderClass::WXMP($openid,$transAmount,$data['order_no'] );
        //var_dump($data);die;
        if (!$data) {
            $result = 'sign is error';
        } else {
            //保存订单
            $result = $data;
            $arr = (is_array($data)) ? $data : json_decode($data,true);
            empty($arr['transAmount'])&&$arr['transAmount'] = $transAmount;
           //dd($arr['resultCode']);
            if($arr['resultCode'] >0){
                $update =[
                    'outTransNo'=>$arr['outTransNo'],
                    'status'=>-1];

                try{
                    $order  = new OrderController();
                   $res =  $order->updateOrder($update);
                   //dd($res);
                }catch (\Exception $e){
                    Log::info($e->getMessage());
                }

            }
           // dd($arr);
            if(false ==$order->saveOrderLog($arr)){
                //因为数据重要性,保存到缓存中
                Cache::forever('out_trans_no_'.$arr['outTransNo'],$result);
                $result ='save orderlog error';
            }



        }
        return $result;
    }

    /**
     * 查询订单
     */
    public function query(Request $request)
    {

        $validate = Validator::make($request->all(),[
            'transDate' => 'required',
            'requestNo' => 'required',
            'outTransNo' => 'required',
        ]);
        $post = $request->input();
        if ($validate->fails()) {
            $responses = ['code'=>1,'msg'=>'param is error ','data'=>'{}'];
            return  response()->json($responses);
        }
        $requestNo = $request->input('requestNo');
        $oriTransDate = $request->input('transDate');
        $oriOutTransNo = $request->input('outTransNo');

        $refundNo = '';
        $res = UniformOrderClass::queryOrder($requestNo, $oriTransDate, $oriOutTransNo, $refundNo);
        if (!$res) {
            $responses = ['code'=>1,'msg'=>'出错啦，签名sign有误， ','data'=>'{}'];
            return  response()->json($responses);
        } else {
            //修改订单
            $result = $res;
            $order = new OrderController();
            $arr = is_array($res)?$res:json_decode($res,true);
            $arr['outTransNo'] = $oriOutTransNo;
            if($arr['resultCode'] == '0000'){
                $arr['status'] = $arr['orderState'] == 'FINISHED'?1:0;
                if(false == $order->updateOrder($arr)){
                    $responses = ['code'=>1,'msg'=>'出错啦，更新订单表失败 ','data'=>$arr];
                    return  response()->json($responses);
                }else{
                    $responses = ['code'=>0,'msg'=>'刷新成功，请查看订单状态 ','data'=>$arr];
                    return  response()->json($responses);
                }
            }else{
                $responses = ['code'=>1,'msg'=>$arr['resultMsg'],'data'=>$arr];
                return  response()->json($responses);
            }



        }
        return response($result);
    }





    /**
     * 保存回调数据
     * @param array $data
     */
    protected function saveNotify($data=[])
    {
        try{
            $noftify = new VeecardNotify();
            $noftify->requestNo = $data['requestNo'];
            $noftify->accessNo = $data['accessNo'];
            $noftify->transId = $data['transId'];
            $noftify->productId = $data['productId'];
            $noftify->mchNo = $data['mchNo'];
            $noftify->mchNo = $data['mchNo'];
            $noftify->outTransNo = $data['outTransNo'];
            $noftify->transAmount = $data['transAmount'];
            $noftify->transNo = $data['transNo'];
            $noftify->bankTradeNo = empty($data['bankTradeNo'])?null:$data['bankTradeNo'];
            $noftify->bankType = empty($data['bankType'])?null:$data['bankType'];
            $noftify->bankUserId = empty($data['bankUserId'])?null:$data['bankUserId'];
            $noftify->transDate = strtotime($noftify->transDate);
            $noftify->payTime = strtotime($noftify->payTime);
            $noftify->updated_at = date('Y-m-d H:i:s');

            $noftify->save();
            return true;
        }catch (\Exception $e){
            \Log::info('saveNotityError:'.$e->getMessage());
            return false;
        }

    }

}

