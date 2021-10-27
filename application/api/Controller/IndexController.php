<?php
/*
 * @Description: 
 * @version: 
 * @Author: SunDuncan
 * @Date: 2021-09-25 16:14:42
 * @LastEditors: SunDuncan
 * @LastEditTime: 2021-10-27 17:39:47
 */

class IndexController extends Controller{
    public function index() {
        echo "欢迎使用Duncan的首个内测框架";
    }

    public function addUser() {
        $userModel = new UserModel();
        $res = $userModel->where(['id' => 1])->delete();
        echo $res;
    }
}