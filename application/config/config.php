<?php
/*
 * @Description: 
 * @version: 
 * @Author: SunDuncan
 * @Date: 2021-09-25 13:28:59
 * @LastEditors: SunDuncan
 * @LastEditTime: 2021-10-26 18:38:31
 */
/**
 * Author: SunDuncan
 */
/**
 * 配置文件
 */
return array(
    // 数据库的配置
    "db" => [
        "host" => getenv("DB_HOST") ? getenv("DB_HOST") : "localhost:8889",
        "user" => getenv("DB_USER") ? getenv("DB_USER") : "root", // 默认的用户名
        "pass" => getenv("DB_PWD") ? getenv("DB_PWD") : "root", // 默认的密码
        "dbname" => getenv("DB_NAME") ? getenv("DB_NAME") : "local_user"
     ],

    // 应用的整体配置
    "app" => [
        'default_module' => 'api/index/index'
    ],
    // 小程序的配置
    "wechat" => [
        "app_id" => getenv("WECHAT_APP_ID"),
        "app_secret" => getenv("WECHAT_APP_SECRET")
    ],
    // 阿里云的配置
    "aliyun" => [
        'access_key' => getenv("ALIYUN_ACCESS_KEY"),
        'access_secret' => getenv("ALIYUN_ACCESS_SECRET"),
        "regionId" => 'hangzhou',
        'expire' => 5 * 60
    ],
    // redis的配置
    "redis" => [
        'host' => getenv("REDIS_HOST") ? getenv("REDIS_HOST") : '127.0.0.1',
        "port" => getenv("REDIS_PORT") ? getenv("REDIS_PORT") : '6379'
    ],
    // 微信网页授权中转接口
    "wxosrv" => [
        'partner_appid' => getenv("WXOSRV_PARTNER_APPID"),
        'target_url' => getenv("WXOSRV_TARGET_URL"),
        "secret" => getenv("WXOSRV_SECRET")
    ],
    "wxpay" => [
        'mch_id'  => getenv("WXPAY_MUCH_ID"),
        'pay_apikey' => getenv("WXPAY_APIKEY"),
        "appid" => getenv("WXPAY_APP_ID"),
        'notify_url' => getenv('WXPAY_NOTIFY_URL'),
        'api_cert' => getenv("WXPAY_REFUND_API_CERT"),
        'api_key' => getenv("WXPAY_REFUND_API_KEY"),
    ]
);