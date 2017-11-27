<?php
/**
 * Created by PhpStorm.
 * User: MyPC
 * Date: 2017/11/26
 * Time: 14:10
 */

namespace app\wx\model;


class Weather extends BaseModel
{
    public static function getCityCodeByCityName($carr){
//        $str="";
//        foreach ($carr as $v){
//            $str.="'".$v."',";
//        }
//        //去掉最右边的撇
//        $mulcity=rtrim($str,",");
        $mulcity=implode(",",$carr);
        $where['cityName']=array("in",$mulcity);
        $citycode_arr=self::where("cityName","in",$mulcity)->column('cityCode');
        return $citycode_arr;
    }
}