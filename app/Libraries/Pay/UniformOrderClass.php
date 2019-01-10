<?php

namespace App\Libraries\Pay;



// 交易类API案例
class UniformOrderClass
{

    //// 统一下单请求地址
    private static $UNIFIEDORDER_URL;
    //通知地址
    private static $NOTIFY_URL;
    private static $WEBNOTIFY_URL;
    // 接入机构号【测试】
    private static $ORG_ID;
    // 测试商户号
    private static $MCH_NO;


    protected static function LoadConfig($config_path)
    {
        $config = ReadConfigClass::read($config_path);
        if (empty($config['unified_order_url'])) {
            die('unified_order_url is not exists,please check config');
        }

        if (empty($config['notify_url'])) {
            die('notify_url  is not exists,please check config');
        }


        if (empty($config['mch_no'])) {
            die('mch_no is not exists ,please check config');
        }

        if (empty($config['org_id'])) {
            die('org_id is not exists,please check config');
        }

        if (!isset($config['web_notify_url'])) {
            die('web_notify_url is not exists ,please check config');
        }

        static::$UNIFIEDORDER_URL = $config['unified_order_url'];
        static::$NOTIFY_URL = $config['notify_url'];
        static::$WEBNOTIFY_URL = $config['web_notify_url'];
        static::$ORG_ID = $config['org_id'];
        static::$MCH_NO = $config['mch_no'];

    }

    /**
     * 微信公众号支付
     * @Title: WXMP
     * @Date 2018年12月18日
     * @author eden
     * @param $config 证书配置文件
     * @param $openId
     * @param $transAmount
     */
    public static function WXMP($openId, $transAmount,$outTransNo,$config=null)
    {
        self::LoadConfig($config);
        $productId = '1063';
        $transId = '100';
        $dataMap = static::getRequsetMsg($productId, $transId, static::$MCH_NO, static::$ORG_ID, static::$NOTIFY_URL,'',$outTransNo);
        //print_r($dataMap);die;
        $dataMap['transAmount'] = $transAmount;
        $dataMap['openId'] = $openId;

        return static::sendToChannel( $dataMap, static::$UNIFIEDORDER_URL,$config);
    }


    /**
     * 微信扫码支付-正扫
     * @Title: WXScan
     * @Date 2018年12月19日
     * @author eden
     * @param $config 证书配置文件
     * @param $transAmount
     */
    public static function WXScan($transAmount,$outTransNo,$config=null)
    {
        self::LoadConfig($config);
        $productId = '1061';
        $transId = '100';
        //dd(static::$MCH_NO .'  '.static::$ORG_ID);
        $dataMap = static::getRequsetMsg($productId, $transId, static::$MCH_NO, static::$ORG_ID, static::$NOTIFY_URL,'',$outTransNo);
        //print_r($dataMap);die;
        $dataMap['limitPay'] = "";//支付限制
        $dataMap['transAmount'] = $transAmount;
        //$dataMap['openId'] = $openId;

        return static::sendToChannel( $dataMap, static::$UNIFIEDORDER_URL,$config);
    }


    /**
     * 微信小程序支付
     * @Title: WXScan
     * @Date 2018年12月19日
     * @author eden
     * @param $config 证书配置文件
     * @param $transAmount
     */
    public static function WXMinApp($openId, $outTransNo,$transAmount,$config=null)
    {
        self::LoadConfig($config);
        $productId = '1066';
        $transId = '100';
        $dataMap = static::getRequsetMsg($productId, $transId, static::$MCH_NO, static::$ORG_ID, static::$NOTIFY_URL,'',$outTransNo);
        //print_r($dataMap);die;
        $dataMap['limitPay'] = "";//支付限制
        $dataMap['transAmount'] = $transAmount;
        $dataMap['openId'] = $openId;

        return static::sendToChannel( $dataMap, static::$UNIFIEDORDER_URL,$config);
    }


