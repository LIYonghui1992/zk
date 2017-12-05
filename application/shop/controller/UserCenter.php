<?php
/**
 * Created by PhpStorm.
 * User: MyPC
 * Date: 2017/12/5
 * Time: 19:56
 */

namespace app\shop\controller;


use app\shop\controller\BaseController;

class UserCenter extends BaseController
{
    public function index(){
        return $this->fetch();
    }
}