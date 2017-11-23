<?php
/**
 * Created by PhpStorm.
 * User: MyPC
 * Date: 2017/11/20
 * Time: 20:51
 */

namespace app\api\controller\v1;
use app\api\validate\Count;
use app\api\model\Product as ProductModel;
use app\api\validate\IDMustBePostiveInt;
use app\lib\exception\ProductException;
use think\Exception;
//创建类和接口  写路由规则 写参数验证 写Model（包含关联关系） 控制器接口调用Model 写异常处理类
class Product
{
    public function getRecent($count=15){
        (new Count())->goCheck();
        $products=ProductModel::getMostRecent($count);
        //数据集是一个对象
        if($products->isEmpty()){
            throw new ProductException();
        }
//        $collection=collection($products);
//        $products=$collection->hidden(['summary']);
        $products=$products->hidden(['summary']);
        return $products;
    }

    public function getAllInCategory($id){
        (new IDMustBePostiveInt())->goCheck();
        $products=ProductModel::getProductsByCategoryID($id);
        if($products->isEmpty()){
            throw new ProductException();
        }
        $products=$products->hidden(['summary']);
        return $products;
    }

    public function getOne($id){
        (new IDMustBePostiveInt())->goCheck();
        $product=ProductModel::getProductDetail($id);
        if(!$product){
            throw new ProductException();
        }
        return $product;
    }
}