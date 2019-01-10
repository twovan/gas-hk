<?php

namespace App\Http\Controllers\Backend;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class OrderController extends BaseController
{
    public function index(Request $request){
        $request_order_no = $request->get('order_no');
        $request_stime = $request->get('stime');
        $request_etime = $request->get('etime');
        $where = [];
        if($request_order_no){
            $where['order_no'] = $request_order_no;
        }
        if($request_stime){
            $where[] = ['order_at','>=',strtotime($request_stime.' 00:00:00')];
        }
        if($request_etime){
            $where[] = ['order_at','<=',strtotime($request_etime.' 23:59:59')];
        }

        if ($request){
            $data = Order::where($where)
                ->with('vipcard')
                ->orderBy('order_at','desc')
                ->paginate(config('params')['pageSize']);
        }
        //dd($data);
        return view('backend.order.index', [
            'lists' => $data,
            'list_title_name' => '订单',
            'request_params' => $request,
        ]);
    }

    public function save(Request $request){
        $id = $request->input('id');
        $request_all = $request->all();
        $res = ThisModel::find($id)->update($request_all);
        if($res){
            return $this->ok();
        }else{
            return $this->err('失败');
        }
    }
}
