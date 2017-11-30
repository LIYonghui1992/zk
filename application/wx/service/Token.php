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
     * 授权后拿到的token 这里的create_time和update_time必须更新到最新
     */
    public function getToken(){
        $token_json=https_request($this->tmpTokenUrl);
        $tokeninfo=json_decode($token_json,true);
        $openid=$tokeninfo['openid'];
        //根据 openid 查找是否有这个用户的token 然后执行插入操作
        $result=WxTokenModel::getTokenByOpenId($openid);
        $data['access_token']=$tokeninfo['access_token'];
        $data['refresh_token']=$tokeninfo['refresh_token'];
        $data['scope']=$tokeninfo['scope'];
        $data['create_time']=time();
        $data['update_time']=time();
        if($result){//如果有 则执行更新操作
            $where['id']=$result['id'];
            $token=WxTokenModel::where($where)->update($data);
        }else{//否则执行插入操作
            $data['openid']=$tokeninfo['openid'];
            $data['expires_in']=$tokeninfo['expires_in'];
            $token=WxTokenModel::create($data);
        }

        return $tokeninfo;
    }

    /**
     * @param $refresh_token
     * 这里的create_time和refresh_token可以不用改 接着用
     *
     */
    public function refreshToken($refresh_token){
        $refresh_token_url=sprintf(config('wx.refresh_tmptoken_url'),config('wx.appId'),$refresh_token);
        $token_json=https_request($refresh_token_url);
        $tokeninfo=json_decode($token_json,true);
        //刷新的token肯定是数据库中已经有openid了所以直接更新行了
        $where['openid']=$tokeninfo['openid'];
        $data['access_token']=$tokeninfo['access_token'];
        $data['expires_in']=$tokeninfo['expires_in'];
        $data['scope']=$tokeninfo['scope'];
        $data['update_time']=time();
        $token=WxTokenModel::where($where)->update($data);
        return $tokeninfo;
    }
}