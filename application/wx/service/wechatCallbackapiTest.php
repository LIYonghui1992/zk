<?php
/**
 * Created by PhpStorm.
 * User: MyPC
 * Date: 2017/11/24
 * Time: 0:21
 */

namespace app\wx\service;


class wechatCallbackapiTest
{
    public function valid()
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature()){
            echo $echoStr;
            exit;
        }
    }

    public function responseMsg()
    {
        //get post data, May be due to the different environments
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

        //extract post data
        if (!empty($postStr)){
            //解析数据
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            //发送消息方ID
            $fromUsername = $postObj->FromUserName;
            //接收消息方ID
            $toUsername = $postObj->ToUserName;
            //消息类型
            $form_MsgType = $postObj->MsgType;
            $keyword = trim($postObj->Content);
            $time = time();
            $textTpl = "<xml>
                            <ToUserName><![CDATA[%s]]></ToUserName>
                            <FromUserName><![CDATA[%s]]></FromUserName>
                            <CreateTime>%s</CreateTime>
                            <MsgType><![CDATA[%s]]></MsgType>
                            <Content><![CDATA[%s]]></Content>
                            <FuncFlag>0</FuncFlag>
                            </xml>";
            $newsTpl = "<xml>  
                           <ToUserName><![CDATA[%s]]></ToUserName>  
                           <FromUserName><![CDATA[%s]]></FromUserName>  
                           <CreateTime>%s</CreateTime>  
                           <MsgType><![CDATA[%s]]></MsgType>  
                           <ArticleCount>%s</ArticleCount>  
                           <Articles>  
                           <item>  
                           <Title><![CDATA[%s]]></Title>   
                           <Description><![CDATA[%s]]></Description>  
                           <PicUrl><![CDATA[%s]]></PicUrl>  
                           <Url><![CDATA[%s]]></Url>  
                           </item>  
                           </Articles>  
                           <FuncFlag>1</FuncFlag>  
                           </xml> ";
            $musicTpl = "<xml>  
                             <ToUserName><![CDATA[%s]]></ToUserName>  
                             <FromUserName><![CDATA[%s]]></FromUserName>  
                             <CreateTime>%s</CreateTime>  
                             <MsgType><![CDATA[%s]]></MsgType>  
                             <Music>  
                             <Title><![CDATA[%s]]></Title>  
                             <Description><![CDATA[%s]]></Description>  
                             <MusicUrl><![CDATA[%s]]></MusicUrl>  
                             <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>  
                             </Music>  
                             <FuncFlag>0</FuncFlag>  
                             </xml>";
            //事件消息
            if($form_MsgType=="event")
            {
                //获取事件类型
                $form_Event = $postObj->Event;
                //订阅事件
                if($form_Event=="subscribe")
                {
                    //回复欢迎文字消息
                    $msgType = "text";
                    $contentStr = "感谢您关注Leo的微信公众号[玫瑰]";
                    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, time(), $msgType, $contentStr);
                    echo $resultStr;
                    exit;
                }
            }
            //如果用户发送内容不为空，回复“谢谢您的回复!”
            if(!empty( $keyword ))
            {
                $msgType = "text";
                $contentStr = "谢谢您的回复!";
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                echo $resultStr;
            }else{
                echo "Input something...";
            }

        }else {
            echo "";
            exit;
        }
    }

    private function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = config('wx.token');
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }
}