<?php
/**
 * 后台公共文件扩展
 * 主要定义后台公共函数库
 */
// 获取游戏名称
function get_game_name($game_id=null,$field='id'){
    $map[$field]=$game_id;
    $data=M('Game','tab_')->where($map)->find();
    if(empty($data)){return false;}
    return $data['game_name'];
}


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

function aes_encode($a, $key='2at7s9lumkgsq6u3'){
    $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, '');
    $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_DEV_RANDOM);
    mcrypt_generic_init($td, $key, $iv);
    $b = mcrypt_generic($td, pkcs5_pad($a, 16));
    mcrypt_generic_deinit($td);
    mcrypt_module_close($td);
    $b = bin2hex($b);
    return $b;
}
/**
 * 对接8868使用的密码解密函数
 * $key 解密key固定
 */
function aes_decode($b, $key='2at7s9lumkgsq6u3'){
     //$t12 = microtime(true);
             
    $b = hex2bin($b);
    /*$t13 = microtime(true);
     $t14 = round($t13-$t12,3).'秒';
    $data['user_t14'] = $t14;*/
    $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, '');
     /*$t15 = microtime(true);
     $t16 = round($t15-$t12,3).'秒';
    $data['user_t16'] = $t16;*/
    $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_DEV_URANDOM);
     /*$t17 = microtime(true);
     $t18 = round($t17-$t12,3).'秒';
    $data['user_t18'] = $t18;*/
    mcrypt_generic_init($td, $key, $iv);
    /* $t19 = microtime(true);
     $t20 = round($t19-$t12,3).'秒';
    $data['user_t20'] = $t20;*/
    $a = pkcs5_unpad(mdecrypt_generic($td, $b));
    /* $t21 = microtime(true);
     $t22 = round($t21-$t12,3).'秒';
    $data['user_t22'] = $t22;*/
    mcrypt_generic_deinit($td);
     /*$t23 = microtime(true);
     $t24 = round($t23-$t12,3).'秒';
    $data['user_t24'] = $t24;*/
    mcrypt_module_close($td);
     /*$t25 = microtime(true);
     $t26 = round($t25-$t12,3).'秒';
    $data['user_t26'] = $t26;
    echo json_encode($data);exit;*/
    return $a;
}



/**
     * 验证签名
     */
     function vcodeVerify($vcode_now){
        $vcode = strtolower(I('post.vcode'));
            if($vcode!= strtolower($vcode_now)){
                $date = date('Y-m-d H:i:s');
                 file_put_contents(__DIR__.'/errverfy.txt',$date.'     '.$vcode.'######'.$vcode_now.'---------',FILE_APPEND);
                 return false;
                
            }{
                return true; 
            }
    }


    /**
     * 获取加密字符串
     */
   function getVcode($p1='',$p2='',$p3='',$p4='',$p5='',$p6='',$p7='',$p8='',$p9='',$p10='',$p11='',$p12=''){
        /*$date = date('Y-m-d H:i:s');
        file_put_contents(__DIR__.'/errget.txt',$date.'         '.$p1.$p2.$p3.$p4.$p5.$p6.$p7.$p8.$p9.$p10.$p11.$p12.'---------',FILE_APPEND);*/
        return md5($p1.$p2.$p3.$p4.$p5.$p6.$p7.$p8.$p9.$p10.$p11.$p12);
        
    } 
    








