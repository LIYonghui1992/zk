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
        'id' => 'require|isPositiveInteger',
        'num'=>'in:1,2,3'
    ];

    protected function isPositiveInteger($value, $rule = '',
        $data = '', $field = '')
    {
        if (is_numeric($value) && is_int($value + 0) && ($value + 0) > 0) {
            return true;
        }
        else{
            return $field.'必须是正整数';
        }
    }
}