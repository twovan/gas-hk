<?php

namespace App\Http\Controllers\Backend;

use App\Models\GasProduct;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends BaseController
{
    public function index(Request $request){

        $data = GasProduct::get();
        return view('backend.product.index', [
            'lists' => $data,
            'list_title_name' => '油品管理',
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
            'gas_no' => 'required',
            'price' => 'required',
            'type' => 'required',

        ]);
        $request_all = $request->all();
        // dump($post);exit();
        if ($validator->fails()) {
            return $this->err('填写的信息不全!');
        }

        $input = $request->only('gas_no','price','type','status','remark');
        $input['price'] *= 100;

        try{
            $res =  GasProduct::create($input);
        }catch (\Exception $ex){
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

        $input = $request->only('id','gas_no','price','type','status','remark');
        if(!empty($input['price'])){
            $input['price'] *= 100;
        }
        $res = GasProduct::find($id)->update($input);
        if($res){
            return $this->ok();
        }else{
            return $this->err('失败');
        }
    }
}
