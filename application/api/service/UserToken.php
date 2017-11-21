<?php
/**
 * Created by PhpStorm.
 * User: MyPC
 * Date: 2017/11/21
 * Time: 19:35
 */

namespace app\api\service;
use app\api\model\User;
use app\lib\exception\TokenException;
use app\lib\exception\WechatException;
use think\Exception;
use app\api\model\User as UserModel;
class UserToken extends Token
{
    protected $code;
    protected $wxAppID;
    protected $wxAppSecret;
    protected $wxLoginUrl;

    function __construct($code)
    {
        $this->code=$code;
        $this->wxAppID=config('wx.app_id');
        $this->wxAppSecret=config('wx.app_secret');
        $this->wxLoginUrl=sprintf(config('wx.login_url'),$this->wxAppID,$this->wxAppSecret,$this->code);
    }
    //从微信服务器获取openid and session_Key
    public function get(){
        $result=curl_get($this->wxLoginUrl);
        $wxResult=json_decode($result,true);
        if(empty($wxResult)){
            throw new Exception('获取seesion_key及openID时异常，微信内部错误');
        }else{
            $loginFail = array_key_exists('errcode',$wxResult);
            if($loginFail){
                $this->processLoginError($wxResult);
            }else{
                $token=$this->grantToken($wxResult);
                return $token;
            }
        }
    }
    //颁发令牌
    private function grantToken($wxResult){
        //拿到openid
        //数据库里看一下，这个openid是不是已经存在
        //如果存在，则不处理，如果不存在那么新增一条user记录(相当于添加了一个新会员)
        //生成令牌，准备缓存数据，写入缓存
        //把令牌返回到客户端去
        //key: 令牌
        //value: wxResult, uid, scope
        $openid=$wxResult['openid'];
        $user=UserModel::getByOpenID($openid);
        if($user){
            $uid=$user->id;
        }else{
            $uid=$this->newUser($openid);
        }
        $cachedValue=$this->prepareCachedValue($wxResult,$uid);
        $token=$this->saveToCache($cachedValue);
        return $token;
    }
    private function saveToCache($cachedValue){
        $key=self::generateToken();
        $value=json_encode($cachedValue);
        //缓存失效了代表令牌失效了
        $expire_in=config('setting.token_expire_in');
        $request=cache($key,$value,$expire_in);
        if(!$request){
            throw new TokenException([
                'msg'=>'服务器缓存异常',
                'errorCode'=>10005
            ]);
        }
        return $key;
    }
    private function prepareCachedValue($wxResult,$uid){
        $cachedValue=$wxResult;
        $cachedValue['uid']=$uid;
        $cachedValue['scope']=16;
        return $cachedValue;
    }
    private function newUser($openid){
        $user=UserModel::create([
           'openid'=>$openid
        ]);
        return $user->id;
    }

    private function processLoginError($wxResult){
        throw new WechatException([
            'msg' => $wxResult['errmsg'],
            'errorCode' => $wxResult['errcode']
        ]);
    }
}