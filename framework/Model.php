<?php
/*
 * @Description: 
 * @version: 
 * @Author: SunDuncan
 * @Date: 2021-09-25 16:42:24
 * @LastEditors: SunDuncan
 * @LastEditTime: 2021-10-29 00:01:18
 */
/**
 * Author: SunDuncan
 */
/**
 * 公共模型类
 * 完成数据库连接和一些公共方法
 */
class Model {
    private $dB = null;
    protected $tableName = null;
    private $where = array();
    private $field = "*";
    private $limit = "";
    private $order = "";
    private $like = "";
    public function __construct()
    {
        $dbConfig = $GLOBALS['config']['db'];
        // $this->tableName = str_replace("Model", "", __CLASS__);
        $this->init($dbConfig); // 完成数据库连接
    }

    private function init($dbConfig = []) {
        $this->dB = Db::getInstance($dbConfig);
    }

    public function selectAll() {
        $sql = "SELECT *from {$this->tableName}";
        return $this->dB->fetchAll($sql);
    }

    /**
     * 链式的封装 where
     */
    public function where($where = []) {
        if ($where) {
            $this->where = $where;
        }
        return $this;
    }
    /**
     * limit
     */

     public function limit($limit = "") {
        if ($limit) {
            $this->limit = $limit;
        }

        return $this;
     }
     /**
      * order
      */
    public function order($order = "") {
        if ($order) {
            $this->order = $order;
        }

        return $this;
    }

    /**
     * 封装一下 
     */
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

    public function save($data) {
        if (!is_array($data)) {
            return false;
        }

        if (count($data) < 1) {
            return false;
        }

        return $this->dB->save($this->tableName, $data, $this->where);
    }


    public function delete() {
        return $this->dB->delete($this->tableName, $this->where);
    }

    public function startTransaction() {
        $this->dB->startTransaction();
    }

    public function commit() {
        $this->dB->commit();
    }

    public function rollback() {
        $this->dB->rollback();
    }
}

