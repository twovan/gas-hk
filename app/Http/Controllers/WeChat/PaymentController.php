<?php

namespace App\Http\Controllers\WeChat;

use App\Models\User;
use App\Models\WeChat;
use App\Models\GasNozzle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use EasyWeChat\Factory;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Veecard\ServiceController;

class PaymentController extends BaseController
{
    protected $official_config;

    public function __construct(){
        parent::__construct();
        $this->official_config = config('wechat.official_account.default');
    }

    public function index(Request $request){
        $app = Factory::officialAccount($this->official_config);
        $user = $request->getUser;
        return view('weChat.payment.index', [
            'app' => $app,
            'openid'=>$user['openid'],
            'pay_limit'=>config('params.pay_limit'),
            'title_name' => '汉口加油站',
        ]);
    }

    public function add(Request $request){
        $post = $request->all();
        $pay_limit = config('params.pay_limit');
        $min = $pay_limit['min_money']*100;//以分为单位
        $max = $pay_limit['max_money']*100;//以分为单位
        //
        $validator = Validator::make($request->all(), [
            'open_id' => 'required',
            'pay_fee'=> "required|integer|min:{$min}|max:{$max}",//以分为单位
            'pay_type'=> 'required',
            'nozzle_no'=>'required',
        ]);

        if ($validator->fails()) {
            $data =['code'=>1,'msg'=>'参数错误','data'=>[]];
            return response()->json($data);
        }
        $nozzle_no =  $request->input('nozzle_no');
        $gas_no = GasNozzle::where('number',$nozzle_no)->value('gas_no');
        if(!isset($gas_no)){
            $data =['code'=>1,'msg'=>'油枪号有误，请询问工作人员','data'=>[]];
            return response()->json($data);
        }
        $services = new ServiceController();
        $result = $services->order($request);
        if(empty($result['resultCode'])){
            $data =['code'=>1,'msg'=>'失败，系统有异常','data'=>$result];
        }elseif($result['resultCode']!=='P000'){
            $data =['code'=>1,'msg'=>$result['resultMsg'],'data'=>$result];
        }else{
            $data =['code'=>0,'msg'=>'成功','data'=>$result];
        }


        return response()->json($data);
    }

    /**
     * 通过油枪号获取油品号
     * @param Request $request
     */
    public function getGasNo(Request $request){
        $nozzle_no = $request->input('nozzle_no');
        if($nozzle_no<1){
            $data =['code'=>1,'msg'=>'油枪号不能为空','data'=>[]];
            return response()->json($data);
        }

        $gas_no = GasNozzle::where('number',$nozzle_no)->value('gas_no');
        if(!isset($gas_no)){
            $data =['code'=>1,'msg'=>'油枪号有误，请询问工作人员','data'=>[]];
            return response()->json($data);
        }
        $data =['code'=>0,'msg'=>'查询成功','data'=>['gas_no'=>$gas_no]];
        return response()->json($data);
    }

}
