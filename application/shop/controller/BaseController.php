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
use app\h5\model\H5BottleContent;
use app\wx\service\Token as TokenService;
use app\wx\model\WxToken as WxTokenModel;
use think\Log;
use app\wx\service\User as UserService;
use app\wx\model\WxUser as WxUserModel;
use app\wx\model\WxSnsUser as WxSnsUserModel;
use think\Session;
use think\Request;

$request=Request::instance();
define('MODULE_NAME', $request->module());//例如 http://www.lvseguoshu.cn
define('CONTROLLER_NAME', $request->controller());//例如 http://www.lvseguoshu.cn
define('ACTION_NAME', $request->action());//例如 http://www.lvseguoshu.cn
class BaseController extends Controller
{
    public function _initialize(){
        //initialize JS-JDK
        $jssdk = new JSSDK(config("wx.appId"), config("wx.appSecret"));
        $signPackage = $jssdk->GetSignPackage();
        $this->assign("appId",$signPackage["appId"]);
        $this->assign("timestamp",$signPackage["timestamp"]);
        $this->assign("nonceStr",$signPackage["nonceStr"]);
        $this->assign("signature",$signPackage["signature"]);


        //get authorization
        //判断当前账号是否授权，所有的页面都要判断 或者 只是个人中心页面进行判断授权
        $flag=0;
        if(empty(Session::get('openid'))){
            Log::record("没有openid session");
            if(!isset($_GET['code'])){
                //执行授权
                $flag=1;
                Log::record("没有设置code执行授权跳转");
            }else{
                Log::record("设置了code".$_GET['code']);
                //根据code去获取
                $token=new TokenService($_GET['code']);
                $tokeninfo=$token->getToken();
                if(array_key_exists("errcode",$tokeninfo)){
                    //the code have been used, authorize again
                    $flag=1;
                }else{
                    //将tokeninfo 存到数据库中
                    $access_token=$tokeninfo['access_token'];
                    $openid=$tokeninfo['openid'];
                    //根据 openid 查找是否有这个用户的token 然后执行插入操作
//                    $result=WxTokenModel::getTokenByOpenId($openid);
                    $result_token=WxTokenModel::getByOpenid($openid);
                    $data['access_token']=$tokeninfo['access_token'];
                    $data['refresh_token']=$tokeninfo['refresh_token'];
                    $data['scope']=$tokeninfo['scope'];
                    $data['create_time']=time();
                    $data['update_time']=time();
                    if($result_token){//如果有 则执行更新操作
                        $where_token['id']=$result_token['id'];
                        $token=WxTokenModel::where($where_token)->update($data);
                    }else{//否则执行插入操作
                        $data['openid']=$tokeninfo['openid'];
                        $data['expires_in']=$tokeninfo['expires_in'];
                        $token=WxTokenModel::create($data);
                    }
                    //after inserting and updating, we will set cookie to save openid for next time using
                    Session::set('openid',$openid); //openid will be saved in session, now we get access_token from Mysql, finally we will use memcache to save access_token
                    Log::record("Session openid have been recorded: ". Session::get('openid')."openid is: $openid");

                }
            }
        }else{
            Log::record("存在openid session".Session::get('openid'));
            //search the access_token from our database and check whether this access_token have been expired
            $openid=Session::get('openid');
            $result_token=WxTokenModel::getByOpenid($openid);
            if(empty($result_token)){
                Log::record("result_token 为空");
                Session::delete("openid");
                $flag=1;
            }else{
                //要用读取器处理，从模型中读出来的update_time自动被转换成了日期的格式 应该是时间戳才对
                $token_expired_time=$result_token['update_time']+7000;
                Log::record("token update time is ".$result_token['update_time']."token expired time is: $token_expired_time");
                if($token_expired_time>time()){//说明access_token还可以用
                    Log::record("result_token 不为空，access_token 没过期 可以使用");
                    $access_token=$result_token['access_token'];
                    //下面根据这个access_token 和openid 就可以进行关于用户信息的操作了
                }else{//判断refresh_token是否超过30天了
                    $refresh_expired_time=$result_token['create_time']+86400*30;
                    if($refresh_expired_time>time()){//说明refresh_token没过期 可以用来刷新access_token
                        //执行刷新access_token
                        $token=new TokenService();
                        $tokenresult=$token->refreshToken($result_token['refresh_token']);
                        //刷新完了 access_token可以用了
                        $access_token=$tokenresult['access_token'];
                    }else{
                        Log::record("result_token 不为空，access_token 过期 refresh_token也过期".time());
                        Session::delete("openid");
                        $flag=1;
                    }
                }
            }
        }
        //1. 拼凑授权地址,跳转到授权页
//        $controller_bf=lcfirst(explode(".",CONTROLLER_NAME)[0]);
        if($flag==1){
            $url= 'http://'.$_SERVER['HTTP_HOST']."/".MODULE_NAME."/".CONTROLLER_NAME."/".ACTION_NAME;
            $token=new TokenService();
            $url=$token->auth($url);
            Log::record("跳转到页面url: $url");
            $this->redirect($url);
            exit();
        }
        //Get user information and save or update to database
        //
        if(!empty($access_token)){
            $userService=new UserService($access_token,$openid);
            $userinfo_json=$userService->getSnsUserInfo();//拿到json格式用户信息
            //将json用户信息转为数组
            $userinfo=json_decode($userinfo_json,true);
            //判断用户信息是否在wx_sns_user表中,如果在将用户信息更新 否则写入数据库
            $result_user=WxSnsUserModel::getByopenid($openid);
            if($result_user){//更新
                Log::record("SnsUserinfo update: ==================================================================");
                $where_user['id']=$result_user['id'];
                $userid=$result_user['id'];
                $userinfo['update_time']=time();
                $result_user=WxSnsUserModel::where($where_user)->update($userinfo);
                Log::record("SnsUserinfo update result:$result_user Userid is $userid======================================================");
            }else{ //插入
                Log::record("SnsUserinfo insert: ==================================================================");
                $usermodel=new WxSnsUserModel($userinfo);
                $usermodel->create_time=time();
                $usermodel->update_time=time();
                $result_user=$usermodel->allowField(true)->save();
                $userid=$usermodel->id;
                Log::record("SnsUserinfo insert result: $userid ==================================================================");
            }
            $user=WxSnsUserModel::getByopenid($openid);
            $this->assign("user",$user);
            Log::record("SnsUserinfo: ".$userinfo_json);
        }


    }

