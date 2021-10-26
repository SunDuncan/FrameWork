<?php
/*
 * @Description: 
 * @version: 
 * @Author: SunDuncan
 * @Date: 2021-09-25 16:42:24
 * @LastEditors: SunDuncan
 * @LastEditTime: 2021-10-26 18:39:48
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

    public function find($where = [], $field = "*",  $limit = "", $order = "", $like = []) {
        if ($where) {
            $sql = "SELECT {$field} from {$this->tableName}";
            $where_count = 0;
            foreach($where as $k => $v) {
                if ($where_count == 0) {
                    $sql .= " where {$k} = '{$v}'";   
                }

                if ($where_count != 0) {
                    $sql .= " and {$k} = '{$v}'";   
                }
                $where_count++;
            }
        } 
        return $this->dB->fetch($sql);
    }

    public function add($data) {
        if (!is_array($data)) {
            return false;
        }

        if (count($data) < 1) {
            return false;
        }
    
        return $this->dB->add($this->tableName, $data);
    }

    public function save($data, $where) {
        if (!is_array($data)) {
            return false;
        }

        if (count($data) < 1) {
            return false;
        }

        $count = 0;
        $sql = "UPDATE {$this->tableName}";
        foreach ($data as $k => $value) {
            if ($count == 0) {
                $sql .= " set `{$k}` = '{$value}'";
            } 

            if ($count > 0) {
                $sql .= ",`{$k}` = '{$value}'";
            }

            $count++;
        }
        
        $coun = 0;
        foreach ($where as $kk => $vv) {
            if ($coun == 0){
                $sql .= " where `{$kk}` = '{$vv}'";
            }

            if ($count > 0) {
                $sql .= "and `{$kk}` = '{$vv}'";
            }

            $coun++;
        }
        return $this->dB->save($sql);
    }
}

