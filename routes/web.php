<?php

use Illuminate\Routing\Router;
Route::get('/',function (){
    return view('welcome');
});

Route::group([
    'prefix'        => 'backend',
    'namespace'     => 'Backend',
    'middleware'    => ['web'],
], function (Router $router) {

    $router->get('login', ['as' => 'backend.loginGet', 'uses' => 'PublicController@loginGet']);
    #$router->post('login', ['as' => 'backend.loginPost', 'uses' => 'PublicController@loginPost']);
    
    $router->get('logout', ['as' => 'backend.logout', 'uses' => 'PublicController@logout']);
    $router->post('upload/image', ['as' => 'backend.uploadImg', 'uses' => 'UploadController@image']);

    $router->get('index', ['as' => 'backend.index', 'uses' => 'IndexController@index']);
    $router->get('base', ['as' => 'backend.base', 'uses' => 'IndexController@base']);

    $router->get('admin', ['as' => 'backend.admin.index', 'uses' => 'AdminController@index']);
    $router->post('admin', ['as' => 'backend.admin.save', 'uses' => 'AdminController@save']);

    $router->get('store', ['as' => 'backend.store.index', 'uses' => 'StoreController@index']);
    $router->post('store', ['as' => 'backend.store.save', 'uses' => 'StoreController@save']);

    $router->get('customer', ['as' => 'backend.customer.index', 'uses' => 'UserController@customer']);
    $router->get('barber', ['as' => 'backend.barber.index', 'uses' => 'UserController@barber']);
    $router->post('user', ['as' => 'backend.user.save', 'uses' => 'UserController@save']);

    //订单
    $router->get('order', ['as' => 'backend.order.index', 'uses' => 'OrderController@index']);
    $router->post('order', ['as' => 'backend.order.save', 'uses' => 'OrderController@save']);


    //油品管理
    $router->get('product', ['as' => 'backend.product.index', 'uses' => 'ProductController@index']);
    $router->post('product', ['as' => 'backend.product.save', 'uses' => 'ProductController@save']);


    //油枪管理
    $router->get('nozzle', ['as' => 'backend.nozzle.index', 'uses' => 'NozzleController@index']);
    $router->post('nozzle', ['as' => 'backend.nozzle.save', 'uses' => 'NozzleController@save']);





});

//汇卡回调
Route::group([
    'prefix'        =>'Veecard',
    'namespace'     => 'Veecard',
    'middleware'    => ['web'],
], function (Router $router) {
    //回调
    $router->post('service/notify', ['as' => 'veecard.notify', 'uses' => 'ServiceController@notify']);

    //下单
    $router->post('service/order', ['as' => 'veecard.order', 'uses' => 'ServiceController@order']);

    //查询
    $router->post('service/query', ['as' => 'veecard.query', 'uses' => 'ServiceController@query']);
});

Route::group([
    'middleware'    => ['api'],
], function (Router $router) {
    $router->post('backend/login', ['as' => 'backend.loginPost', 'uses' => 'Backend\PublicController@loginPost']);

    $router->post('index/login', ['as' => 'index.loginPost', 'uses' => 'Index\PublicController@loginPost']);
});
