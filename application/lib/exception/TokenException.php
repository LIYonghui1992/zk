<?php
/**
 * Created by PhpStorm.
 * User: MyPC
 * Date: 2017/11/21
 * Time: 23:14
 */

namespace app\lib\exception;


class TokenException extends BaseException
{
    public $code = 401;
    public $msg="Token已过期或无效Token";
    public $errorCode=10001;
}