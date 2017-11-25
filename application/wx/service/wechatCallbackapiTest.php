<?php
/**
 * Created by PhpStorm.
 * User: MyPC
 * Date: 2017/11/24
 * Time: 0:21
 */

namespace app\wx\service;
use think\Log;

class wechatCallbackapiTest
{
    public function valid()
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature()){
            return $echoStr;
//            exit;
        }else{
            return false;
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
            //记录收到的数据
            Log::record("Receive post Str".$postObj->Content);
            //发送消息方ID
            $fromUsername = $postObj->FromUserName;
            //接收消息方ID
            $toUsername = $postObj->ToUserName;
            //消息类型
            $form_MsgType = $postObj->MsgType;//text image location voice video link
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
            switch ($form_MsgType){
                case 'text':
                    //如果用户发送内容不为空，回复“谢谢您的回复!”
                    if(!empty( $keyword ))
                    {
                        $msgType = "text";
                        $contentStr = "谢谢您的回复!";
                        $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                        return $resultStr;
                    }else{
                        return "";
                    }
                    break;
                case 'image':
                    break;
                case 'location':
                    break;
                case 'voice':
                    break;
                case 'link':
                    break;
                case 'video':
                    break;
                case 'event':
                    //获取事件类型
                    $form_Event = $postObj->Event;
                    //订阅事件
                    if($form_Event=="subscribe")
                    {
                        //回复欢迎文字消息
                        $msgType = "text";
                        $contentStr = "感谢您关注Leo的微信公众号[玫瑰]";
                        $resultStr = sprintf($textTpl, $fromUsername, $toUsername, time(), $msgType, $contentStr);
                        return $resultStr;
                    }
                    break;

            }
//            if($form_MsgType=="event")
//            {
//                //获取事件类型
//                $form_Event = $postObj->Event;
//                //订阅事件
//                if($form_Event=="subscribe")
//                {
//                    //回复欢迎文字消息
//                    $msgType = "text";
//                    $contentStr = "感谢您关注Leo的微信公众号[玫瑰]";
//                    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, time(), $msgType, $contentStr);
//                    return $resultStr;
////                    exit;
//                }
//            }
//            //如果用户发送内容不为空，回复“谢谢您的回复!”
//            if(!empty( $keyword ))
//            {
//                $msgType = "text";
//                $contentStr = "谢谢您的回复!";
//                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
//                return $resultStr;
//            }else{
//                return "";
//            }

        }else {
            return "";
//            exit;
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




    public function index()
    {
        $this->show('This is for Wechat','utf-8');
    }
    //用户首次配置开发环境
    public function echoStr()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce     = $_GET["nonce"];
        $echostr   = $_GET['echostr'];
        $token     = 'skye';
        $tmpArr    = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr    = implode( $tmpArr );
        $tmpStr    = sha1( $tmpStr );
        if( $tmpStr == $signature && $echostr)
        {
            echo $echostr;
    }else{
            $this->reposeMsg();
        }
    }

    //回复消息
    public function reposeMsg()
    {
        //1.接受数据
        $postArr = $GLOBALS['HTTP_RAW_POST_DATA'];  //接受xml数据
        //2.处理消息类型,推送消息
        $postObj = simplexml_load_string( $postArr );   //将xml数据转化为对象
        if( strtolower( $postObj->MsgType ) == 'event')
        {
            //关注公众号事件
        if( strtolower( $postObj->Event ) == 'subscribe' )
        {
            $toUser    =  $postObj->FromUserName;
            $fromUser  =  $postObj->ToUserName;
            $time      =  time();
            $msgType   =  'text';
            $content   =  '你终于来啦,等你等的好辛苦啊!可尝试输入关键字:教程,Tel,wechat,1等000';
            $template  =  "<xml>  
                    <ToUserName><![CDATA[%s]]></ToUserName>  
                    <FromUserName><![CDATA[%s]]></FromUserName>  
                    <CreateTime>%s</CreateTime>  
                    <MsgType><![CDATA[%s]]></MsgType>  
                    <Content><![CDATA[%s]]></Content>  
                    </xml>";
            echo sprintf($template, $toUser, $fromUser, $time, $msgType, $content);
        }
    }

        //回复文本信息
        if( strtolower( $postObj->MsgType ) == 'text' && trim($postObj->Content)=='wechat')
        {
            $toUser = $postObj->FromUserName;
            $fromUser = $postObj->ToUserName;
            $arr = array(
                array(
                    'title'=>'test',
                    'description'=>"just so so...",
                    'picUrl'=>'http://www.acting-man.com/blog/media/2014/11/secret-.jpg',
                    'url'=>'http://www.imooc.com',
                ),
                array(
                    'title'=>'hao123',
                    'description'=>"hao123 is very cool",
                    'picUrl'=>'https://www.baidu.com/img/bdlogo.png',
                    'url'=>'http://www.hao123.com',
                ),
                array(
                    'title'=>'qq',
                    'description'=>"qq is very cool",
                    'picUrl'=>'http://www.imooc.com/static/img/common/logo.png',
                    'url'=>'http://www.qq.com',
                ),
            );
            $template = "<xml>  
                 <ToUserName><![CDATA[%s]]></ToUserName>  
                 <FromUserName><![CDATA[%s]]></FromUserName>  
                 <CreateTime>%s</CreateTime>  
                 <MsgType><![CDATA[%s]]></MsgType>  
                 <ArticleCount>".count($arr)."</ArticleCount>  
                 <Articles>";
            foreach($arr as $k=>$v){
                $template .="<item>  
                    <Title><![CDATA[".$v['title']."]]></Title>   
                    <Description><![CDATA[".$v['description']."]]></Description>  
                    <PicUrl><![CDATA[".$v['picUrl']."]]></PicUrl>  
                    <Url><![CDATA[".$v['url']."]]></Url>  
                    </item>";
            }

            $template .="</Articles>  
                 </xml> ";
            echo sprintf($template, $toUser, $fromUser, time(), 'news');
            //注意：进行多图文发送时，子图文个数不能超过10个
        }else{
            switch( trim( $postObj->Content ) )
            {
                case 1:
                    $content = '你输入了个数字1';
                    break;
                case Tel:
                    $content = '12345678901';
                    break;
                case '教程':
                    $content = "<a href='www.imooc.com'>慕课网</a>";
                    break;
                case '博客':
                    $content = "<a href='blog.abc.com'>测试微信</a>";
                    break;
                default:
                    $content = '升级打造中...';
                    break;
            }
            $toUser     =  $postObj->FromUserName;
            $fromUser   =  $postObj->ToUserName;
            $time       =  time();
            $msgType    =  'text';
            $template   =  "<xml>  
                        <ToUserName><![CDATA[%s]]></ToUserName>  
                        <FromUserName><![CDATA[%s]]></FromUserName>  
                        <CreateTime>%s</CreateTime>  
                        <MsgType><![CDATA[%s]]></MsgType>  
                            <Content><![CDATA[%s]]></Content>  
                        </xml>";
            echo sprintf($template, $toUser, $fromUser, $time, $msgType, $content);
        }
    }
}