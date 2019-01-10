<?php
use App\Models\RechargeRule;

/**
 * 将数组中的key转成某个对象的属性并过滤相关key
 * @param $model
 * @param array $arr
 * @param $filter
 * @return bool|object
 */
function createObect($model, $arr = [], $filter)
{
    if (!is_array($arr) || empty($arr)) {
        return false;
    }

    if (isset($filter) && is_string($filter)) {
        $filter = explode(',', $filter);
    }
    foreach ($arr as $k => $v) {
        if (!empty($filter) && in_array($k, $filter)) {
            continue;
        }
        $model = (object)$model;
        $model->$k = $v;
    }
    return $model;
}


/**
 * 模拟post进行url请求
 * @param string $url
 * @param array $post_data
 */
function requestPost($url = '', $post_data = array())
{
    if (empty($url) || empty($post_data)) {
        return false;
    }

    $o = "";
    foreach ($post_data as $k => $v) {
        $o .= "$k=" . urlencode($v) . "&";
    }
    $post_data = substr($o, 0, -1);

    $postUrl = $url;
    $curlPost = $post_data;
    $ch = curl_init();//初始化curl
    curl_setopt($ch, CURLOPT_URL, $postUrl);//抓取指定网页
    curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
    curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
    curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
    $data = curl_exec($ch);//运行curl
    curl_close($ch);

    return $data;
}



/**
 * 带时间的订单号
 */
function buidOrderNo(){
    $ts = date('YmdHis');
    $uuid = substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
    return $ts.$uuid;
}

//生成guid
function buidGuid($prefix="") {
    $str = md5(uniqid(mt_rand(), true));
    $uuid  = substr($str,0,8);
    $uuid .= substr($str,8,4) ;
    $uuid .= substr($str,12,4);
    $uuid .= substr($str,16,4);
    $uuid .= substr($str,20,12);
    return $prefix . $uuid;
}

//通过积分规则算出积分
function getBonus($money,$ceil=true,$rule=null){
    $bonus = 0;
    $rule_key = empty($rul)?env('BONUS_RULE_DEFAULT','1:1'):$rule;
    $bonus_rule = config('params.bonus_rule');
    if(key_exists($rule_key,$bonus_rule)){
        $bonus = bcmul($money.'',$bonus_rule[$rule_key]['percent'].'');
        $bonus = $ceil==true ? ceil($bonus) :$bonus;
    }
    $bonus = (int)number_format($bonus,0,'','');
    return $bonus;
}

//获取积分规则说明
function getBonusInfo($rule=null){
    $infos = '';
    $rule_key = empty($rul)?env('BONUS_RULE_DEFAULT','1:1'):$rule;
    $bonus_rule = config('params.bonus_rule');
    if(key_exists($rule_key,$bonus_rule)){
        $infos = $bonus_rule[$rule_key]['info'];
    }
    return $infos;
}
//充值规则获取
function getRechargeRules($recharge){
    $name_key = 'gift_'.$recharge;
    $rules = RechargeRule::where('name',$name_key)->value('value');
    $gift = empty($rules)? 0: $rules*100;
    return $gift;
}