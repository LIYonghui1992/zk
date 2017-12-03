<?php
/**
 * Created by PhpStorm.
 * User: MyPC
 * Date: 2017/12/2
 * Time: 21:28
 */

namespace app\shop\controller;


use think\Controller;

class Index extends Controller
{
    public function index(){
        echo "aaaa";
        return $this->fetch();
    }
}