<?php

namespace App\Http\Controllers\WeChat;

use App\Models\HairStyle;
use App\Models\Order;
use App\Models\User;
use App\Models\WeChat;
use App\Models\WorkLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use EasyWeChat\Factory;

class OrderController extends BaseController
{
    protected $official_config;

    public function __construct(){
        parent::__construct();
        $this->official_config = config('wechat.official_account.default');
    }

    /**
     * 订单列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $order_status = $request->input('status');
        $request_stime = $request->get('stime');
        $request_etime = $request->get('etime');
        $user = $request->getUser;
        if ($user){
            $openid = $user['openid'];
            $map['openid'] = $openid;
            isset($order_status) &&  $map['status'] = $order_status;
            isset($request_stime) && $map[] = ['order_at','>=',strtotime($request_stime.' 00:00:00')];
            isset($request_etime) && $map[] = ['order_at','<=',strtotime($request_etime.' 23:59:59')];
            $data = Order::where($map)->orderBy('order_at','desc')
                ->paginate(config('params')['pageSize']);

            $app = Factory::officialAccount($this->official_config);
            return view('weChat.order.list', [
                'list' => $data,
                'app' => $app,
                'title_name' => '订单列表',
            ]);
        }else{
            abort(404);
        }
    }

    /**
     * 订单详情
     * @param $id 订单id
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function info($id, Request $request)
    {
        $user = $request->getUser;
        if ($user){
            $map = [
                'id' => $id,
            ];
            $data = Order::where($map)->first();
            if (empty($data)){
                abort(404);
            }
            $app = Factory::officialAccount($this->official_config);
            return view('weChat.order.info_barber', [
                'list' => $data,
                'app' => $app,
                'title_name' => '订单详情',
            ]);

        }else{
            abort(404);
        }

    }




}
