<?php
/**
 * Created by PhpStorm.
 * User: MyPC
 * Date: 2017/11/26
 * Time: 16:49
 */

namespace app\wx\service;

use think\Log;
class User
{
    protected $wxUserUrl;
    protected $wxSnsUserUrl;
    public function __construct($access_token,$openId)
    {
        $this->wxUserUrl=sprintf(config('wx.user_url'),$access_token,$openId);
        Log::record("UserUrl is: ".$this->wxUserUrl);
        $this->wxSnsUserUrl=sprintf(config('wx.snsuser_url'),$access_token,$openId);
        Log::record("SnsUserUrl is: ".$this->wxSnsUserUrl);
    }

    public function getUserInfo(){
        $userinfo_json=https_request($this->wxUserUrl);
        return $userinfo_json;
    }
    public function getSnsUserInfo(){
        $userinfo_json=https_request($this->wxSnsUserUrl);
        return $userinfo_json;
    }
}