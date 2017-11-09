<?php
/**
 * Created by PhpStorm.
 * User: lyh
 * Date: 2017/11/8
 * Time: 15:29
 */

namespace app\lib\exception;


class BannerMissException extends BaseException
{
    public $code = 404;
    public $msg = '请求Banner不存在';
    public $errorCode = 40000;
}