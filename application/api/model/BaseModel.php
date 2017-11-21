<?php
/**
 * Created by PhpStorm.
 * User: MyPC
 * Date: 2017/11/19
 * Time: 10:41
 */

namespace app\api\model;


use think\Model;

class BaseModel extends Model
{
    protected function prefixImgUrl($value,$data){
        if($data['from']==1){
            return config('setting.img_prefix').$value;
        }
        return $value;

    }
}