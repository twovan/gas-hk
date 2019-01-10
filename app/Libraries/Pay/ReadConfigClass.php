<?php

namespace App\Libraries\Pay;

/**
 * @Description: 配置文件加载
 * @Date 2018年12月21日
 * @author eden
 * @package Pay
 */
class ReadConfigClass

{

    private static $loaded = false;
    private static $cached;
    public static $CONFIG_PATH;

    public static function read($config = null)
    {
        if (self::$loaded) {
            return self::$cached;
        }
        $file_path = dirname(__FILE__) . DIRECTORY_SEPARATOR . "config.php";

        self::$CONFIG_PATH = isset($config) ? $config : $file_path;
        if (!file_exists(static::$CONFIG_PATH)) {
            return null;
        }
        $local_config = @require(static::$CONFIG_PATH);
        if(!is_array($local_config)){
            return null;
        }
        self::$loaded = true;
        self::$cached= $local_config;
        return self::$cached;

    }

}