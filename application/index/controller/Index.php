<?php
/**
 * Created by PhpStorm.
 * User: lyh
 * Date: 2017/11/27
 * Time: 13:47
 */

namespace app\index\controller;


use think\Controller;

class Index extends Controller
{
    public function index(){
        $this->assign("test","testleo");
        $this->fetch();
    }
}