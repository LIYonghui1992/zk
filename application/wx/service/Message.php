<?php
/**
 * Created by PhpStorm.
 * User: MyPC
 * Date: 2017/11/24
 * Time: 0:21
 */

namespace app\wx\service;
use think\Log;
use app\wx\model\Weather as WeatherModel;
use app\wx\service\User as UserService;
use app\wx\model\WxUser as WxUserModel;
class Message
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
            //记录收到的数据,先处理下放置中文乱码
//            $logJSON=[];
//            foreach ( $postObj as $key => $value ) {
//                $logJSON[$key] = urlencode ( $value );
//            }
//            Log::record("Receive post Str".json_encode($logJSON));
            Log::record("Receive post Str".json_encode($postObj));
            //事件消息类型
            switch ($postObj->MsgType){      //text image location voice video link
                case 'event':
                    //获取事件类型
//                    $form_Event = $postObj->Event;
//                    //订阅事件
//                    if($form_Event=="subscribe")
//                    {
//                        //回复欢迎文字消息
//                        $msgType = "text";
//                        $contentStr = "感谢您关注Leo的微信公众号[玫瑰]";
//                        $resultStr = sprintf($textTpl, $fromUsername, $toUsername, time(), $msgType, $contentStr);
//                        return $resultStr;
//                    }
                    $result = $this->receiveEvent($postObj);
                    break;
                case "text":
                    $result = $this->receiveText($postObj);     //接收文本消息
                    break;
                case "image":
                    $result = $this->receiveImage($postObj);   //接收图片消息
                    break;
                case "location":
                    $result = $this->receiveLocation($postObj);  //接收位置消息
                    break;
                case "voice":
                    $result = $this->receiveVoice($postObj);   //接收语音消息 -----
                    break;
                case "video":
                    $result = $this->receiveVideo($postObj);  //接收视频消息
                    break;
                case "link":
                    $result = $this->receiveLink($postObj);  //接收链接消息
                    break;
                default:
                    return "";
                    break;
            }
            return $result;
        }else {
            return "";
        }
    }
    //接收事件消息
    private function receiveEvent($object)
    {

        //临时定义一个变量， 不同的事件发生时， 给用户反馈不同的内容
        $content = "";

        //通过用户发过来的不同事件做处理
        switch ($object->Event)
        {
            //用户一关注 触发的事件
            case "subscribe":

                $content = "欢迎关注leo的测试账号！";

                $access_token="ufmTencp2CMvha92l8NZhkh64zeoaMJAISI8JGZbTfL4SMZ6YxmDSjdf0n4cnZQMNOqX36vFiSh_VcE5VA1EYnEtR7yuiHX0c1Jln8tguQc";

                $groupid = str_replace("qrscene_","", $object->EventKey);

                $openid = $object->FromUserName;

                $url = "https://api.weixin.qq.com/cgi-bin/groups/members/update?access_token={$access_token}";

                //参数post json
                $jsonstr = '{"openid":"'.$openid.'","to_groupid":'.$groupid.'}';


                $result = https_request($url, $jsonstr);

                //如果用户传来EventKey事件， 则是扫描二维码的
                $content .= (!empty($object->EventKey))?"\n来自二维码场景 ".$scan:"";
                break;
            //取消关注时触发的事件
            case "unsubscribe":
                $content = "取消关注";
                break;

            case "SCAN":
                $content = "扫描场景 ".$object->EventKey;
                break;

            case "CLICK":
                switch ($object->EventKey)
                {
                    case "company":
                        $content = array();
                        $content[] = array("Title"=>"小规模低性能低流量网站设计原则",  "Description"=>"单图文内容", "PicUrl"=>"http://mmbiz.qpic.cn/mmbiz/2j8mJHm8CogqL5ZSDErOzeiaGyWIibNrwrVibuKUibkqMjicCmjTjNMYic8vwv3zMPNfichUwLQp35apGhiciatcv0j6xwA/0", "Url" =>"http://mp.weixin.qq.com/s?__biz=MjM5NDAxMDEyMg==&mid=201222165&idx=1&sn=68b6c2a79e1e33c5228fff3cb1761587#rd");
                        break;
                    default:
                        $content = "点击菜单：".$object->EventKey;
                        break;
                }
                break;
            case "LOCATION":
                $content = "上传位置：纬度 ".$object->Latitude.";经度 ".$object->Longitude;
                break;
            case "VIEW":
                $content = "跳转链接 ".$object->EventKey;
                break;
            case "MASSSENDJOBFINISH":
                $content = "消息ID：".$object->MsgID."，结果：".$object->Status."，粉丝数：".$object->TotalCount."，过滤：".$object->FilterCount."，发送成功：".$object->SentCount."，发送失败：".$object->ErrorCount;
                break;
            default:
                $content = "receive a new event: ".$object->Event;
                break;
        }
        if(is_array($content)){
            if (isset($content[0])){
                $result = $this->transmitNews($object, $content);
            }else if (isset($content['MusicUrl'])){
                $result = $this->transmitMusic($object, $content);
            }
        }else{
            $result = $this->transmitText($object, $content);
        }

        return $result;
    }
    //处理文本消息
    private function receiveText($object){
        $keyword=trim($object->Content);
        //如果用户发送内容不为空，回复“谢谢您的回复!”
        if(!empty( $keyword ))
        {
            $contentStr = "谢谢您的回复!";
            if(strstr($keyword,"文本")){
                $content="你正在问一个文本";
                $contentStr=$this->transmitText($object,$content);
            }else if(strstr($keyword,"单图文")){
//                $newsArray[]=array("Title"=>"","Description"=>"","PicUrl"=>"","Url"=>"");
                $newsArray[]=array("Title"=>"单图文","Description"=>"单图文测试","PicUrl"=>"http://n.sinaimg.cn/default/1_img/uplaod/3933d981/20171124/oZAP-fypatmu9056108.jpg","Url"=>"http://www.dzwww.com/tupian/wyzp/201711/t20171124_16703559.htm");
                $contentStr=$this->transmitNews($object,$newsArray);
            }else if(strstr($keyword,"多图文")){
                $newsArray[]=array("Title"=>"多图文1","Description"=>"多图文1","PicUrl"=>"http://n.sinaimg.cn/default/1_img/uplaod/3933d981/20171124/oZAP-fypatmu9056108.jpg","Url"=>"http://www.baidu.com");
                $newsArray[]=array("Title"=>"多图文2","Description"=>"多图文1","PicUrl"=>"http://n.sinaimg.cn/default/1_img/uplaod/3933d981/20171124/oZAP-fypatmu9056108.jpg","Url"=>"http://www.baidu.com");
                $newsArray[]=array("Title"=>"多图文3","Description"=>"多图文1","PicUrl"=>"http://n.sinaimg.cn/default/1_img/uplaod/3933d981/20171124/oZAP-fypatmu9056108.jpg","Url"=>"http://www.baidu.com");
                $newsArray[]=array("Title"=>"多图文4","Description"=>"多图文1","PicUrl"=>"http://n.sinaimg.cn/default/1_img/uplaod/3933d981/20171124/oZAP-fypatmu9056108.jpg","Url"=>"http://www.baidu.com");
                $contentStr=$this->transmitNews($object,$newsArray);
            }else if(strstr($keyword,"音乐")){
                $music=["Title"=>"音乐标题","Description"=>"这是一首歌","MusicUrl"=>"http://music.baidu.com/song/100575177?fm=altg_new3","HQMusicUrl"=>"http://music.baidu.com/song/100575177?fm=altg_new3"];
                $contentStr=$this->transmitMusic($object,$music);
            }else {
                $content="谢谢您的回复!"; //如果添加了客服服务 这里就是你输入的文本
                //客服接口，先将用户发送的消息和用户个人信息写入数据库
                //调用一个方法 将openId和你输入的内容,使用函数处理
                $access_token=get_token();
                $openId=$object->FromUserName;
                $user=new UserService($access_token,$openId);
                $userinfo_json=$user->getUserInfo();//拿到json格式用户信息
                //将json用户信息转为数组
                $userinfo=json_decode($userinfo_json,true);
                //将用户信息写入数据库
                $usermodel=new WxUserModel($userinfo);
                $usermodel->allowField(true)->save();
                Log::record("Userinfo: ".$userinfo_json);
                //给你回复的内容
                $contentStr=$this->transmitText($object,$usermodel->id);
            }
            return $contentStr;
        }else{
            return "";
        }
    }
    //接收图片消息
    private function receiveImage($object)
    {
        $content = array("MediaId"=>$object->MediaId);
        $result = $this->transmitImage($object, $content);
        return $result;
    }
    //接收位置消息
    private function receiveLocation($object)
    {
        $content = "你发送的是位置，纬度为：".$object->Location_X."；经度为：".$object->Location_Y."；缩放级别为：".$object->Scale."；位置为：".$object->Label;
        $result = $this->transmitText($object, $content);
        return $result;
    }
    //接收语音消息
    private function receiveVoice($object)
    {

        /*

            //如果开启语言识别功能， 就可以使用这个
        if (isset($object->Recognition) && !empty($object->Recognition)){
            $content = "你刚才说的是：".$object->Recognition;
            $result = $this->transmitText($object, $content);
        }else{
            $content = "未开启语音识别功能或者识别内容为空";
             $result = $this->transmitText($object, $content);
        }


        */

        //如果开启语言识别功能， 就可以使用这个
        if (isset($object->Recognition) && !empty($object->Recognition)){
            $wordpos=fc($object->Recognition);//拿到数组 ['0'=>'北京:n','1'=>'上海:n']
            //去除里面:n
            $wordpos=removeletter($wordpos);
            $wordpos=array_filter($wordpos);
            $wordpos=array_merge($wordpos);
            $citycode_arr=WeatherModel::getCityCodeByCityName($wordpos);
//            $content=implode(",",$wordpos);
//            $content=json_encode($citycode);
            if(!empty($citycode_arr)){
                //发送图文消息
                $weatherArray=[];
                foreach ($citycode_arr as $citycode){
//                    Log::record("天气url".config("setting.wa"))
                    $weatherurl=sprintf(config("setting.weatherapi"),$citycode);
                    $weatherinfo_json=https_request($weatherurl);
//                    Log::record("天气:".$weatherinfo_json);
                    $weatherinfo=json_decode($weatherinfo_json,true);
                    $info=$weatherinfo['weatherinfo'];
//                    ." 湿度：".$info['SD']
                    $content="实时温度：".$info['temp']." 风向：".$info['WD']." 风力：".$info['WS']." 更新时间：".$info['time'];
                    Log::record("天气:".$weatherinfo_json."，描述内容: ".$content);
                    $weatherArray[]=array("Title"=>$info['city']."现在的天气预报", "Description"=>$content, "PicUrl"=>"https://ss0.bdstatic.com/94oJfD_bAAcT8t7mm9GUKT-xh_/timg?image&quality=100&size=b4000_4000&sec=1511680520&di=c07b62017fcb4e35e6725663d773384a&src=http://img.pconline.com.cn/images/upload/upc/tx/itbbs/1412/28/c14/1223167_1419772621368.jpg", "Url"=>"https://ss0.bdstatic.com/94oJfD_bAAcT8t7mm9GUKT-xh_/timg?image&quality=100&size=b4000_4000&sec=1511680520&di=c07b62017fcb4e35e6725663d773384a&src=http://img.pconline.com.cn/images/upload/upc/tx/itbbs/1412/28/c14/1223167_1419772621368.jpg");
                }
                $result=$this->transmitNews($object,$weatherArray);
            }else{
                $content = "没有找到您说的：".$object->Recognition;
                $result = $this->transmitText($object, $content);
            }
        }else{
            $content = array("MediaId"=>$object->MediaId);
            $result = $this->transmitVoice($object, $content);
        }

        return $result;
    }
    //接收视频消息
    private function receiveVideo($object)
    {
        $content = array("MediaId"=>$object->MediaId, "Title"=>"this is a test", "Description"=>"pai pai");
        $result = $this->transmitVideo($object, $content);
        return $result;
    }
    //接收链接消息
    private function receiveLink($object)
    {
        $content = "你发送的是链接，标题为：".$object->Title."；内容为：".$object->Description."；链接地址为：".$object->Url;
        $result = $this->transmitText($object, $content);
        return $result;
    }


    //回复文本消息
    private function transmitText($object,$content){
        $textTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[text]]></MsgType>
                    <Content><![CDATA[%s]]></Content>
                    </xml>";
        $resultStr = sprintf($textTpl, $object->FromUserName,$object->ToUserName,time(), $content);
        return $resultStr;
    }
    //回复图片消息
    private function transmitImage($object, $imageArray)
    {
        $itemTpl = "<Image>
    <MediaId><![CDATA[%s]]></MediaId>
</Image>";

        $item_str = sprintf($itemTpl, $imageArray['MediaId']);

        $xmlTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[image]]></MsgType>
