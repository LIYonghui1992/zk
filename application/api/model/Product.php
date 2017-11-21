<?php
/**
 * Created by PhpStorm.
 * User: MyPC
 * Date: 2017/11/19
 * Time: 12:58
 */

namespace app\api\model;


class Product extends BaseModel
{
    protected $hidden=[
        'delete_time', 'main_img_id', 'pivot', 'from', 'category_id',
        'create_time', 'update_time'
    ];

    public function getMainImgUrlAttr($value,$data){
        return $this->prefixImgUrl($value,$data);
    }

    public static function getMostRecent($count){
        $products=self::limit($count)->order('create_time desc')->select();
        return $products;
    }

    public static function getProductsByCategoryID($categoryID){
        $products=self::where('category_id','=',$categoryID)->select();
        return $products;
    }
}