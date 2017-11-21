<?php
/**
 * Created by PhpStorm.
 * User: MyPC
 * Date: 2017/11/20
 * Time: 20:54
 */

namespace app\api\validate;


class Count extends BaseValidate
{
    //要有数量限制
    protected $rule=[
        'count'=>'isPositiveInteger|between:1,15'
    ];


}