<?php

//公共函数库

/**
 * 自动产生快发用户名
 * @author whh
 * @param len  用户名长度
 * @return 用户名
 */
function get_verify_kfusername($len=8)
{   
    $where['account'] = sp_random_string($len);
    $is = M('User','tab_')->where($where)->find();
    if($is)
    {
         unset($where['account']);
         get_verify_kfusername($len=8);
    }else
    {
         return $where['account'];
    } 
}


/**
 * 查看当前快发id是否已被绑定
 * @author whh
 * @param len  用户名长度
 * @return 用户名
 */
function is_bindid($kfUserid)
{   
    $where['bindid'] = $kfUserid;
    $is = M('User','tab_')->where($where)->find();
    if($is)
    {
         return $is['id'];
    }else
    {
         return false;
    } 
}

/**
 * 查看spend表中的定单是否已存在
 * @author whh
 * @param len  用户名长度
 * @return 用户名
 */
function is_ordernum($out_trade_no,$user_id,$game_id)
{   
    $where['order_number'] = $out_trade_no;
    $where['user_id'] = $user_id;
    $where['game_id'] = $game_id;
    $is = M('spend','tab_')->where($where)->find();
    if($is)
    {
         return $is;
    }else
    {
         return false;
    } 
}
