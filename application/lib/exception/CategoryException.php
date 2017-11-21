<?php
/**
 * Created by PhpStorm.
 * User: MyPC
 * Date: 2017/11/20
 * Time: 23:58
 */

namespace app\lib\exception;


class CategoryException extends BaseException
{
    public $code = 404;
    public $msg="指定类目不存在，请检查参数";
    public $errorCode=50000;
}