<?php
/**
 * Created by PhpStorm.
 * User: MyPC
 * Date: 2017/12/2
 * Time: 21:28
 */

namespace app\shop\controller;


use think\Controller;
use app\shop\controller\BaseController;
class Index extends BaseController
{
    public function index(){
        $uid="3225613097";
        $url=$this->urlsafe_b64encode($uid);
        $this->assign('url',$url);
        return $this->fetch();
    }
    function urlsafe_b64encode($string) {
        $data = base64_encode($string);
        $data = str_replace(array('+','/','='),array('-','_',''),$data);
        return $data;
    }
}