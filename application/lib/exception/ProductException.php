<?php
/**
 * Created by PhpStorm.
 * User: MyPC
 * Date: 2017/11/20
 * Time: 21:20
 */

namespace app\lib\exception;


class ProductException extends BaseException
{
    public $code = 404;
    public $msg="指定的商品不存在，请检查参数";
    public $errorCode=20000;
}