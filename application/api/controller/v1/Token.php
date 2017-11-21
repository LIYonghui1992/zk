<?php
/**
 * Created by PhpStorm.
 * User: MyPC
 * Date: 2017/11/21
 * Time: 19:24
 */

namespace app\api\controller\v1;


use app\api\service\UserToken;
use app\api\validate\TokenGet;

class Token
{
    public function getToken($code=''){
        (new TokenGet())->goCheck();
        $ut=new UserToken($code);
        $token=$ut->get();
        return [
            'token'=>$token
        ];
    }
}