<?php

namespace App\Libraries\Pay;

/**
 * @Description: RSA2认证
 * @Date 2018年12月18日
 * @author eden
 * @package Pay
 */
class RsaClass

{


    private static $PRIVATE_KEY = '';//我方私钥

    private static $PUBLIC_KEY = '';//我方共钥

    private static $PLATFORM_KEY = '';//平台公钥

    protected static function LoadPath($config=null)
    {
        extension_loaded('openssl') or die('no openssl suport,please check php.ini');
        $config = ReadConfigClass::read($config);
        if (empty($config['private_key_path'])) {
            die('private_key_path is not exists,please check config');
        }

        if (empty($config['public_key_path'])) {
            die('public_key_path is not exists,please check config');
        }
        if (empty($config['platform_key_path'])) {
            die('platform_key_path is not exists,please check config');
        }
        try {
            static::$PRIVATE_KEY = file_get_contents($config['private_key_path']);
            static::$PUBLIC_KEY = file_get_contents($config['public_key_path']);
            //平台公钥需要格式化
            $platform_key = file_get_contents($config['platform_key_path']);
            static::$PLATFORM_KEY = static::formatPlatformKey($platform_key);
        } catch (\Exception $e) {
            die($e->getMessage());
        }


    }

    /**
     * 获取私钥
     * @return bool|resource
     */

    private static function getPrivateKey($privKey)

    {

        return openssl_pkey_get_private($privKey);

    }

    /**
     * 获取公钥
     * @return bool|resource
     */

    private static function getPublicKey($publicKey)

    {
        return openssl_pkey_get_public($publicKey);

    }

    /**
     * 获取平台公钥
     *
     * @return bool|resource
     */

    private static function getPlatformPublicKey($platformKey)

    {
        return openssl_pkey_get_public($platformKey);

    }

    /**
     * 因为平台公钥格式不对，需要格式化下
     * @param string $data
     */
    private static function formatPlatformKey($data = '')
    {

        $start_key = "-----BEGIN PUBLIC KEY-----\n";
        $end_key = "-----END PUBLIC KEY-----\n";
        $str = chunk_split($data, 64, "\n");
        return $start_key . $str . $end_key;

    }

    /**
     * 创建签名,这边是用我们生成密钥对中的私钥进行加密，把公钥给合作机构，他们来验签
     * @param muti $respMap 数据
     * @return null|string
     */

    public static function createSign($respMap,$config=null)

    {
        self::LoadPath($config);
        if (is_array($respMap)) {
            $data = static::getSignatureStr($respMap);
        } elseif (is_string($respMap)) {
            $data = $respMap;
        } else {
            $data = '';
        }
        return openssl_sign(
            $data,
            $sign,
            self::getPrivateKey(static::$PRIVATE_KEY),
            OPENSSL_ALGO_SHA256
        ) ? base64_encode($sign) : null;

    }

    /**
     * 验证签名,这里用合作机构给的平台公钥进行验证，因为他们用他们的密钥加密，我们就得用他们提供的公钥里德验签
     * @param muti $data 数据
     * @param string $sign 签名
     * @return bool
     */

    public static function verifySign($respMap, $sign = '',$config=null)

    {
        self::LoadPath($config);
        if (is_array($respMap)) {
            $data = static::getSignatureStr($respMap);
        } elseif (is_string($respMap)) {
            $data = $respMap;
        } else {
            $data = '';
        }
        //var_dump($sign);die;
        return (bool)openssl_verify(
            $data,
            base64_decode($sign),
            self::getPlatformPublicKey(static::$PLATFORM_KEY),
            OPENSSL_ALGO_SHA256

        );

    }

    /**
     * 将数组(数组排序:ASCII码从小到大排序（字典序)转成字符串，key1=values&key2=value2…
     *
     * @param Array $respMap 数据
     * @return bool
     */
    public static function getSignatureStr($respMap)
    {
        $data = '';
        if (!is_array($respMap)) {
            return $data;
        }

        ksort($respMap);
        foreach ($respMap as $key => $value) {
            if (!empty($respMap[$key]) && $key != "signature") {
                $data .= $key . '=' . $value . '&';
            }
        }

        if (!empty($data)) {
            $data = substr($data, 0, strlen($data) - 1);
        }

        return $data;
    }

}