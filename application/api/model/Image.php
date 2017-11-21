<?php
/**
 * Created by PhpStorm.
 * User: MyPC
 * Date: 2017/11/18
 * Time: 23:09
 */

namespace app\api\model;


use think\Model;
use app\api\model\BaseModel;
class Image extends BaseModel
{
    protected $hidden=['id','from','update_time','delete_time'];
    public function getUrlAttr($value,$data){
        return $this->prefixImgUrl($value,$data);
    }
}