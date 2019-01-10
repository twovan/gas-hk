<?php

namespace App\Http\Controllers\WeChat;

use EasyWeChat\Factory;
use Illuminate\Http\Request;

class MenuController extends BaseController
{
    protected $official_config;
    protected $app;

    public function __construct()
    {
        parent::__construct();
        $this->official_config = config('wechat.official_account.default');
        $this->app =  app('wechat.official_account');
    }



    //添加菜单
    public function add()
    {
        $buttons = [
            [
                "type" => "view",
                "name" => "加油",
                "url"  => "http://www.soso.com/"

            ],
            [
                "type" => "view",
                "name" => "会员中心",
                "url"  => "http://gashk.twovan.cn/wechat/vipcard/index"
            ],
            [
                "type" => "view",
                "name" => "关于我们",
                "url"  => "http://www.qq.com/"
            ],

        ];
       return $this->app->menu->create($buttons);
    }

    //查看菜单
    public function list()
    {
        $list =  $this->app->menu->list();
        return $list;
    }
}
