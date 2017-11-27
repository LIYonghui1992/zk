<?php
/**
 * Created by PhpStorm.
 * User: lyh
 * Date: 2017/11/27
 * Time: 11:31
 */

namespace app\wx\controller\v1;


use think\Controller;

class Index extends Controller
{
    public function index(){
        $a="aaa";
        $this->assign("a",$a);
        return $this->display();
    }
}