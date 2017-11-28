<?php
/**
 * Created by PhpStorm.
 * User: MyPC
 * Date: 2017/11/25
 * Time: 21:11
 */

namespace app\wx\service;

use think\Log;
class Menu
{
    protected $wxMenuUrl;
    public function __construct($access_token)
    {
        $this->wxMenuUrl=sprintf(config('wx.menu_url'),$access_token);
        Log::record("MenuUrl is: ".$this->wxMenuUrl);
    }
    public function setMenu(){
        //前台表单定制菜单，提交表单数组 转换为json菜单 然后发送
        $jsonMenu=<<<json
 {
     "button":[
     {	
          "type":"click",
          "name":"今日歌曲",
          "key":"V1001_TODAY_MUSIC"
      },
      {
           "name":"h5",
           "sub_button":[
           {	
               "type":"view",
               "name":"漂流瓶",
               "url":"http://lvseguoshu.cn/h5/bottle/index"
            },
            {
               "type":"view",
               "name":"首页",
               "url":"http://lvseguoshu.cn/h5/v1/index"
            }]
       }]
 }
json;
        $result=https_request($this->wxMenuUrl,$jsonMenu);
        Log::record("Menu setting result is".$result);
        return $result;

    }
}