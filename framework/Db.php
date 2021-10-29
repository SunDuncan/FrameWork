<?php
/**
 * 数据库的基本操作
 * Author: SunDuncan
 * create: 2021-9-25
 */
class Db{
    // 私有属性数据库的连接配置
    private $dbConfig = null;

    // 单例模式，本类的实例
    private static $instance = null;

    // 数据库的连接
    private $conn = null;

    // 新增的id
    public $insertId = null;

    // 受影响的数量
    public $num = 0;

    // 构造函数--1. 初始化连接参数 2. 连接参数
    private function __construct($param = [])
    {
        $this->dbConfig = array(
            //  用户配置不同的db参数可在nginx中进行配置,直接获取,这样就可以高可用
             "db" => 'mysql', // 数据库的类型
             "host" => getenv("DB_HOST") ? getenv("DB_HOST") : "localhost", // 默认的主机
             "user" => getenv("DB_USER") ? getenv("DB_USER") : "root", // 默认的用户名
             "pass" => getenv("DB_PWD") ? getenv("DB_PWD") : "root", // 默认的密码
             "port" => getenv("DB_PORT") ? getenv("DB_PORT") : "8889", // 默认端口号，一般的为3306
             "charset" => "utf8", // 默认的字符集
             "dbname" => "local_user" // 默认数据库
        );
        if ($param) {
            $this->dbConfig = array_merge($this->dbConfig, $param);
        }
        $this->connect();
    }

    // 创造单例
    public static function getInstance($param = []) {
        if (!self::$instance instanceof self) {
            self::$instance = new self($param);
        }

        return self::$instance;
    }


    // 禁止其他实例，通过克隆的方式
    private function __clone()
    {
        
    }

    // 编写连接
    private function connect() {
      try {
          // 配置dsn
          $dsn = "{$this->dbConfig['db']}:host={$this->dbConfig['host']};port={$this->dbConfig['port']};
          dbname={$this->dbConfig['dbname']};charset={$this->dbConfig['charset']}";
          $this->conn = new PDO($dsn, $this->dbConfig['user'], $this->dbConfig['pass']);

          // 设置客户端的字符集
          $this->conn->query("SET NAMES {$this->dbConfig['charset']}");
          
          // 设置默认的查询返回的类型
          $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
          $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
      } catch(PDOException $e) {
          die("数据库连接失败" . $e->getMessage());
      }
    }


    // 写操作
    /**
     * 生成insert的Sql
     */
    private function createInsertSql($tableName, $data) {
        $execData = array();
        $sql = "INSERT INTO {$tableName}";
        $count = 0;
        $params = "";
        $values = "";
        foreach($data as $k => $value) {
            if ($count == 0) {
                $params .= "`{$k}`";
                $values = ":{$k}";
                $execData[":{$k}"] = $value;
            } else {
                $params .= ",`{$k}`";
                $values .= ",:{$k}";
                $execData[":{$k}"] = $value;
            }
            $count++;
        }
       
        $sql .= "({$params}) VALUES ({$values})";
        return [
            'sql' => $sql,
            'execData' => $execData
        ];
    }

    private function createUpdateSql($tableName, $data, $where) {
        $sql = "UPDATE {$tableName} set ";
        $execData = array();
        $params = "";
        $wheres = "";
        $count = 0;
        foreach($data as $k => $value) {
            if ($count == 0 ){
                $params .= "`{$k}` = :{$k}";
                $execData[":{$k}"]= $value;
            } else {
                $params .= ",`{$k}` = :{$k}";
                $execData[":{$k}"]= $value;
            }
            $count++;
        }
        $count = 0;
        foreach($where as $kk => $vv) {
            if ($count == 0 ){
                $wheres .= "`{$kk}` = :where{$kk}";
                $execData[":where{$kk}"]= $vv;
            } else {
                $wheres .= "and `{$kk}` = :where{$kk}";
                $execData[":where{$kk}"]= $vv;
            }
            $count++;
        }

        if (!$wheres) {
            $sql .= $params . " where " . $wheres;
        } else {
            $sql .= $params;
        }
        return [
            'sql' => $sql,
            'execData' => $execData
        ];
    }

    private function createDeleteSql($tableName, $where) {
        $count = 0;
        $wheres = "";
        $sql = "DELETE FROM {$tableName} ";
        foreach($where as $kk => $vv) {
            if ($count == 0 ){
                $wheres .= "`{$kk}` = :where{$kk}";
                $execData[":where{$kk}"]= $vv;
            } else {
                $wheres .= "and `{$kk}` = :where{$kk}";
                $execData[":where{$kk}"]= $vv;
            }
            $count++;
        }
        if ($wheres) {
            $sql .= " where " . $wheres;
        }
        return [
            'sql' => $sql,
            'execData' => $execData
        ];
    }
    /**
     * 增删改的操作进行prep
     */
    /**
     * 目前支持and的查询，其他可能待完善
     */
    protected function exec($tableName, $data, $type, $where = []) {
        try {
            $execData = array();
            if ($type == "insert") {
                $result = $this->createInsertSql($tableName, $data);
                $sql = $result['sql'];
                $execData = $result['execData'];
            }
    
            if ($type == "update") {
                $result = $this->createUpdateSql($tableName, $data, $where);
                $sql = $result['sql'];
                $execData = $result['execData'];
            }
    
            if ($type == "delete") {
                $result = $this->createDeleteSql($tableName, $where);
                $sql = $result['sql'];
                $execData = $result['execData'];
            }
           
            $stmt = $this->conn->prepare($sql);
            $res = $stmt->execute($execData);
            if ($res > 0) {
                if (null != $this->conn->lastInsertId()) {
                    $this->insertId = $this->conn->lastInsertId();
                }
                $this->num = $stmt->rowCount();
            } else {
                echo json_encode([
                    'msg' => "该操作没有改变数据"
                ]);
                exit;
            }
        } catch(PDOException $e) {
            echo json_encode([
                'msg' => $e->getMessage()
            ]);
            exit;
        }
    }

    // 查询
    /**
     * 单条查询
     */
    public function fetch($sql){
        return $this->conn->query($sql)->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * 多条数据
     */
    public function fetchAll($sql) {
        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * 封装一下插入的操作
     */
    public function add($tableName, $data) {
        $this->exec($tableName, $data, "insert");
        return $this->insertId;
    }

    public function save($tableName, $data, $where = []) {
        $this->exec($tableName, $data, "update", $where);
        return $this->num;
    }

    public function delete($tableName, $where = []) {
        $this->exec($tableName, [], "delete", $where);
        return $this->num;
    }

    /**
     * 开启事务
     */
    public function startTransaction() {
        $this->conn->beginTransaction();
    }

    /**
     * 提交事务
     */
    public function commit() {
        $this->conn->commit();
    }

    /**
     * 提交回滚
     */
    public function rollback() {
        $this->conn->rollBack();
    }
}