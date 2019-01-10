<?php

namespace App\Http\Controllers\Backend;

use App\Models\User as ThisModel;
use App\Models\VipCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends BaseController
{
    // 用户
    public function customer(Request $request)
    {

        $data =[];
        $where = [
            'status' => 1,
        ];
        $wx_name = $request->get('wx_name');
        $phone = $request->get('phone');
        if (!empty($wx_name)) {
            $where[] = ['wx_name', 'like', '%' . $wx_name . '%'];
            $data = ThisModel::where($where)->with('vipcard')->orderBy('id', 'desc')->paginate(config('params')['pageSize']);
        } elseif (!empty($phone)) {
            $openid = VipCard::where('phone',$phone)->value('openid');
            $data = ThisModel::where('openid',$openid)
                ->orderBy('id', 'desc')
                ->paginate(config('params')['pageSize']);
        }else{
            $data = ThisModel::where($where)->with('vipcard')->orderBy('id', 'desc')->paginate(config('params')['pageSize']);
        }

        $admins = Auth::guard('admin')->user();
        return view('backend.customer.index', [
            'lists' => $data,
            'list_title_name' => '用户',
            'admins' => $admins,
            'request_params' => $request,
        ]);
    }


    //保存
    public function save(Request $request)
    {
        $id = $request->input('id');
        $request_all = $request->all();

//        if (ThisModel::isExist(['phone' => $request_all['phone']], $id)) {
//            return $this->err('手机号已存在');
//        }
        if(isset($request_all['phone'])){
            unset($request_all['phone']);
        }
        if(isset($request_all['s'])){
            unset($request_all['s']);
        }
        if ($id) {
            $res = ThisModel::find($id)->update($request_all);
        } else {
            $res = ThisModel::create($request_all);
        }
        if ($res) {
            return $this->ok();
        } else {
            return $this->err('失败');
        }
    }

}