    /**
     * 订单查询
     * @Title: queryOrder
     * @Date 2018年12月19日
     * @author eden
     * @param $config 配置信息
     * @param $oriTransDate  原交易订单日期
     * @param $oriOutTransNo 原始商户交易订单号
     * @param $refundNo      退款订单号
     */
    public static function queryOrder($requestNo,$oriTransDate, $oriOutTransNo, $refundNo,$config=null)
    {
        self::LoadConfig($config);
        $transId = '101';
        $dataMap = static::getQueryMsg($transId, '', '', static::$MCH_NO, static::$ORG_ID, static::$NOTIFY_URL,$requestNo);
        $dataMap['oriTransDate'] = $oriTransDate;
        if (!empty($oriOutTransNo)) {
            $dataMap['oriOutTransNo'] = $oriOutTransNo;
        } else {
            $dataMap['refundNo'] = $refundNo;
        }
        return static::sendToChannel($dataMap, static::$UNIFIEDORDER_URL,$config);
    }


    /**
     * 退货/退款
     * @Title: returnOrder
     * @Description: TODO   transDate is oriTransDate?
     * @Date 2018年12月19日
     * @author eden
     * @param $oriTransDate  原交易订单日期yyyyMMdd
     * @param $oriOutTransNo 原始商户交易订单号
     * @param $refundAmount 退款金额
     * @param $refundReason 退款原因
     */
    public static function returnOrder($requestNo,$oriTransDate, $oriOutTransNo, $refundAmount, $refundReason,$config=null)
    {

        self::LoadConfig($config);
        $transId = '102';
        $transDate = date('Ymd');
        $outTransNo =buidOrderNo();
        $dataMap = static::getQueryMsg($transId, $transDate, $outTransNo, static::$MCH_NO, static::$ORG_ID, static::$UNIFIEDORDER_URL,$requestNo);
        $dataMap['oriTransDate'] = $oriTransDate;
        $dataMap['oriOutTransNo'] = $oriOutTransNo;
        $dataMap['transAmount'] = $refundAmount;
        $dataMap['refundReason'] = $refundReason;
        return static::sendToChannel($dataMap, static::$UNIFIEDORDER_URL,$config);
    }

    /**
     * 消费撤销
     * @Title: cacelOrder
     * @Description: TODO
     * @Date 2018年12月19日
     * @author eden
     * @param $config  配置文件
     * @param $oriTransDate  原交易订单日期
     * @param $oriOutTransNo 原始商户交易订单号
     */
    public static function cacelOrder($requestNo,$oriTransDate, $oriOutTransNo,$config)
    {
        self::LoadConfig($config);
        $transId = '103';
        $transDate = date('Ymd');
        $outTransNo = buidOrderNo();
        $dataMap = static::getQueryMsg($transId, $transDate, $outTransNo, static::$MCH_NO, static::$ORG_ID, static::$UNIFIEDORDER_URL,$requestNo);
        $dataMap['oriTransDate'] = $oriTransDate;
        $dataMap['oriOutTransNo'] = $oriOutTransNo;
        return static::sendToChannel($dataMap, static::$UNIFIEDORDER_URL,$config);
    }

    /**
     * 订单关闭
     * @Title: closeOrder
     * @Description: TODO
     * @Date 2018年12月19日
     * @author OnlyMate
     * @param $oriTransDate  原交易订单日期yyyyMMdd
     * @param $oriOutTransNo 原始商户交易订单号
     */
    public static function closeOrder($requestNo,$oriTransDate, $oriOutTransNo,$config)
    {
        self::LoadConfig($config);
        $transId = '104';
        $transDate = date('Ymd');
        $outTransNo = buidOrderNo();
        $dataMap = static::getQueryMsg($transId, $transDate, $outTransNo, static::$MCH_NO, static::$ORG_ID, static::$UNIFIEDORDER_URL,$requestNo);
        $dataMap['oriTransDate'] = $oriTransDate;
        $dataMap['oriOutTransNo'] = $oriOutTransNo;
        return static::sendToChannel($dataMap, static::$UNIFIEDORDER_URL,$config);
    }


