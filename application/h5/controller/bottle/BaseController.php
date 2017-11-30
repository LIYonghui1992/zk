<?php
/**
 * Created by PhpStorm.
 * User: MyPC
 * Date: 2017/11/30
 * Time: 21:22
 */

namespace app\h5\controller\bottle;


use think\Controller;
use think\Request;

$request=Request::instance();
define('MODULE_NAME', $request->module());//例如 http://www.lvseguoshu.cn
define('CONTROLLER_NAME', $request->controller());//例如 http://www.lvseguoshu.cn
define('ACTION_NAME', $request->action());//例如 http://www.lvseguoshu.cn
class BaseController extends Controller
{
    
}