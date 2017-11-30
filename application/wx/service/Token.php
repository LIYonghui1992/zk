<?php
/**
 * Created by PhpStorm.
 * User: lyh
 * Date: 2017/11/29
 * Time: 14:49
 */

namespace app\wx\service;

use app\wx\model\WxToken as WxTokenModel;
use think\Log;
class Token
{
    protected $tmpTokenUrl;
    public function __construct($code="")
    {
        $this->tmpTokenUrl=sprintf(config('wx.tmptoken_url'),config('appId'),config('appSecret'),$code);
        Log::record("TmpTokenUrl is: ".$this->tmpTokenUrl);
    }
    public function auth($url){
        $bottle_url=urlencode($url);
        $bottle_para="123";
        $bottleurl=sprintf(config('wx.auth_url'),config('wx.appId'),$bottle_url,$bottle_para);
        return $bottleurl;
    }

    /**
     * 根据code获取token信息 调用WxTokenModel 将token信息存入数据库 以及返回token信息
     */
    public function getToken(){
        $token_json=https_request($this->tmpTokenUrl);
        $tokeninfo=json_decode($token_json,true);
        $openid=$tokeninfo['openid'];
        //根据 openid 查找是否有这个用户的token 并且确认这个token是否过期 如果没有token则插入 如果过期则重新获取
        $token=new WxTokenModel();

        return $tokeninfo;
    }
}