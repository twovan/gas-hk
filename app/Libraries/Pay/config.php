<?php
$private_key_path = dirname(__FILE__).DIRECTORY_SEPARATOR.'storage'.DIRECTORY_SEPARATOR.'access-prv-key.pem';
$public_key_path =  dirname(__FILE__).DIRECTORY_SEPARATOR.'storage'.DIRECTORY_SEPARATOR.'access-pub-key.pem';
$platform_key_path = dirname(__FILE__).DIRECTORY_SEPARATOR.'storage'.DIRECTORY_SEPARATOR.'platform-pub-key.pem';
return array(
    'private_key_path'=>$private_key_path,//我方生成私钥
    'public_key_path'=>$public_key_path,//我方生成公钥
    'platform_key_path'=>$platform_key_path,//合作方提供的平台公钥
    'unified_order_url'=>'http://online.u-easy.cn/gateway/api/consumeTrans',//'http://113.98.101.186:23060/gateway/api/consumeTrans',//统一下单接口地址
    'web_notify_url'=>'',//页面通知地址，用于跳转到商户前台结果页面
    'notify_url'=>'http://gashk.twovan.cn/Veecard/service/notify',//异步通知地址(我们的)，用于后台推送交易结果，需要提前报备到合作平台
    'org_id'=>'200000026253',//接入机构号
    'mch_no'=>'860420155410001',//商户号
);
