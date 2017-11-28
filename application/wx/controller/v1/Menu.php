<?php
/**
 * Created by PhpStorm.
 * User: MyPC
 * Date: 2017/11/25
 * Time: 21:08
 */

namespace app\wx\controller\v1;

use app\wx\service\Menu as MenuService;
class Menu
{
    function setMenu(){
        $access_token=get_token();
//        $access_token="UOZifR3nBond7HhgLEGXridmq4wyU9bwv3zOOHzXoLeE0jJlT1Xw68t4GQA9E6ZuDZR0K4k9L7xmIiEtOyIWTZVyPOcr-Bxu-hln0h3KBgkd8ltYlee0er2t40td75xCPDUhACAKYP";
        $menu=new MenuService($access_token);
        $result=$menu->setMenu();
        return $result;
    }
}