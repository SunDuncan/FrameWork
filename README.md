## 后台框架
目录结构
---
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
使用
---
1. 控制类的命名规范 *Controller
2. 模型的命名规范 *Model,

<span style="color:red;font-size: 20px;">该版本还在完善中，路由需要完善，目前的访问路由比较单一 baseUrl/index.php?a=方法名&c=控制器名字</span>
**TO BE CONTINUE**