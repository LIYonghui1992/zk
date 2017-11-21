<?php
/**
 * Created by PhpStorm.
 * User: MyPC
 * Date: 2017/11/21
 * Time: 19:26
 */

namespace app\api\validate;


class TokenGet extends BaseValidate
{
    protected $rule=[
        'code'=>'require|isNotEmpty'
    ];

    protected $message=[
        'code'=>'没有code还想获取Token'
    ];
}