<?php
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
            'Output' => "./framework/include/Output.php"
        ];
        // 判断这个类是 基本类？模型类？控制类
        if (isset($baseClass[$className])) {
            require $baseClass[$className];
        } else if (substr($className, -5) == "Model") {
            require "./application/api/model/" . $className . ".php";
        } else if (substr($className, -10) == "Controller") {
            require "./application/api/controller/" . $className . ".php";
        } else {
            die("该文件" . $className . "没有找到");
        }
    }

    private function registerAutoLoad() {
        spl_autoload_register([$this, 'userAutoLoad']);
    }

    private function getRequestParams() {

        // echo "<pre>";
        // print_r($_SERVER);
        // print_r(parse_url($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']));
        // 当前的模块
        $defPlate = $GLOBALS['config']['app']['default_platform'];
        $p  = isset($_GET['p']) ? $_GET['p'] : $defPlate;
        define("PLATFORM", $p);
        // 当前控制器
        $defController = $GLOBALS['config'][PLATFORM]['default_controller'];
        $c  = isset($_GET['c']) ? $_GET['c'] : $defController;
        define("CONTROLLER", $c);

        // 当前的方法
        $defAction = $GLOBALS['config'][PLATFORM]['default_action'];
        $a  = isset($_GET['a']) ? $_GET['a'] : $defAction;
        define("ACTION", $a);

    }


    private function dispatch() {
        $controllerName = CONTROLLER."Controller";
        $actionName = ACTION;
        // echo $actionName;
        $controller = new $controllerName;
        $controller->$actionName();
    }

 }