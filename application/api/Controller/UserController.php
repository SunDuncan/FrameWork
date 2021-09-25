<?php

class UserController extends Controller{
    public function index() {
        $indexModel = new UserModel();
        $info = $indexModel->getAll();
        $this->output($info);
    }
}