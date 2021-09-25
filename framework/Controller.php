<?php
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
 }