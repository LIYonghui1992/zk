<?php
/**
 * Created by PhpStorm.
 * User: MyPC
 * Date: 2017/11/18
 * Time: 16:32
 */

namespace app\api\model;

use think\Model;
use think\Db;
class BannerItem extends BaseModel
{
    protected $hidden=['id','update_time','delete_time','img_id','banner_id'];
    public function img(){
        //关联模型的外键 不一定在关联模型中
        return $this->belongsTo('Image','img_id','id');
    }
}