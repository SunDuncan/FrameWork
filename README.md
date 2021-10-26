<!--
 * @Description: 
 * @version: 
 * @Author: SunDuncan
 * @Date: 2021-09-25 18:46:59
 * @LastEditors: SunDuncan
 * @LastEditTime: 2021-10-15 17:51:27
-->
# 后台框架
## 序言
> 目前本框架仍属于开发阶段，基础架构已经搭好，目前只开发了接口的部分，关于namespace的规范，sql方法的仍需要完善，前端的view层准备采用前后端的分离，还有一些其他模块的封装。



## 目录结构

```
teownFramework
├── README.md
├── application // 业务逻辑目录
│   ├── api     // 接口目录
│   │   ├── Controller // 控制器
│   │   │   ├── IndexController.php //默认控制器
│   │   │   └── UserController.php 
│   │   └── Model
│   │       ├── IndexModel.php // 默认模型
│   │       └── UserModel.php 
│   └── config
│       └── config.php  // 公共配置
├── framework // 框架核心
│   ├── Base.php // 基类，自动加载，请求分发
│   ├── Controller.php // 集成控制类
│   ├── Db.php // 单例数据库
│   ├── Model.php // 集成模型
│   └── include // 公共工具
│       └── Output.php // 输出工具
├── index.php // 框架入口文件
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


**TO BE CONTINUE**
