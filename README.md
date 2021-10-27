<!--
 * @Description: 
 * @version: 
 * @Author: SunDuncan
 * @Date: 2021-09-25 18:46:59
 * @LastEditors: SunDuncan
 * @LastEditTime: 2021-10-27 18:29:07
-->
# 后台框架
## 序言
> 目前本框架仍属于开发阶段，基础架构已经搭好，目前只开发了接口的部分，关于namespace的规范，sql方法的仍需要完善，前端的view层准备采用前后端的分离，还有一些其他模块的封装。



## 目录结构

```
FrameWork
├── README.md
├── application    // 业务逻辑目录
│   ├── admin-ui   // 后台的页面
│   │   └── vue-element-admin
│   ├── api       // 接口目录
│   │   ├── Controller  // 控制器层
│   │   └── Model // 数据model层
│   ├── config
│   │   └── config.php  //所有引入的配置文件
│   ├── sql  // 创建必要库的sql文件
│   └── wechat // 封装的微信的一些方法
│       ├── controller
│       └── model
├── framework   //框架核心部分
│   ├── Base.php // 基类，自动加载，请求分发
│   ├── Controller.php // 集成控制类
│   ├── Db.php // 单例数据库，主要封装数据库的一些底层操作
│   ├── Model.php // 集成模型
│   ├── Rd.php  // Redis的底层封装 //这边待修改，以后也需要改成单例的模式
│   └── include // 一些集成的工具类
│       ├── Output.php // 返回的规范
│       ├── Utils.php  // 工具类
│       ├── WechatApi.php // 微信的api
│       ├── aliMessage   // 阿里短信的实体类
│       └── wxPay1   // 微信的支付的封装
└── index.php // 项目总的入口文件


```
## 使用

### 基础
1. 入口文件
> 入口文件(index.php)主要完成:
+ 定义框架的路径，项目路径
+ 定义相关的常量
+ 载入框架的入口文件

2. 开发规范
+ 关于控制器层，必须在某个模块下的Controller目录下面，且命名规范 ***Controller.php，内部框架有很多的方法在开发，建议直接继承Controller
+ 关于数据Model层，必须在某个模块下的Model目录下面，且命名规范 ***Model.php, 内部框架的很多方法在开发，建议直接继承Model
+ 关于Service层，这边建议主要的逻辑处理可以放在Service层下面，这样会减少

3. 公共文件配置
在./application/config/config.php

4. 路由访问规则
+ index.php?s=/api/index/index   其中api代表模块名，index控制器名，index方法名
+ index.php/api/index/index 其中api代表模块名，index控制器名，index方法名

## 环境搭建
### 配置
1. lamp环境，php建议7.0以上，在当前目录下需要一个.htaccess文件，文件内容
```
      Options +FollowSymlinks
      RewriteEngine On
      RewriteCond %{REQUEST_FILENAME} !-d
      RewriteCond %{REQUEST_FILENAME} !-f
      RewriteRule ^(.*)$ index.php/$1 [QSA,PT,L]
```


2. lnmp环境，主要是关于nginx的配置，需要在nginx,rewrite重写路由,下面是样例，请根据自己环境进行重新配置
```
      set $path_info "";
      set $real_script_name $fastcgi_script_name;
      if ($fastcgi_script_name ~ "^(.+?\.php)(/.+)$")
  	{
		set $real_script_name $1;
		set $path_info $2;
	}
```

## 数据库
### 模型定义
1. 定义一个User模型
```
      <?php
      class UserModel extends Model {
            protected $tableName = "user"; // 数据库的表 // 对应的就是数据库中的user表
      }
```
2. 模型调用
```
      // 目前仅支持实例化调用
      $user = new UserModel;
      $user->save();
```

### 新增
1. 添加一条数据
```
      $user           = new UserModel;
      $data           = array();
      $data['name']   = 'duncan';
      $data['age']    = 1;
      $res            = $user->add($data); // $res 为自增的id
      // 如果数据错误会抛出pdo异常
      
```
2. 批量新增
```
      // 正在待开发中
```

### 更新
1. 更新数据
```
      $user           = new UserModel;
      $data           = array();
      $data['name']   = 'duncan';
      $data['age']    = 1;
      $where['id']    = 1;
      $res            = $user->where($where)->save($data); // $res 为自增的受影响的行数 
      // 如果数据错误会抛出pdo异常

```

### 删除
```
      $user           = new UserModel;
      $where['id']    = 1;
      $res            = $user->where($where)->delete(); // $res 为自增的受影响的行数 
      // 如果数据错误会抛出pdo异常
```


**TO BE CONTINUE**
