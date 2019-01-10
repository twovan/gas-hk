<?php

use Illuminate\Routing\Router;

Route::group([
    'prefix'        => 'wechat',
    'namespace'     => 'WeChat',
    'middleware'    => ['api'],
], function (Router $router) {
    $router->any('/', 'PublicController@serve');
    $router->get('test', 'PublicController@test');
    //菜单
    $router->get('menu/list', 'MenuController@list');
    $router->get('menu/add', 'MenuController@add');


});

Route::group([
    'prefix'        => 'wechat',
    'namespace'     => 'WeChat',
    'middleware'    => ['web', 'wechat.oauth'],
//    'middleware'    => ['web'],
], function (Router $router) {

    //充值
    $router->get('recharge', 'RechargeController@index');
    $router->post('recharge/add', 'RechargeController@add');

    //快捷加油
    $router->get('payment', 'PaymentController@index');
    $router->post('payment/add', 'PaymentController@add');
    $router->post('payment/getGasNo', 'PaymentController@getGasNo');

    //微信会员卡
    $router->get('vipcard/index', 'VipCardController@index');
    $router->get('vipcard/getCard', 'VipCardController@getCard');
    $router->get('vipcard/myCard', 'VipCardController@getMycard');


});

