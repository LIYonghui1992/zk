<?php
/**
 * Created by 七月.
 * Author: 七月
 * Date: 2017/4/17
 * Time: 2:05
 */

namespace app\api\controller\v1;

use app\api\validate\IDMustBePostiveInt;
use think\Exception;
use think\Validate;
use app\api\model\Banner as BannerModel;
use app\lib\exception\BannerMissException;
class Banner
{
    /**
     * 获取指定id的banner信息
     * @url /banner/:id
     * @http GET
     * @id banner的id号
     *
     */
    public function getBanner($id)
    {
        (new IDMustBePostiveInt())->goCheck();
//        try{
            $banner = BannerModel::getBannerByID($id);
//        }catch (Exception $ex){
//            //return一个消息的结构体给前端 方便处理
//            $err=[
//                'error_code'=>10001,
//                'msg'=>$ex->getMessage()
//            ];
//            return json($err,400);
//        }
        if(!$banner){
            throw new Exception("内部错误");
//            throw new BannerMissException();
        }

        return $banner;
    }


}