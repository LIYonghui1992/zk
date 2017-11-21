<?php
/**
 * Created by 七月.
 * Author: 七月
 * Date: 2017/4/18
 * Time: 5:15
 */

namespace app\api\validate;


use think\Exception;
use think\Request;
use think\Validate;
use app\lib\exception\ParameterException;
use app\lib\exception\BaseException;
class BaseValidate extends Validate
{
    public function goCheck()
    {
        // 获取http传入的参数
        // 对这些参数做检验
        $request = Request::instance();
        $params = $request->param();

        $result = $this->batch()->check($params);
        if(!$result){
            $e=new ParameterException([
                'msg'=>$this->error,
//                'code'=>400,
//                'errorCode'=>10002
            ]); //直接用BaseException 也可以 只不过$error和$errorCode 是用的BaseException的 而不是用的ParameterException的
//            $e=new BaseException();
//            $e->msg=$this->error;
            throw $e;
//            $error = $this->error;
//            throw new Exception($error);
        }
        else{
            return true;
        }
    }

    protected function isPositiveInteger($value, $rule = '',
                                         $data = '', $field = '')
    {
        if (is_numeric($value) && is_int($value + 0) && ($value + 0) > 0) {
            return true;
        }
        else{
            return false;
        }
    }

    protected function isNotEmpty($value, $rule = '',
                                  $data = '', $field = ''){
        if(empty($value)){
            return false;
        }else{
            return true;
        }

    }
}