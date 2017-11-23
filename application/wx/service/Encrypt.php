<?php
/**
 * Created by PhpStorm.
 * User: MyPC
 * Date: 2017/11/23
 * Time: 20:54
 */

namespace app\wx\service;
use app\lib\wx\WXBizMsgCrypt;

class Encrypt
{
    /**
     * @param $text 要加密的内容
     * @param $timeStamp 当前时间戳
     * @param $nonce
     * @param $encryptMsg 加密后产生的密文
     */
    public static function encryptMsg($text, $timeStamp, $nonce, $encryptMsg){
        $token = config('wx.token');
        $encodingAesKey = config('wx.EncodingAESKey');
        $appId = config('wx.appId');
        $pc = new WXBizMsgCrypt($token, $encodingAesKey, $appId);
        $encryptMsg = '';
        $errCode = $pc->encryptMsg($text, $timeStamp, $nonce, $encryptMsg);
        if ($errCode == 0) {
            print("加密后: " . $encryptMsg . "\n");
        } else {
            print($errCode . "\n");
        }
    }
    public static function decryptMsg($msg_sign, $timeStamp, $nonce, $from_xml, $msg){
        $token = config('wx.token');
        $encodingAesKey = config('wx.EncodingAESKey');
        $appId = config('wx.appId');
        $pc = new WXBizMsgCrypt($token, $encodingAesKey, $appId);
        $errCode = $pc->decryptMsg($msg_sign, $timeStamp, $nonce, $from_xml, $msg);
        if ($errCode == 0) {
            print("解密后: " . $msg . "\n");
        } else {
            print($errCode . "\n");
        }
    }
}