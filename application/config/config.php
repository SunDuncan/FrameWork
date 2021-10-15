<?php
/*
 * @Description: 
 * @version: 
 * @Author: SunDuncan
 * @Date: 2021-09-25 13:28:59
 * @LastEditors: SunDuncan
 * @LastEditTime: 2021-10-15 17:15:07
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
        "user" => getenv("DB_USER") ? getenv("DB_USER") : "root", // 默认的用户名
        "pass" => getenv("DB_PWD") ? getenv("DB_PWD") : "root", // 默认的密码
        "dbname" => getenv("DB_NAME") ? getenv("DB_NAME") : "local_user"
    ],

    // 应用的整体配置
    "app" => [
        'default_module' => 'api/Index/index'
    ]
);