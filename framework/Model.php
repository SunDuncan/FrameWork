<?php
/*
 * @Description: 
 * @version: 
 * @Author: SunDuncan
 * @Date: 2021-09-25 16:42:24
 * @LastEditors: SunDuncan
 * @LastEditTime: 2021-10-19 16:28:18
 */
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

    public function selectAll() {
        $sql = "SELECT *from {$this->tableName}";
        return $this->dB->fetchAll($sql);
    }

    public function find($id, $where = [], $limit = "", $order = "") {
        $sql = "SELECT *from {$this->tableName} where id = {$id}";
        return $this->dB->fetch($sql);
    }

    public function add($data) {
        if (!is_array($data)) {
            return false;
        }

        if (count($data) < 1) {
            return false;
        }

        $count = 0;
        $sql = "INSERT INTO {$this->tableName}";
        foreach ($data as $k => $value) {
            if ($count == 0) {
                $sql .= " set `{$k}` = {$value}";
            } 

            if ($count > 0) {
                $sql .= ",set `{$k}` = {$value}";
            }

            $count++;
        }
        return $this->dB->add($sql);
    }
}

