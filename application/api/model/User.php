<?php
/**
 * Created by PhpStorm.
 * User: MyPC
 * Date: 2017/11/21
 * Time: 19:34
 */

namespace app\api\model;


class User extends BaseModel
{
    public static function getByOpenID($openid){
        $user=self::where('openid','=',$openid)->find();
        return $user;
    }
}