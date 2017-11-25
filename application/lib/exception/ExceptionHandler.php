<?php
/**
 * Created by PhpStorm.
 * User: lyh
 * Date: 2017/11/8
 * Time: 15:22
 */

namespace app\lib\exception;


use think\Exception;
use think\exception\Handle;
use think\Log;
use think\Request;

class ExceptionHandler extends Handle
{
    private $code;
    private $msg;
    private $errorCode;
    //需要返回客户端当前请求的url路径
    public function render(\Exception $e){
        if($e instanceof BaseException){
            $this->code=$e->code;
            $this->msg=$e->msg;
            $this->errorCode=$e->errorCode;
        }else{
            if(config('app_debug')){
                return parent::render($e);
            }
            $this->code=500;
            $this->msg='服务器内部错误2';
            $this->errorCode=999;
            $this->recordErrorLog($e);
        }
        $request=Request::instance();
        $result=[
            'msg'=>$this->msg,
            'error_code'=>$this->errorCode,
            'request_url'=>$request->url()
        ];
        return json($result,$this->code);

    }

    private function recordErrorLog(\Exception $e){
        //如果config中的Log type="test" 则这里就必须要手动初始化了
        Log::init([
            'type'=>'File',
            'path'=>LOG_PATH,
            'level'=>['error']
        ]);
        Log::record($e->getMessage(),'error');
    }
}