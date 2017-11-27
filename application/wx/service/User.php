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
    public function __construct($access_token,$openId)
    {
        $this->wxUserUrl=sprintf(config('wx.user_url'),$access_token,$openId);
        Log::record("UserUrl is: ".$this->wxUserUrl);
    }
    public function getUserInfo(){
        $userinfo=https_request($this->wxUserUrl);
        return $userinfo;
    }
}