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
class Index extends Controller
{
    public function index(){
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