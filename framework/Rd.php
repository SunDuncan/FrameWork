<?php
/*
 * @Description: 
 * @version: 
 * @Author: SunDuncan
 * @Date: 2021-10-18 17:01:28
 * @LastEditors: SunDuncan
 * @LastEditTime: 2021-10-26 11:57:40
 */
// 关于redis的一些基础配置

class Rd {
    public function getRedis($select = 0) {
        $config = [
            "host" => $GLOBALS['config']['redis']['host'],
            'port'         => $GLOBALS['config']['redis']['port'], // redis端口 ssl 6380
            'password'     => '',// 密码
            'select'       => $select, // 操作库
            // 'expire'       => $GLOBALS['config']['redis']['expire'], // 有效期(秒)
            'timeout'      => 0, // 超时时间(秒)
            'persistent'   => true, // 是否长连接
        ];

        if(!extension_loaded("redis")) {
            echo "not support: redis";
        }

        $redisObject = new \Redis();
        $result = $redisObject->pconnect($config['host'], $config['port'], $config['timeout']);
        if ('' != $config['password']) {
            $redisObject->auth($config['password']);
        }

        if (0 != $config['select']) {
            $redisObject->select($config['select']);
        }

        return $redisObject;
    }
}