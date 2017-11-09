<?php
/**
 * Created by PhpStorm.
 * User: lyh
 * Date: 2017/11/8
 * Time: 15:26
 */

namespace app\lib\exception;




use think\Exception;

class BaseException extends Exception
{
    // HTTP状态码 404,200
    public $code =400;
    //错误具体信息
    public $msg = '参数错误';
    //自定义的错误码
    public $errorCode = 10000;
}