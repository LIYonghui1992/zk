<?php
/**
 * Created by PhpStorm.
 * User: MyPC
 * Date: 2017/11/20
 * Time: 23:49
 */

namespace app\api\controller\v1;
use app\api\model\Category as CategoryModel;
use app\lib\exception\CategoryException;

class Category
{
    public function getAllCategories(){
        $categories=CategoryModel::all([],'img');
        if(!$categories){
            throw new CategoryException();
        }
        return $categories;
    }
}