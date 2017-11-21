<?php
/**
 * Created by 七月.
 * Author: 七月
 * Date: 2017/4/18
 * Time: 3:11
 */

namespace app\api\validate;


use think\Validate;
use app\api\validate\BaseValidate;
class IDMustBePostiveInt extends BaseValidate
{
    protected $rule = [
        'id' => 'require|isPositiveInteger'
    ];
    protected  $message=[
        'id'=>'id必须为正整数'
    ];
}