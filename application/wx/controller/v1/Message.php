<?php
/**
 * Created by PhpStorm.
 * User: MyPC
 * Date: 2017/11/23
 * Time: 21:35
 */

namespace app\wx\controller\v1;

use app\lib\exception\BaseException;
use app\lib\exception\ParameterException;
use app\wx\service\wechatCallbackapiTest;
use think\Exception;
use think\Request;

class Message
{
    public function receiveMsg($signature,$timestamp,$nonce,$echostr){
        error_log("==========================".$signature." ".$timestamp." ".$nonce." ".$echostr."============================================");
//        exit;
//        $signature='87dca0f49b1a6d593c3e1ad1b0bcfa42d22c29e2';
//        $timestamp='1511452409';
//        $nonce='4135056283';
//        $echostr='10848167547209514751';
        $wechatObj = new wechatCallbackapiTest();
        if(!isset($_GET['echostr'])){
            $wechatObj->responseMsg();
        }else{
            $wechatObj->valid();
        }
//        $result=checkSignature($signature,$timestamp,$nonce);
//        error_log("============".$result."================");
//        if($result){
//            header('content-type:text');
//            echo $echostr;
//            exit;
//        }else{
//            throw new Exception($signature." ".$timestamp." ".$nonce." ".$echostr);
//        }
    }
//    public function receiveMsg(){
//        $request=Request::instance();
////        $request->param();
//        echo "success";
//        error_log(json_encode($request->param()));
//    }
}