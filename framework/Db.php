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
      } catch(PDOException $e) {
          die("数据库连接失败" . $e->getMessage());
      }
    }


    // 写操作
    /**
     * 新增，更新，删除
     */
    protected function exec($sql) {
        $num = $this->conn->exec($sql);
        if ($num > 0) {
            // 如果是新增的操作
            if (null != $this->conn->lastInsertId()) {
                $this->insertId = $this->conn->lastInsertId();
            }

            $this->num = $num;
        } else {
                $error = $this->conn->errorInfo(); // [0] 错误标识符 [1]错误代码 [2]错误信息
                echo json_encode([
                    'msg' => '数据库内部错误'
                ]);
                exit;
                // var_dump($error);
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
    public function add($sql) {
        $this->exec($sql);
        return $this->insertId;
    }

    public function save($sql) {
        $this->exec($sql);
        return $this->num;
    }
}