$item_str
</xml>";

        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }
    //回复语音消息
    private function transmitVoice($object, $voiceArray)
    {
        $itemTpl = "<Voice>
    <MediaId><![CDATA[%s]]></MediaId>
</Voice>";

        $item_str = sprintf($itemTpl, $voiceArray['MediaId']);

        $xmlTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[voice]]></MsgType>
$item_str
</xml>";

        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }
    //回复视频消息
    private function transmitVideo($object, $videoArray)
    {
        $itemTpl = "<Video>
    <MediaId><![CDATA[%s]]></MediaId>
    <Title><![CDATA[%s]]></Title>
    <Description><![CDATA[%s]]></Description>
</Video>";

        $item_str = sprintf($itemTpl, $videoArray['MediaId'], $videoArray['Title'], $videoArray['Description']);

        $xmlTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[video]]></MsgType>
$item_str
</xml>";

        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }
    //回复图文消息
    private function transmitNews($object,$newsArray){
        if(!is_array($newsArray)){
            return "";
        }
        //选项
        $itemTpl="
            <item>
            <Title><![CDATA[%s]]></Title> 
            <Description><![CDATA[%s]]></Description>
            <PicUrl><![CDATA[%s]]></PicUrl>
            <Url><![CDATA[%s]]></Url>
            </item>
        ";
        $item_str="";
        foreach ($newsArray as $item){
            $item_str.=sprintf($itemTpl,$item['Title'],$item['Description'],$item['PicUrl'],$item['Url']);
        }
        $xmlTpl="
            <xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%s</CreateTime>
            <MsgType><![CDATA[news]]></MsgType>
            <ArticleCount>%s</ArticleCount>
            <Articles>
            $item_str
            </Articles>
            </xml>
        ";
        return sprintf($xmlTpl,$object->FromUserName,$object->ToUserName,time(),count($newsArray));

    }
    //回复音乐消息
    private function transmitMusic($object,$music){
        $musicTpl = "<xml>  
                             <ToUserName><![CDATA[%s]]></ToUserName>  
                             <FromUserName><![CDATA[%s]]></FromUserName>  
                             <CreateTime>%s</CreateTime>  
                             <MsgType><![CDATA[music]]></MsgType>  
                             <Music>  
                             <Title><![CDATA[%s]]></Title>  
                             <Description><![CDATA[%s]]></Description>  
                             <MusicUrl><![CDATA[%s]]></MusicUrl>  
                             <HQMusicUrl><![CDATA[%s]]></HQMusicUrl> 
                             </Music>  
                             </xml>";
        $resultStr = sprintf($musicTpl, $object->FromUserName,$object->ToUserName,time(), $music['Title'],$music['Description'],$music['MusicUrl'],$music['HQMusicUrl']);
        return $resultStr;
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