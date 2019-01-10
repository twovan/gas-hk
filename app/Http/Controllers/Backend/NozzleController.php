<?php

namespace App\Http\Controllers\Backend;

use App\Models\GasNozzle;
use App\Models\GasProduct;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NozzleController extends BaseController
{
    public function index(Request $request){

        $data = GasNozzle::get();
        $gas_no_list = [];
        $gaslist = GasProduct::where('status',1)->get();
        if(collect($gaslist)->isNotEmpty()){
            $gas_no_list = collect($gaslist)->pluck('gas_no')->all();
        }
        return view('backend.nozzle.index', [
            'lists' => $data,
            'gas_no_list'=>$gas_no_list,
            'list_title_name' => '油枪管理',
            'request_params' => $request,
        ]);
    }

    /**
     * 信息保存
     * @param Request $request
     * @return array
     */
    public function save(Request $request){
        $option = $request->input('option');
        if($option == "add"){
            return $this->create($request);
        }elseif($option == "edit"){
            return $this->update($request);
        }else{
            return $this->err('无权操作!');
        }
    }

    /**
     * 添加信息
     * @param $request
     * @return array
     */
    public function create($request){

        $validator =Validator::make($request->all(),[
            'number'=>'required',
            'gas_no' => 'required',
        ]);
        $request_all = $request->all();
        // dump($post);exit();
        if ($validator->fails()) {
            return $this->err('填写的信息不全!');
        }

        $input = $request->only('gas_no','number','status','remark');
              try{
            $res =  GasNozzle::create($input);
        }catch (\Exception $ex){
                  dd($ex->getMessage());
            \Log::info($ex->getMessage());
            return $this->err('添加失败');
        }
        return $this->ok();
    }

    /**
     * 更改信息
     * @param $request
     */
    public function update($request){
        $id = $request->input('id');
        if(empty($id)){
            return $this->err('失败，id不能为空');
        }
        $request_all = $request->all();

        $input = $request->only('id','gas_no','number','status','remark');

        $res = GasNozzle::find($id)->update($input);
        if($res){
            return $this->ok();
        }else{
            return $this->err('失败');
        }
    }
}
