<?php
/**
 * Created by PhpStorm.
 * User: lyh
 * Date: 2017/11/28
 * Time: 9:17
 */

namespace app\h5\controller\bottle;


use think\Controller;
use app\h5\model\H5BottleContent;
use app\wx\service\Token as TokenService;
use app\wx\model\WxToken as WxTokenModel;
use think\Log;
class Index extends BaseController
{
    public function index(){

//        echo CONTROLLER_NAME; //Bottle.index
        $controller_bf=lcfirst(explode(".",CONTROLLER_NAME)[0]);
        $controller_af=explode(".",CONTROLLER_NAME)[1];
//        echo $_SERVER['PHP_SELF']."<br>"; // /index.php/h5/bottle/index
//solution
        //Our purpose is getting access_token to access user's information, first of all, we check whether we have an openid cache, if we have we will check our database, if we don't have openid cache , we're going to judge code, if we have code we will use
        //this code to get access_token, if we get it , we will use it and save it, or we apply an authority
        $flag=0;
        //because wechat can't save cookie
        if(empty($_SESSION['openid'])){
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
                    //after inserting and updating, we will set cookie to save openid for next time using
                    $_SESSION['openid']=$openid;
                }
            }else{
                //执行授权
                $flag=1;
                Log::record("没有设置code执行授权跳转");
            }
        }else{
            Log::record("存在openid session".$_SESSION['openid']);
            //search the access_token from our database and check whether this access_token have been expired
            $openid=$_SESSION['openid'];
            $result=WxTokenModel::getTokenByOpenId($openid);
            if(empty($result)){
                $flag=1;
            }else{
                $token_expired_time=$result['update_time']+7000;
                if($token_expired_time>time()){//说明access_token还可以用
                    $access_token=$result['access_token'];
                    //下面根据这个access_token 和openid 就可以进行关于用户信息的操作了
                }else{//判断refresh_token是否超过30天了
                    $refresh_expired_time=$result['create_time']+86400*30;
                    if($refresh_expired_time>time()){//说明refresh_token没过期 可以用来刷新access_token
                        //执行刷新access_token
                        $token=new TokenService();
                        $tokenresult=$token->refreshToken($result['refresh_token']);
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
//        if(!empty($access_token)){
//            echo $access_token;
//        }
        //To do some user's information accessing procedure according to access_token

        $bottle_content_list=H5BottleContent::select();
//        var_dump($bottle_content_list);
        $count=count($bottle_content_list);
        $id=rand(1,$count);
        $bottle_content_one=H5BottleContent::get($id);
//        $bottle_content_one=$bottle_content_list[$id];
        $content=$bottle_content_one['content'];
        $this->assign("content",$content);
        return $this->fetch();
    }

}