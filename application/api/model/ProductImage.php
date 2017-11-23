<?php
/**
 * Created by PhpStorm.
 * User: MyPC
 * Date: 2017/11/22
 * Time: 20:35
 */

namespace app\api\model;


class ProductImage extends BaseModel
{
    protected $hidden=['img_id','delete_time','product_id'];
    public function imgUrl(){
        return $this->belongsTo('Image','img_id','id');
    }
}