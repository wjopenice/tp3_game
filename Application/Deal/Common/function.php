<?php

function pkcs5_pad($text, $blocksize)
{
    $pad = $blocksize - (strlen($text) % $blocksize);
    return $text . str_repeat(chr($pad), $pad);
}
function pkcs5_unpad($text)
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

function des_encode($a, $key){
    $td = mcrypt_module_open(MCRYPT_DES, '', MCRYPT_MODE_ECB, '');
    mcrypt_generic_init($td, $key, ""); //ECB算法不需要IV
    $b = mcrypt_generic($td, pkcs5_pad($a, 8));
    mcrypt_generic_deinit($td);
    mcrypt_module_close($td);
    return $b;
}

function des_decode($b, $key){
    $td = mcrypt_module_open(MCRYPT_DES, '', MCRYPT_MODE_ECB, '');
    mcrypt_generic_init($td, $key, "");
    $a = pkcs5_unpad(mdecrypt_generic($td, $b));
    mcrypt_generic_deinit($td);
    mcrypt_module_close($td);
    return $a;
}

function aes_encode($a, $key){
    $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, '');
    $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_DEV_URANDOM);
    mcrypt_generic_init($td, $key, $iv);
    $b = mcrypt_generic($td, pkcs5_pad($a, 8));
    mcrypt_generic_deinit($td);
    mcrypt_module_close($td);
    return $b;
}
/**
 * 对接8868使用的密码解密函数
 * $key 解密key固定
 */
function aes_decode($b, $key='078a34201e6d3ea5'){
    $b = hex2bin($b);
    $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, '');
    $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_DEV_URANDOM);
    mcrypt_generic_init($td, $key, $iv);
    $a = pkcs5_unpad(mdecrypt_generic($td, $b));
    mcrypt_generic_deinit($td);
    mcrypt_module_close($td);
    return $a;
}

/*$a = "123456";
$key = "078a34201e6d3ea5";


$b = aes_encode($a, $key);
echo "encoded: ".base64_encode($b), "\n";


$b = "5a8720d4246795182526e2fb24238211";
$a = aes_decode($b, $key);
echo "decoded: ".$a, "\n";*/

/**
     * 验证签名
     */
     function vcodeVerify($vcode_now){
        $vcode = strtolower(I('post.vcode'));
            if($vcode != strtolower($vcode_now)){
                 file_put_contents('/home/wwwroot/www.u7858.com/Application/Deal/Common/b.txt',$vcode.'######'.$vcode_now.'---------',FILE_APPEND);
                $msg='签名验证失败';
                toBack('false',$msg,5004);
            }
    }
    /**
     * 验证接口
     */
    function cmdVerify($cmd_now){
        $cmd = strtolower(I('post.cmd'));
            if($cmd != strtolower($cmd_now)){
                $msg='接口验证失败';
                toBack('false',$msg,5003);
            }
    }
    /**
     * 获取加密字符串
     */
   function getVcode($p1='',$p2='',$p3='',$p4='',$p5='',$p6=''){
   
        return md5($p1.$p2.$p3.$p4.$p5.$p6);
        
    }
    /**
     * 定义统一返回数据
     */
     function toBack($success='true',$msg='ok',$errCode='',$mobile=''){
        $resArr['success']=$success;
        $resArr['msg']=$msg;
        if(!empty($errCode)){
            $resArr['errCode']=$errCode;
        }
        if(!empty($mobile)){
            $resArr['mobile']=$mobile;
        }
        
        echo json_encode($resArr);
        exit();
    }

