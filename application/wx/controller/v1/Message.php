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
use app\wx\service\Message as MessageService;
use think\Exception;
use think\Request;
use think\Log;

class Message
{

    public function receiveMsg(){
//        $str=<<<hello
//<xml>
// <ToUserName><![CDATA[toUser]]></ToUserName>
// <FromUserName><![CDATA[fromUser]]></FromUserName>
// <CreateTime>1348831860</CreateTime>
// <MsgType><![CDATA[text]]></MsgType>
// <Content><![CDATA[this is a test]]></Content>
// <MsgId>1234567890123456</MsgId>
// </xml>
//hello;
//        //将xml转换为对象格式
//        $str_obj=simplexml_load_string($str,'SimpleXMLElement',LIBXML_NOCDATA);
////        echo $str."<br>";
////        echo json_encode($str_obj);
//        echo $str_obj->ToUserName;
//        exit;
        $request=Request::instance();
//        error_log(json_encode($request->param()));
        Log::record("Receive".json_encode($request->param()));
        $wechatObj = new MessageService();
        if(!isset($_GET['echostr'])){
//            error_log("good");
            $message=$wechatObj->responseMsg();
            if(empty($message)){
                echo "";
            }else{
                echo $message;
            }
        }else{
            $result=$wechatObj->valid();
            if($result){
                echo $result;
            }else{
                echo "";
            }
        }
    }
}