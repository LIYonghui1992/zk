<?php
/**
 * Created by PhpStorm.
 * User: lyh
 * Date: 2017/12/4
 * Time: 9:39
 */

namespace app\shop\controller;


use think\Controller;
use app\shop\service\JSSDK;
class BaseController extends Controller
{
    public function _initialize(){
        $jssdk = new JSSDK(config("wx.appId"), config("wx.appSecret"));
        $signPackage = $jssdk->GetSignPackage();
        $this->assign("appId",$signPackage["appId"]);
        $this->assign("timestamp",$signPackage["timestamp"]);
        $this->assign("nonceStr",$signPackage["nonceStr"]);
        $this->assign("signature",$signPackage["signature"]);
    }
}