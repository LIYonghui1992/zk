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
class Index extends Controller
{
    public function index(){
        $this->assign("state",'code');
        $code="";
        echo 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].$_SERVER['QUERY_STRING']."<br>";
        echo $_SERVER['HTTP_HOST']."<br>";
        echo $_SERVER['PHP_SELF']."<br>";
        echo $_SERVER['QUERY_STRING'];
//        if(isset($_GET['code'])){
//            $code=$_GET['code'];
//        }else{
//            //说明用户无授权 则跳转到授权页
//            $url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].$_SERVER['QUERY_STRING'];
////            $baseUrl = rtrim($url, '/');
////            $baseUrl = urlencode($baseUrl);
//            $token=new TokenService();
//            $url=$token->auth($url);
//            $this->redirect($url);
//            exit();
//        }
        $this->assign("code",$code);

//        if(isset($_GET['state'])){
//            $this->assign("state",$_GET['state']);
//        }else{
//            $this->assign("state","00001");
//        }
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