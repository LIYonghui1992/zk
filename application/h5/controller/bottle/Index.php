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
class Index extends BaseController
{
    public function index(){

        $code="";
//        echo CONTROLLER_NAME; //Bottle.index
        $controller_bf=lcfirst(explode(".",CONTROLLER_NAME)[0]);
        $controller_af=explode(".",CONTROLLER_NAME)[1];
//        $url= 'http://'.$_SERVER['HTTP_HOST']."/".MODULE_NAME."/".$controller_bf."/".ACTION_NAME."<br>";
//        echo $url;
//        echo $_SERVER['PHP_SELF']."<br>"; // /index.php/h5/bottle/index
        //根据openid 查找本地数据库 所以这里我根据openid来判断
//不能让它无限跳转 解决办法
        //1. 根据code来判断 能否获取到access_token 不能则重新授权

//        if(empty($_COOKIE['openid'])){
//            $_COOKIE['openid']="1";
            $url= 'http://'.$_SERVER['HTTP_HOST']."/".MODULE_NAME."/".$controller_bf."/".ACTION_NAME;
            $token=new TokenService();
            $bottleurl=$token->auth($url);
            $this->redirect($bottleurl);
            exit();
//        }



        $flag=0;//为1 代表需要授权
//        if(empty($_COOKIE['openid'])){
//            //说明用户无授权 则跳转到授权页
//            $flag=1;
//        }else{
//            //根据openid去数据库中查找是否存在access_token以及是否过期
//            $openid=$_COOKIE['openid'];
//            $result=WxTokenModel::getTokenByOpenId($openid);
//            if(empty($result)){
//                $flag=1;
//            }else{
//                $token_expired_time=$result['update_time']+7000;
//                if($token_expired_time>time()){//说明access_token还可以用
//                    $access_token=$result['access_token'];
//                    //下面根据这个access_token 和openid 就可以进行关于用户信息的操作了
//                }else{//判断refresh_token是否超过30天了
//                    $refresh_expired_time=$result['create_time']+86400*30;
//                    if($refresh_expired_time>time()){//说明refresh_token没过期 可以用来刷新access_token
//                        //执行刷新access_token
//                        $token=new TokenService();
//                        $tokenresult=$token->refreshToken($result['refresh_token']);
//                        //刷新完了 access_token可以用了
//                        $access_token=$tokenresult['access_token'];
//                    }else{
//                        $flag=1;
//                    }
//                }
//            }
//        }
//        if($flag==1){
//            $url= 'http://'.$_SERVER['HTTP_HOST']."/".MODULE_NAME."/".$controller_bf."/".ACTION_NAME;
//            $token=new TokenService();
//            $bottleurl=$token->auth($url);
//            $this->redirect($bottleurl);
//            exit();
//        }
        if(isset($_GET['code'])){
            $code=$_GET['code'];
        }else{

        }
        $this->assign("code",$code);
        if(isset($_GET['state'])){
            $this->assign("state",$_GET['state']);
        }else{
            $this->assign("state",'state');
        }
        //拿到了code 就要根据code 获取用户的信息 进行一些操作
//        $token=new TokenService($code);
//        $tokeninfo=$token->getToken();
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