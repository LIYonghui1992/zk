<?php
/**
 * Created by PhpStorm.
 * User: lyh
 * Date: 2017/11/8
 * Time: 14:23
 */

namespace app\api\model;


use think\Model;
use think\Db;
use think\Exception;

class Banner extends BaseModel
{
    protected $hidden=['update_time','delete_time'];
//    protected $table='';
//原本通过DB访问数据库的，现在用模型关系来拿取数据
    public static function getBannerByID($id){
        //TODO:根据Banner ID号获取Banner信息
        //1. 原生sql语句访问数据库
//        $result=Db::query("select * from banner_item where banner_id=?",[$id]);
//        $where['id']=$id;
//        $result=Db::name('banner')->where($where)->select();
//        $result=Db::table('banner_item')->where('banner_id','=',$id)->find();
        $banner=self::with(['items','items.img'])->find($id);
        return $banner;
    }

    public function items(){
        //BannerItem 关联模型的模型名字
        //banner_id 关联模型外键
        //id 当前模型主键
        //关联模型指的是BannerItem 当前模型指的是Banner
        return $this->hasMany('BannerItem','banner_id','id');
    }
}