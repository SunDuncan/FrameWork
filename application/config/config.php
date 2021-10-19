<?php
/*
 * @Description: 
 * @version: 
 * @Author: SunDuncan
 * @Date: 2021-09-25 13:28:59
 * @LastEditors: SunDuncan
 * @LastEditTime: 2021-10-19 18:19:29
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
        "dbname" => getenv("DB_NAME") ? getenv("DB_NAME") : "king_question"
    ],

    // 应用的整体配置
    "app" => [
        'default_module' => 'api/index/index'
    ],
    // 小程序的配置
    "wechat" => [
        "app_id" => 'wxb64b4c0a1e417723',
        "app_secret" => 'cf3e40dc6a138951117bcc5c06bed73b'
    ] 
);