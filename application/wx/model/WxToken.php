<?php
/**
 * Created by PhpStorm.
 * User: lyh
 * Date: 2017/11/29
 * Time: 15:08
 */

namespace app\wx\model;


use app\wx\model\BaseModel;

class WxToken extends BaseModel
{
    //根据openid从wx_token表中获取ccess_token 判断create_time 到现在是否超过了30天 超过了重新授权，不然则判断update_time距离现在是否超过了7000秒，超过了则基于refresh_token重新获取access_token
    public static function getTokenByOpenId($openid){
        $where['openid']=$openid;
        $result=self::where($where)->find();
        return $result;
    }

}