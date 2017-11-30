<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]
// 定义应用目录
define('APP_PATH', __DIR__ . '/../application/');
define('LOG_PATH', __DIR__ . '/../log/');//修改log目录
define('WEB_URL', 'http://'.$_SERVER['HTTP_HOST']);//例如 http://www.lvseguoshu.cn

// 加载框架引导文件
require __DIR__ . '/../thinkphp/start.php';
//如果config中关闭了log日志 则自己记录日志需要初始化，这里统一初始化 全局初始化又和开启type=File 一样了
//\think\Log::init([
//    'type'=>'File',
//    'path'=>LOG_PATH,
//    'level'=>[]
//]);