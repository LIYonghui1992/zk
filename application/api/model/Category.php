<?php
/**
 * Created by PhpStorm.
 * User: MyPC
 * Date: 2017/11/20
 * Time: 23:50
 */

namespace app\api\model;


class Category extends BaseModel
{
    protected $hidden=['delete_time','update_time','create_time'];
    public function img(){ //这个方法名字 就是参数的键名
        return $this->belongsTo('Image','topic_img_id','id');
    }
}