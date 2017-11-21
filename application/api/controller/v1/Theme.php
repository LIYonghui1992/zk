<?php
/**
 * Created by PhpStorm.
 * User: MyPC
 * Date: 2017/11/19
 * Time: 12:56
 */

namespace app\api\controller\v1;

use app\api\validate\IDCollection;
use app\api\validate\IDMustBePostiveInt;
use think\Request;
use think\Controller;
use app\api\model\Theme as ThemeModel;
class Theme
{
    /**
     * @url /theme?ids=id1,id2,id3,...
     * @return 一组theme模型
     */
    public function getSimpleList($ids=''){
        (new IDCollection())->goCheck();
        $ids=explode(',',$ids);
        $result=ThemeModel::with('topicImg,headImg')->select($ids);
        if($result->isEmpty()){
            throw new ThemeException(); //不是抛BaseException的好处是 BannerMissException里面直接写好了$code $msg $errorCode 我们不需要重新赋值这三个值了
        }
        return $result;
    }

    public function getComplexOne($id){
        (new IDMustBePostiveInt())->goCheck();
        $theme= ThemeModel::getThemeWithProducts($id);
        if(!$theme){
            throw new ThemeException(); //不是抛BaseException的好处是 BannerMissException里面直接写好了$code $msg $errorCode 我们不需要重新赋值这三个值了
        }
        return $theme;
    }
}