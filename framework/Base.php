<?php
/*
 * @Description: 
 * @version: 
 * @Author: SunDuncan
 * @Date: 2021-09-25 13:30:33
 * @LastEditors: SunDuncan
 * @LastEditTime: 2021-10-19 15:41:04
 */
/**
 * Author: SunDuncan
 */
/**
 * 1. 读取配置
 * 2. 自动加载类
 * 3. 请求分发
 */

 class Base {
     // 创建的run方法，完成框架的所有功能
     public function run() {
         // 记载配置
         $this->loadConfig();

        //  // 注册自动加载
         $this->registerAutoLoad();

        //  // 获取请求参数
         $this->getRequestParams();

        //  // 请求分发
         $this->dispatch();
     }

     private function loadConfig() {
         $GLOBALS['config'] = require "./application/config/config.php";
     }

    // 创建用户自定义类的加载类
    public function userAutoLoad($className) {
        $baseClass = [
            'Model' => "./framework/Model.php",
            'Db' => "./framework/Db.php",
            'Controller' => "./framework/Controller.php",
            'Output' => "./framework/include/Output.php",
            'Utils' => "./framework/include/Utils.php",
            'WechatApi' => './framework/include/WechatApi.php'
        ];

        $platForm = PLATFORM;
        // 判断这个类是 基本类？模型类？控制类
        if (isset($baseClass[$className])) {
            require $baseClass[$className];
        } else if (substr($className, -5) == "Model") {
            require "./application/{$platForm}/model/" . $className . ".php";
        } else if (substr($className, -10) == "Controller") {
            require "./application/{$platForm}/controller/" . $className . ".php";
        } else {
            die("该文件" . $className . "没有找到");
        }

        
    }

    private function registerAutoLoad() {
        spl_autoload_register([$this, 'userAutoLoad']);
    }

    private function getRequestParams() {

        // 这边来解析路由
        if (isset($_SERVER['PATH_INFO']) && $_SERVER['PATH_INFO']) {
            $path_info_array = explode("/", $_SERVER['PATH_INFO']);
            if (count($path_info_array) < 3) {
                die("没找到合适的路由模块");
            }
            /**
             * 需要去查看是否有这个
             */

            define("PLATFORM", $path_info_array[1]);
            define("CONTROLLER", $path_info_array[2]);
            define("ACTION", $path_info_array[3]);
        } else {
            $defModule = $GLOBALS['config']['app']['default_module'];
            $s = isset($_GET['s']) ? $_GET['s'] : $defModule;
            $base_url_array = explode("/", $s);
            if ($base_url_array[0]) {
                define("PLATFORM", $base_url_array[0]);
                // 当前控制器
                define("CONTROLLER", $base_url_array[1]);
                // 当前的方法
                define("ACTION", $base_url_array[2]);
            }

            if ($base_url_array[1] == "index.php") {
                define("PLATFORM", $base_url_array[2]);
                // 当前控制器
                define("CONTROLLER", $base_url_array[3]);
                // 当前的方法
                define("ACTION", $base_url_array[4]);
            }

            if ($base_url_array[1] != "index.php") {
                define("PLATFORM", $base_url_array[1]);
                // 当前控制器
                define("CONTROLLER", $base_url_array[2]);
                // 当前的方法
                define("ACTION", $base_url_array[3]);
            }
        }
    }


    private function dispatch() {
        $controllerName = CONTROLLER."Controller";
        $actionName = ACTION;
        // echo $actionName;
        $controller = new $controllerName;
        $controller->$actionName();
    }

 }