<?php
/**
 * Created by PhpStorm.
 * User: MyPC
 * Date: 2017/11/23
 * Time: 19:58
 */

namespace app\wx\service;


class Verification
{
    public static function checkSignature($signature,$timestamp,$nonce,$echostr){
//        return $signature." ".$timestamp." ".$nonce;
        $result=checkSignature($signature,$timestamp,$nonce);
        if($result){
            return $echostr;
        }else{
            return false;
        }
    }
}