    /**
     * 测试异步通知
     * @Title: getNotify
     * @Description: TODO
     * @Date 2018年12月19日
     * @author OnlyMate
     */
public static function postNotify($requestNo,$transDate, $outTransNo,$productId,$transAmount,$config=null) {

    self::LoadConfig($config);
    $transId = '103';
    $dataMap = static::getQueryMsg($transId, $transDate, $outTransNo, static::$MCH_NO, static::$ORG_ID, static::$UNIFIEDORDER_URL,$requestNo);
    $dataMap['payTime'] = date('YmdHis');
    $dataMap['productId'] = $productId;
    $dataMap['respCode'] = '0000';
    $dataMap['transAmount'] = $transAmount;
    return static::sendToChannel($dataMap, static::$UNIFIEDORDER_URL,$config);

}

    /**
     * 获取请求报文
     * @Title: getRequsetMsg
     * @Description: TODO
     * @Date 2018年12月18日
     * @author eden
     * @return
     */
    public static function getRequsetMsg($productId, $transId, $mchNo, $accessNo, $notifyUrl, $webNotifyUrl=null,$outTransNo)
    {
        $date = date('Ymd');
        $requestNo = buidGuid();
        $dataMap = static::getRequsetHeader($transId, $accessNo,$requestNo);
        $dataMap['productId'] = $productId;
        $dataMap['mchNo'] = $mchNo;
        $dataMap['transDate'] = $date;
        $dataMap['outTransNo'] = isset($outTransNo)?$outTransNo:buidOrderNo();
        $dataMap['goodsSubject'] = '加油';
        $dataMap['notifyUrl'] = $notifyUrl;
        $dataMap['webNotifyUrl'] = $webNotifyUrl;
        //print_r($dataMap);die;
        return $dataMap;
    }

    /**
     * 获取相关查询报文
     * @Title: getQueryMsg
     * @Date 2018年12月19日
     * @author OnlyMate
     * @param transId
     * @param transDate
     * @param outTransNo
     * @return
     */
    public static function getQueryMsg($transId, $transDate, $outTransNo, $mchNo, $accessNo, $notifyUrl,$requestNo)
    {
        $dataMap = static::getRequsetHeader($transId, $accessNo,$requestNo);
        $dataMap['mchNo'] = $mchNo;
        $dataMap['transDate'] = $transDate;
        $dataMap['outTransNo'] = $outTransNo;
        if ($transId != '101') {
            $dataMap['notifyUrl'] = $notifyUrl;
        }

        return $dataMap;
    }


    /**
     * 请求报文的头部信息
     * @Title: getRequsetHeader
     * @Description: TODO
     * @Date 2018年12月18日
     * @author eden
     * @param $ransId
     * @return
     */
    private static function getRequsetHeader($transId, $accessNo,$requestNo)
    {
        $dataMap = [];
        $dataMap = [
            'requestNo' => $requestNo,
            'version' => 'V1.0',
            'transId' => $transId,
            'accessNo' => $accessNo,
            'signType' => "RSA2",
        ];
        return $dataMap;
    }


    /**
     * 发送请求
     * @Title: sendToChannel
     * @Description: TODO
     * @Date 2018年12月18日
     * @author eden
     * @param $config
     * @param $dataMap
     * @param $requestURL
     */
    private static function sendToChannel($dataMap, $requestURL,$config=null)
    {
        try {
            $signature = RsaClass::createSign($dataMap,$config);
            $dataMap['signature'] = $signature;
            //var_dump($signature);
            //dd($dataMap['openId']);
            $responses = requestPost($requestURL, $dataMap);
            $mylog = storage_path('logs/order.log');
            if(file_exists($mylog)){
                file_put_contents($mylog,$responses."\n",FILE_APPEND);
            }
            //echo $responses;die;
            //var_dump($responses);die;
            $dataArr = json_decode($responses, true);

            if (!empty($dataArr['signature'])) {
                $verferResult = RsaClass::verifySign($dataArr, $dataArr['signature'],$config);
                //return $verferResult;
              if($verferResult == true){
                  isset($dataMap['openId'])&&$dataArr['openId'] = $dataMap['openId'];
                  return $dataArr;
              }else{
                  return false;
              }
            }
        } catch (\Exception $e) {
            \Log::info($e->getMessage(),0);//错误写到日志中
            return false;
        }

    }


}