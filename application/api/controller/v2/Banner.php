<?php
/**
 * Created by 七月.
 * Author: 七月
 * Date: 2017/4/17
 * Time: 2:05
 */

namespace app\api\controller\v2;

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

//        $banner=BannerModel::with(['items','items.img'])->find($id);

//        $data=$banner->toArray();
//        unset($data['delete_time']);
//        $banner->hidden(['update_time']);
        if(!$banner){
            throw new BannerMissException(); //不是抛BaseException的好处是 BannerMissException里面直接写好了$code $msg $errorCode 我们不需要重新赋值这三个值了
        }
        return $banner;
    }


}