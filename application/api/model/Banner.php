<?php
/**
 * Created by PhpStorm.
 * User: lyh
 * Date: 2017/11/8
 * Time: 14:23
 */

namespace app\api\model;


use think\Db;
use think\Exception;

class Banner
{
    public static function getBannerByID($id){
        //TODO:根据Banner ID号获取Banner信息
        //1. 原生sql语句访问数据库
        $result=Db::query("select * from banner_item where banner_id=?",[$id]);
//        $where['id']=$id;
//        $result=Db::name('banner')->where($where)->select();
        return $result;
    }
}