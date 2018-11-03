<?php
namespace Quicksdk\Controller;

use Think\Controller;

//加密类

class EncryptController extends Controller
{
    /**
     * 验证签名
     * @param encrypt 需要加密的字符串或者数组
     * @param md5_sign 加密后的字符串
     * @param md5_key md5加密key
     * @return boole
     * @author zdd
     */
    protected function validation_sign($encrypt="",$md5_sign="")
    {
        $md5Str = $this->encrypt_md5($encrypt);
        //print_r($md5Str.'----'.$md5_sign);exit;
        if($md5Str === $md5_sign)
        {
            return true;
        }else
        {
            //记录错误文件
            $date = date('Y-m-d H:i:s');
            file_put_contents(__DIR__.'/errverfy.txt','date：'.$date.'\n\raccept：'.$md5_sign.'\n\rcompute'.$md5Str.'\n\r',FILE_APPEND);
            return false;
        }
    }
    /**
     *对数组的键值按照Ascll码进行升序排序
     */
    protected function arrSort($para)
    {
        ksort($para);
        reset($para);
        return $para;
    }

    /**
     *MD5验签加密
     */
    protected function encrypt_md5($param="",$md5_key="ab4hjylfnpcrc4"){
        #对数组进行排序拼接
        if(is_array($param))
        {
            //$md5Str = implode($this->arrSort($param));
            //print_r($md5Str);exit;
            $data = $this->arrSort($param);
            //print_r($data);exit;
            foreach ($data as $k => $v) 
            {
                $md5Str = $md5Str.$k.'='.$v.'&';
            }

        }else
        {
            $md5Str = $param.'&';
        }
        $md5_key = md5($md5_key);
        //print_r($md5Str . $md5_key);exit;
        $md5 = md5($md5Str . $md5_key);
        //print_r($md5);exit;
        return '' === $param ? 'false' : $md5;
    }
    /**
     * 加密
     * @author zdd
     */
    protected function pkcs5_pad($text, $blocksize)
    {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }
    /**
     * 解密
     * @author zdd
     */
    protected function pkcs5_unpad($text)
    {
        $pad = ord($text{strlen($text)-1});
        if ($pad > strlen($text))
        {
            return false;
        }
        if( strspn($text, chr($pad), strlen($text) - $pad) != $pad)
        {
            return false;
        }
        return substr($text, 0, -1 * $pad);
    }
    /**
     * des方式加密
     * @author zdd
     */
    protected function des_encode($a, $key)
    {
        $td = mcrypt_module_open(MCRYPT_DES, '', MCRYPT_MODE_ECB, '');
        mcrypt_generic_init($td, $key, ""); //ECB算法不需要IV
        $b = mcrypt_generic($td, $this->pkcs5_pad($a, 8));
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        return $b;
    }
    /**
     * des方式解密
     * @param b string 需要解密的字符串
     * @param key string 解密key
     * @return a string 解密后的字符串
     * @author zdd
     */
    protected function des_decode($b, $key)
    {
        $td = mcrypt_module_open(MCRYPT_DES, '', MCRYPT_MODE_ECB, '');
        mcrypt_generic_init($td, $key, "");
        $a = $this->pkcs5_unpad(mdecrypt_generic($td, $b));
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        return $a;
    }
    /**
     * aes方式加密
     * @param $a string 需要加密的字符串
     * @param $b string 加密后的字符串
     * @author zdd
     */
    protected function aes_encode($a, $key='2at7s9lumkgsq6u3'){
        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, '');
        $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_DEV_URANDOM);
        mcrypt_generic_init($td, $key, $iv);
        $b = mcrypt_generic($td, $this->pkcs5_pad($a, 16));
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $b = bin2hex($b);
        return $b;
    }
    /**
     * aes方式解密
     * 对接8868使用的密码解密函数
     * @param key 解密key固定
     * @return $a aes加密后的字符串
     * @author zdd
     */
    protected function aes_decode($b, $key='2at7s9lumkgsq6u3'){
        $b = hex2bin($b);
        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, '');
        $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_DEV_URANDOM);
        mcrypt_generic_init($td, $key, $iv);
        $a = $this->pkcs5_unpad(mdecrypt_generic($td, $b));
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        return $a;
    }







}