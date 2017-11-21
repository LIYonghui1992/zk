<?php
/**
 * Created by PhpStorm.
 * User: MyPC
 * Date: 2017/11/19
 * Time: 14:30
 */

namespace app\api\validate;

use app\api\validate\BaseValidate;
class IDCollection extends BaseValidate
{
    protected $rule=[
        'ids'=>'require|checkIDs'
    ];
    protected $message =[
        'ids'=>'ids参数必须是以逗号分隔的多个正整数'
    ];
    //$value 为 id1,id2,id3
    protected function checkIDs($value){
        $values=explode(',',$value);
        if(empty($values)){
            return false;
        }
        foreach ($values as $id){
            if(!$this->isPositiveInteger($id)){
                return false;
            }
        }
        return true;
    }
}