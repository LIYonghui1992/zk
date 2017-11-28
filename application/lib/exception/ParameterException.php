<?php
/**
 * Created by PhpStorm.
 * User: MyPC
 * Date: 2017/11/9
 * Time: 19:44
 */

namespace app\lib\exception;

use app\lib\exception\BaseException;
class ParameterException extends BaseException
{
    public $code=400;
    public $msg='参数错误';
    public $errorCode= 10000;
}