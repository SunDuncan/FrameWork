<?php
/*
 * @Description: 
 * @version: 
 * @Author: SunDuncan
 * @Date: 2021-10-26 11:56:50
 * @LastEditors: SunDuncan
 * @LastEditTime: 2021-10-26 12:50:42
 */
/**
 * Author: SunDuncan
 */
/**
 * 公共的控制类
 * 目前主要封装一些公共方法
 */

 class Controller{
    protected function output($data = null, $errId = 0) {
        $output = new Output($errId, $data);
        $output->output();
    }

    /**
     * 一些接口的签名的设置
     */
    protected function checkSign($param, $sign) {
        $secret = $GLOBALS['config']['wxosrv']['secret'];
        ksort($param);
        if (isset($param['sign'])) {
            unset($param['sign']);
        }
        $verify = "";
        $count = 0;
        foreach($param as $k => $v) {
            if (!$v || !$k) {
                unset($param[$k]);
                continue;
            }
            if ($count == 0) {
                $verify .= $k . "=" .$v;
                $count++;
            } else {
                $value = urldecode($v);
                $verify .= "&{$k}={$value}";
            }
        }

        $verify .= $secret;
        if (md5($verify) != $sign) {
            $this->output("签名错误", 1000);
        }
    }

    protected function createSign($param) {
        $secret = $GLOBALS['config']['wxosrv']['secret'];
        ksort($param);
        if (isset($param['sign'])) {
            unset($param['sign']);
        }
        $verify = "";
        $count = 0;
        foreach($param as $k => $v) {
            if (!$v || !$k) {
                unset($param[$k]);
                continue;
            }

            if ($count == 0) {
                $value = urldecode($v);
                $verify .= $k . "=" .$v;
                $count++;
            } else {
                $value = urldecode($v);
                $verify .= "&{$k}={$value}";
            }
        }
        
        $verify .= $secret;
        return md5($verify);
    }
 }