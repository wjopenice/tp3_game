<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

/**
 * 前台公共库文件
 * 主要定义前台公共函数库
 */


/**
 * 检测用户是否登录
 * @return integer 0-未登录，大于0-当前登录用户ID
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function is_login_promote(){
    $user = session('promote_auth');
    if (empty($user)) {
        return 0;
    } else {
        return session('promote_auth_sign') == data_auth_sign($user) ? $user['pid'] : 0;
    }
}

//生成订单号
function build_order_no(){
        return date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
    }
function get_pay_sett($id){
    switch ($id) {
        case 0:
        return "未提现";
            break;

        case 1:
        return "已提现";
            break;
        
    }
}
function get_pro_admin($id)
{
    $map['id']=$id;
    $pro=M("promote","tab_")->where($map)->find();
    if($pro){
        return $pro['admin_id'];
    }else{
        return false;
    }
}
function get_limts_status($type){
   switch ($type) {
       case 0:
           return "未充值";
           break;
       case 1:
           return "已充值";
           break;        
       default:
           return "未充值";
           break;
   }
}

//二级代理列表
function agency_list()
{
    $map['parent_id']=get_pid();
    $promote=M("promote","tab_")->where($map)->select();
    if(null==$promote){
        return false;
    }else{
        return $promote;
    }
}
/**
 * 检测验证码
 * @param  integer $id 验证码ID
 * @return boolean     检测结果
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function check_verify($code, $id = 1){
	$verify = new \Think\Verify();
	return $verify->check($code, $id);
}

/**
 * 获取列表总行数
 * @param  string  $category 分类ID
 * @param  integer $status   数据状态
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function get_list_count($category, $status = 1){
    static $count;
    if(!isset($count[$category])){
        $count[$category] = D('Document')->listCount($category, $status);
    }
    return $count[$category];
}

/**
 * 获取段落总数
 * @param  string $id 文档ID
 * @return integer    段落总数
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function get_part_count($id){
    static $count;
    if(!isset($count[$id])){
        $count[$id] = D('Document')->partCount($id);
    }
    return $count[$id];
}

/**
 * 获取导航URL
 * @param  string $url 导航URL
 * @return string      解析或的url
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function get_nav_url($url){
    switch ($url) {
        case 'http://' === substr($url, 0, 7):
        case '#' === substr($url, 0, 1):
            break;        
        default:
            $url = U($url);
            break;
    }
    return $url;
}
// 获取游戏名称
function get_game_name($game_id=null,$field='id'){
    $map[$field]=$game_id;
    $data=M('Game','tab_')->where($map)->find();
    if(empty($data)){return false;}
    return $data['game_name'];
}
function get_apply_dow_url($game_id=0,$promote_id=0)
{
    $model = M('Apply','tab_');
    $map['game_id'] = $game_id;
    $map['promote_id'] = $promote_id;
    $data = $model->where($map)->find();
    if(empty($data['dow_url'])){
        $game_address = M('game','tab_')->field('game_address')->where('id='.$game_id)->find();
        return $game_address['game_address'];
    }
    return $_SERVER['HTTP_HOST'].$data['dow_url'];
}
//获取转移平台总数
function get_balance_ico()
{
    $map['promote_id']=get_pid();
    $pay_agent=M("PayAgents","tab_")->where($map)->sum("amount");

    return $pay_agent==null?0:$pay_agent;
}
function get_yi_balance()
{
    $map['id']=get_pid();
    $pro=M("promote","tab_")->where($map)->find();
    return $pro['balance_coin'];
}
function get_yi_bind_balance($game_id)
{
    $map['promote_id']=get_pid();
    $map['game_id']=$game_id;
    $pro=M("promote_game","tab_")->where($map)->find();
    return $pro['bind_balance'];
}
//获取属于该推广的所有账号（不包含二级）
 function get_all_user($id)
{
    $map['promote_id']=$id;
    $user=M("user","tab_")->where($map)->select();
    if(empty($user)){
        return false;
    }else{
        return $user;
    }
}
//获取用户名
function get_user_name($id){
$map['id']=$id;
$user=M("user","tab_")->where($map)->find();
if(empty($user)){
    return false;
}else{
    return $user['account'];
}
}

function get_typo($id)
{
    switch ($id) {
        case 0:
            return "二级渠道";
            break;
        case 1:
            return "用户帐号";
            break;
        
        default:
              return "未知";
            break;
    }
}
function get_pay_status($type){
    switch ($type) {
        case 0:
              return "失败";
            break;

        case 1:
              return "成功";
            break;
        
        default:
                return "失败";
             break;
    }

}
//获取整条字符串汉字拼音首字母
function pinyin_long($zh){  
    $ret = "";
    $s1 = iconv("UTF-8","gb2312", $zh);
    $s2 = iconv("gb2312","UTF-8", $s1);
    if($s2 == $zh){$zh = $s1;}
    for($i = 0; $i < strlen($zh); $i++){
        $s1 = substr($zh,$i,1);
        $p = ord($s1);
        if($p > 160){
            $s2 = substr($zh,$i++,2);
            $ret .= getfirstchar($s2);
        }else{
            $ret .= $s1;
        }
    }
    return $ret;
}
//获取单个汉字拼音首字母。注意:此处不要纠结。汉字拼音是没有以U和V开头的
function getfirstchar($s0){   
    $fchar = ord($s0{0});
    if($fchar >= ord("A") and $fchar <= ord("z") )return strtoupper($s0{0});
    $s1 = iconv("UTF-8","gb2312", $s0);
    $s2 = iconv("gb2312","UTF-8", $s1);
    if($s2 == $s0){$s = $s1;}else{$s = $s0;}
    $asc = ord($s{0}) * 256 + ord($s{1}) - 65536;
    if($asc >= -20319 and $asc <= -20284) return "A";
    if($asc >= -20283 and $asc <= -19776) return "B";
    if($asc >= -19775 and $asc <= -19219) return "C";
    if($asc >= -19218 and $asc <= -18711) return "D";
    if($asc >= -18710 and $asc <= -18527) return "E";
    if($asc >= -18526 and $asc <= -18240) return "F";
    if($asc >= -18239 and $asc <= -17923) return "G";
    if($asc >= -17922 and $asc <= -17418) return "H";
    if($asc >= -17922 and $asc <= -17418) return "I";
    if($asc >= -17417 and $asc <= -16475) return "J";
    if($asc >= -16474 and $asc <= -16213) return "K";
    if($asc >= -16212 and $asc <= -15641) return "L";
    if($asc >= -15640 and $asc <= -15166) return "M";
    if($asc >= -15165 and $asc <= -14923) return "N";
    if($asc >= -14922 and $asc <= -14915) return "O";
    if($asc >= -14914 and $asc <= -14631) return "P";
    if($asc >= -14630 and $asc <= -14150) return "Q";
    if($asc >= -14149 and $asc <= -14091) return "R";
    if($asc >= -14090 and $asc <= -13319) return "S";
    if($asc >= -13318 and $asc <= -12839) return "T";
    if($asc >= -12838 and $asc <= -12557) return "W";
    if($asc >= -12556 and $asc <= -11848) return "X";
    if($asc >= -11847 and $asc <= -11056) return "Y";
    if($asc >= -11055 and $asc <= -10247) return "Z";
    return NULL;
    //return $s0;
}
