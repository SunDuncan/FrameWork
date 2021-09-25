<?php
/**
 * Author: SunDuncan
 */
/**
 * 配置文件
 */
return array(
    // 数据库的配置
    "db" => [
        "user" => getenv("DB_USER") ? getenv("DB_USER") : "root", // 默认的用户名
        "pass" => getenv("DB_PWD") ? getenv("DB_PWD") : "root", // 默认的密码
        "dbname" => getenv("DB_NAME") ? getenv("DB_NAME") : "local_user"
    ],

    // 应用的整体配置
    "app" => [
        "default_platform" => 'api',
    ],

    // 接口的默认配置
    "api" => [
        "default_controller" => "Index",
        'default_action' => "index" 
    ]
);