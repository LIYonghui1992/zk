<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Log;
// 应用公共文件
function curl_get($url, &$httpCode = 0){
    $ch=curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//是否返回内容还是直接输出 1为返回内容 0为直接输出内容
//模拟post
//    curl_setopt($ch,CURLOPT_POST,1);
//post 发送的数据
//    curl_setopt($ch,CURLOPT_POSTFIELDS,$arr);
    //不做证书校验，部署在Linux环境下请改为true
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    $file_contents = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return $file_contents;
}

function getRandChar($length){
    $str=null;
    $strPol="ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
    $max=strlen($strPol)-1;
    for($i=0;$i<$length;$i++){
        $str.=$strPol[rand(0,$max)];
    }
    return $str;
}


//微信公众号
function checkSignature($signature,$timestamp,$nonce){
    $token=config('wx.token');
    $tmpArr = array($token,$timestamp, $nonce);
    sort($tmpArr, SORT_STRING);
    $tmpStr = implode( $tmpArr );
    $tmpStr = sha1( $tmpStr );
    if($signature==$tmpStr){
        return true;
    }else{
        return false;
    }

}

//CURL请求的函数http_request()
//通过https 中的get 或 post
function https_request($url, $data = null)
{
    Log::record("Common: https_request: url is $url, data is $data ");
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    if (!empty($data)){
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($curl);
    $err_code = curl_errno($curl);
    curl_close($curl);
    Log::record("Common: https_request: output is $output, error is $err_code");
    //获得的是json格式数据
    return $output;
}

//获取access_token函数
//获取access_token
function get_token() {
    $appid=config("wx.appId");
    $secret=config("wx.appSecret");
    $token_url=sprintf(config('wx.token_url'),$appid,$secret);
    if(session("access_token")){
            $access_token=session("access_token");
    }else {
        $json = https_request($token_url);
        $arr = json_decode($json, true);//将json转化为数组
        $access_token = $arr["access_token"];
        session("access_token", $access_token, 7100);
    }
    return $access_token;
}

//my_json_decode() 将数组转成json

function my_json_encode($p, $type="text") {
    if (PHP_VERSION >= '5.4')
    {
        $str = json_encode($p, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
    else
    {
        switch ($type)
        {
            case 'text':
                isset($p['text']['content']) && ($p['text']['content'] = urlencode($p['text']['content']));
                break;
        }
        $str = urldecode(json_encode($p));
    }
    return $str;
}

//调用百度分词接口
function fc($text){
    Log::record("分词内容:".$text);
    //百度分词支持后面跟中文所以不用转utf-8
//    $text=iconv("UTF-8", "GBK//IGNORE", $text);
    $text=urlencode($text);
    $fcurl=config("setting.baidufc").$text;
    Log::record("分词url is".$fcurl);
    $result=file_get_contents($fcurl);//拿到的结果是一个json
//    $result=https_request($fcurl);//拿到的结果是一个数组，里面wordpos 对应的是分词的数组
    $result_arr=json_decode($result,true);//先把json转化为数组，里面result ->res ->wordpos 对应的是分词的数组
    $wordpos=$result_arr['result']['res']['wordpos'];
//    $return=[];
//    foreach ($wordpos as $value){
//            $return[]=iconv("GBK","UTF-8//IGNORE",$value);
//    }
    return $wordpos;

}
//去除数组中字母
function removeletter($array){
    foreach ($array as &$value){
        $value=preg_replace('#\:#',"",$value);
        $value=preg_replace("/[a-zA-Z]+/","",$value);
        $value=strFilter($value);
    }
    return $array;
}
//去除字符串中特殊符号

function strFilter($str){
    $str = str_replace('`', '', $str);
    $str = str_replace('·', '', $str);
    $str = str_replace('~', '', $str);
    $str = str_replace('!', '', $str);
    $str = str_replace('！', '', $str);
    $str = str_replace('@', '', $str);
    $str = str_replace('#', '', $str);
    $str = str_replace('$', '', $str);
    $str = str_replace('￥', '', $str);
    $str = str_replace('%', '', $str);
    $str = str_replace('^', '', $str);
    $str = str_replace('……', '', $str);
    $str = str_replace('&', '', $str);
    $str = str_replace('*', '', $str);
    $str = str_replace('(', '', $str);
    $str = str_replace(')', '', $str);
    $str = str_replace('（', '', $str);
    $str = str_replace('）', '', $str);
    $str = str_replace('-', '', $str);
    $str = str_replace('_', '', $str);
    $str = str_replace('——', '', $str);
    $str = str_replace('+', '', $str);
    $str = str_replace('=', '', $str);
    $str = str_replace('|', '', $str);
    $str = str_replace('\\', '', $str);
    $str = str_replace('[', '', $str);
    $str = str_replace(']', '', $str);
    $str = str_replace('【', '', $str);
    $str = str_replace('】', '', $str);
    $str = str_replace('{', '', $str);
    $str = str_replace('}', '', $str);
    $str = str_replace(';', '', $str);
    $str = str_replace('；', '', $str);
    $str = str_replace(':', '', $str);
    $str = str_replace('：', '', $str);
    $str = str_replace('\'', '', $str);
    $str = str_replace('"', '', $str);
    $str = str_replace('“', '', $str);
    $str = str_replace('”', '', $str);
    $str = str_replace(',', '', $str);
    $str = str_replace('，', '', $str);
    $str = str_replace('<', '', $str);
    $str = str_replace('>', '', $str);
    $str = str_replace('《', '', $str);
    $str = str_replace('》', '', $str);
    $str = str_replace('.', '', $str);
    $str = str_replace('。', '', $str);
    $str = str_replace('/', '', $str);
    $str = str_replace('、', '', $str);
    $str = str_replace('?', '', $str);
    $str = str_replace('？', '', $str);
    return trim($str);
}