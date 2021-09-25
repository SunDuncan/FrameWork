<?php
/**
 * Author: SunDuncan
 */
/**
 * 公共模型类
 * 完成数据库连接和一些公共方法
 */
class Model {
    protected $dB = null;
    protected $tableName = null;
    public function __construct()
    {
        $dbConfig = $GLOBALS['config']['db'];
        $this->init($dbConfig); // 完成数据库连接
    }

    private function init($dbConfig = []) {
        $this->dB = Db::getInstance($dbConfig);
    }

    public function getAll() {
        $sql = "SELECT *from {$this->tableName}";
        return $this->dB->fetchAll($sql);
    }

    public function get($id) {
        $sql = "SELECT *from {$this->tableName} where id = {$id}";
        return $this->dB->fetch($sql);
    }
}

