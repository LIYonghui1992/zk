<?php
/**
 * Created by PhpStorm.
 * User: MyPC
 * Date: 2017/11/19
 * Time: 23:50
 */

namespace app\lib\exception;


class ThemeException extends BaseException
{
    public $code = 404;
    public $msg="指定主题不存在，请检查主题ID";
    public $errorCode=30000;
}