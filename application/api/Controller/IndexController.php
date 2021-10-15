<?php
/*
 * @Description: 
 * @version: 
 * @Author: SunDuncan
 * @Date: 2021-09-25 16:14:42
 * @LastEditors: SunDuncan
 * @LastEditTime: 2021-10-15 17:49:27
 */

class IndexController extends Controller{
    public function index() {
        // $indexModel = new IndexModel();
        // $info = $indexModel->getAll();
        $this->output("欢迎使用Duncan的首个内测框架");
    }
}