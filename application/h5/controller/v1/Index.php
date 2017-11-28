<?php
/**
 * Created by PhpStorm.
 * User: lyh
 * Date: 2017/11/27
 * Time: 11:31
 */

namespace app\h5\controller\v1;


use think\Controller;

class Index extends Controller
{
    public function index(){
        return $this->fetch();
    }
}