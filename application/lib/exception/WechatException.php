<?php
/**
 * Created by PhpStorm.
 * User: MyPC
 * Date: 2017/11/21
 * Time: 20:36
 */

namespace app\lib\exception;


class WechatException extends BaseException
{
    public $code = 400;
    public $msg="微信服务器接口调用失败";
    public $errorCode=999;
}