    public function index(){

//        echo CONTROLLER_NAME; //Bottle.index
        $controller_bf=lcfirst(explode(".",CONTROLLER_NAME)[0]);
        $controller_af=explode(".",CONTROLLER_NAME)[1];
//        echo $_SERVER['PHP_SELF']."<br>"; // /index.php/h5/bottle/index
//solution
        //Our purpose is getting access_token to access user's information, first of all, we check whether we have an openid cache, if we have, we will check our database, if we don't have openid cache , we're going to judge code, if we have code we will use
        //this code to get access_token, if we get it , we will use it and save it, or we apply an authority
        $flag=0;
        //we will judge this logic in every action, because we wanna get access_token to get user's information
        //if we have session we can get user's information from database, if we don't have session, we will get it by code->access_token->user's information
        //Important: we should control which user's information should be the most fresh
        if(empty(Session::get('openid'))){
            Log::record("没有openid session");
            if(isset($_GET['code'])){
                Log::record("设置了code".$_GET['code']);
                //根据code去获取
                $token=new TokenService($_GET['code']);
                $tokeninfo=$token->getToken();
                if(array_key_exists("errcode",$tokeninfo)){
                    //the code have been used, authorize again
                    $flag=1;
                }else{
                    //将tokeninfo 存到数据库中
                    $access_token=$tokeninfo['access_token'];
                    $openid=$tokeninfo['openid'];
                    //根据 openid 查找是否有这个用户的token 然后执行插入操作
//                    $result=WxTokenModel::getTokenByOpenId($openid);
                    $result_token=WxTokenModel::getByOpenid($openid);
                    $data['access_token']=$tokeninfo['access_token'];
                    $data['refresh_token']=$tokeninfo['refresh_token'];
                    $data['scope']=$tokeninfo['scope'];
                    $data['create_time']=time();
                    $data['update_time']=time();
                    if($result_token){//如果有 则执行更新操作
                        $where_token['id']=$result_token['id'];
                        $token=WxTokenModel::where($where_token)->update($data);
                    }else{//否则执行插入操作
                        $data['openid']=$tokeninfo['openid'];
                        $data['expires_in']=$tokeninfo['expires_in'];
                        $token=WxTokenModel::create($data);
                    }
                    //after inserting and updating, we will set cookie to save openid for next time using
                    Session::set('openid',$openid); //openid will be saved in session, now we get access_token from Mysql, finally we will use memcache to save access_token
                }
            }else{
                //执行授权
                $flag=1;
                Log::record("没有设置code执行授权跳转");
            }
        }else{
            Log::record("存在openid session".Session::get('openid'));
            //search the access_token from our database and check whether this access_token have been expired
            $openid=Session::get('openid');
            $result_token=WxTokenModel::getByOpenid($openid);
            if(empty($result_token)){
                $flag=1;
            }else{
                $token_expired_time=$result_token['update_time']+7000;
                if($token_expired_time>time()){//说明access_token还可以用
                    $access_token=$result_token['access_token'];
                    //下面根据这个access_token 和openid 就可以进行关于用户信息的操作了
                }else{//判断refresh_token是否超过30天了
                    $refresh_expired_time=$result_token['create_time']+86400*30;
                    if($refresh_expired_time>time()){//说明refresh_token没过期 可以用来刷新access_token
                        //执行刷新access_token
                        $token=new TokenService();
                        $tokenresult=$token->refreshToken($result_token['refresh_token']);
                        //刷新完了 access_token可以用了
                        $access_token=$tokenresult['access_token'];
                    }else{
                        $flag=1;
                    }
                }
            }
        }

        if($flag==1){
            $url= 'http://'.$_SERVER['HTTP_HOST']."/".MODULE_NAME."/".$controller_bf."/".ACTION_NAME;
            $token=new TokenService();
            $bottleurl=$token->auth($url);
            Log::record("跳转到页面url$bottleurl. 本页面url 为$url");
            $this->redirect($bottleurl);
            exit();
        }

        //Get user information and save or update to database
        //
        if(!empty($access_token)){
            $userService=new UserService($access_token,$openid);
            $userinfo_json=$userService->getSnsUserInfo();//拿到json格式用户信息
            //将json用户信息转为数组
            $userinfo=json_decode($userinfo_json,true);
            //判断用户信息是否在wx_sns_user表中,如果在将用户信息更新 否则写入数据库
            $result_user=WxSnsUserModel::getByopenid($openid);
            if($result_user){//更新
                Log::record("SnsUserinfo update: ==================================================================");
                $where_user['id']=$result_user['id'];
                $userid=$result_user['id'];
                $userinfo['update_time']=time();
                $result_user=WxSnsUserModel::where($where_user)->update($userinfo);
                Log::record("SnsUserinfo update result:$result_user Userid is $userid======================================================");
            }else{ //插入
                Log::record("SnsUserinfo insert: ==================================================================");
                $usermodel=new WxSnsUserModel($userinfo);
                $usermodel->create_time=time();
                $usermodel->update_time=time();
                $result_user=$usermodel->allowField(true)->save();
                $userid=$result_user->id;
                Log::record("SnsUserinfo insert result: $userid ==================================================================");
            }
            $user=WxSnsUserModel::getByopenid($openid);
            $this->assign("user",$user);
            Log::record("SnsUserinfo: ".$userinfo_json);
        }

        //Personal information have been saved, now we do something
        return $this->fetch();
    }
}