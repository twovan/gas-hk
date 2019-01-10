<?php

namespace App\Http\Controllers\WeChat;

use App\Models\User;
use App\Models\WeChat;
use Carbon\Carbon;
use Illuminate\Http\Request;
use EasyWeChat\Factory;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Veecard\ServiceController;

class RechargeController extends BaseController
{
    protected $official_config;

    public function __construct(){
        parent::__construct();
        $this->official_config = config('wechat.official_account.default');
    }

    public function index(Request $request){
        $app = Factory::officialAccount($this->official_config);
        $user = $request->getUser;
        return view('weChat.recharge.index', [
            'app' => $app,
            'openid'=>$user['openid'],
            'title_name' => '充值',
        ]);
    }

    public function add(Request $request){
        $post = $request->all();
        $validator = Validator::make($request->all(), [
            'open_id' => 'required',
            'pay_fee'=> 'required',
            'pay_type'=> 'required',
        ]);

        if ($validator->fails()) {
            $data =['code'=>1,'msg'=>'参数错误','data'=>[]];
            return response()->json($data);
        }

        $services = new ServiceController();
        $result = $services->order($request);
        if(empty($result['resultCode'])){
            $data =['code'=>1,'msg'=>'充值失败，系统有异常','data'=>$result];
        }elseif($result['resultCode']!=='P000'){
            $data =['code'=>1,'msg'=>$result['resultMsg'],'data'=>$result];
        }else{
            $data =['code'=>0,'msg'=>'充值成功','data'=>$result];
        }


        return response()->json($data);
    }

    public function list(Request $request){

    }

}
