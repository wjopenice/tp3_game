<?php
/**
 * 后台公共文件扩展
 * 主要定义后台公共函数库
 */
//获取渠道类型
function get_type_move($type){
    switch ($type) {
        case 0:
            return '二级渠道';
            break;
        case 1:
            return '用户';
            break;
        default:
            return '未知类型';
            break;
    }
}
//获取游戏ID
function get_game_id($name){
    $game=M('game','tab_');
    $map['game_name']=$name;
    $data=$game->where($map)->find();
    if($data['id']==null){
        return false;
    }
    return $data['id'];
}
function appid_get_game_name($appid){
    $game=M('game','tab_');
    $map['game_appid']=$appid;
    $data=$game->where($map)->find();
    if($data['game_name']==null){
        return false;
    }
    return $data['game_name'];
}
// lwx 多选字段值判断
function check_field_value($field,$key) {
    if(empty($field) || empty($key)){
        return false;
    }
    $field = explode(",",$field);
    
    if (in_array($key,$field)) {
        return true;
    } else {
        return false;
    }
    
}

//获取渠道自然流水开启情况
function get_promote_alipayway_sign($alipayway_sign=0){
    switch ($alipayway_sign) {
        case 0:
            return '关闭';
            break;
        case 1:
            return '开启';
            break;
        default:
            return '未知';
            break;
    }
} 

// lwx 获得所有开放类型
function get_opentype_all() {
    
    $list = M("Opentype","tab_")->where("status=1")->select();

    if (empty($list)) {return '';}

    return $list;
}
 
// lwx 获得所有游戏类型
function get_game_type_all() {

     $list = M("Game_type","tab_")->where("status=1")->order('sort desc')->select();

    if (empty($list)) {return '';}

    return $list;

}
// lwx 获得所有要展示的游戏类型
function get_game_type_all_show() {

     $list = M("Game_type","tab_")->where("status_show=1")->order('sort desc')->select();

    if (empty($list)) {return '';}

    return $list;

}
// lwx 获得所有游戏
function get_game_id_all()

 {

    $game = M("game","tab_");

    $map['game_status'] = 1;

    $lists = $game->field("id,game_name")->where($map)->select();

    if(empty($lists)){return false;}

    return $lists;

 }

/**
 * 获取游戏列表
 * @return array，false
 * @author 小纯洁 
 */
 function get_game_list()
 {
    $game = M("game","tab_");
    // $map['game_status'] = 1;
    $lists = $game->where($map)->select();
    if(empty($lists)){return false;}
    return $lists;
 }
/**
 * 获取代充折扣游戏列表
 * @return array，false
 * @author zdd
 */
 function get_game_discount_list()
 {
    $game = M("Charge","tab_");
    $lists = $game
            ->group('tab_charge.game_id')
            ->join('tab_game')
            ->field('tab_game.id as game_id,tab_game.game_name')
            ->where('tab_game.id=tab_charge.game_id')
            ->select();
    if(empty($lists)){return false;}
    return $lists;
 }
 /**
 * 获取APP图片位置信息
 * @return array，false
 * @author whh 
 */
 function get_applocation_list()
 {
    $app = M("appimage","tab_");
    // $map['game_status'] = 1;
    $lists = $app->where($map)->group('pos_id')->select();
    if(empty($lists)){return false;}
    return $lists;
 }


 /**
 * 用户消费记录获取游戏列表
 * @return array，false
 * @author whh 
 */
 function get_game1_list()
 {
    $game = M("spend","tab_");
    // $map['game_status'] = 1;
    $lists = $game->where($map)->group('game_name')->select();
    if(empty($lists)){return false;}
    return $lists;
 }

 /**
 * 获取管理员列表
 * @return array，false
 * @author whh 
 */
 function get_member_list()
 {
    $member = M("member","sys_");
    // $map['game_status'] = 1;
    $lists = $member->where($map)->select();
    if(empty($lists)){return false;}
    return $lists;
 }


/**
*游戏区服名称
*/
function get_area_name($area_id= null){
    if(empty($area_id)){return false;}
    $area_model = D('Server');
    $map['server_num'] = $area_id;
    $name = $area_model->where($map)->find();
    if(empty($name['server_name'])){return false;}
    return $name['server_name'];
}
/**
 * 获取对应游戏类型的文字信息
 */
function get_game_type($type = null){
    if(!isset($type)){
        return false;
    }
    $cl = M("game_type","tab_")->where("status=1 and id=$type")->limit(1)->select();
    return $cl[0]['type_name'];
}
/**
*获取推广员列表
*@return array
*@author 小纯洁
*/
 function get_promote_list(){
    $promote = M("promote","tab_");
    $map['status'] = 1;
    $data = $promote->where($map)->select();
    if(empty($data)){return false;}
    return $data;
 }

 /**
 * 获取渠道游戏列表
 * @return array，false
 * @author whh 
 */
 function get_promote_game_list($promote_id)
 {
    $apply = M("apply","tab_");
    $map['a.promote_id']=$promote_id;
    $map['a.status']=1;
    $map['b.game_status'] = 1;
    $lists = $apply
           ->alias('a')
           ->field('b.id,b.game_name,a.promote_id,a.promote_account')
           ->join('left join tab_game as b on a.game_id=b.id ')
           ->where($map)
           ->select();
    if(empty($lists)){return false;}
    return $lists;
 }
 
 /**
*检查链接地址是否有效
*/
function varify_url($url){  
    $check = @fopen($url,"r");  
    if($check){  
     $status = true;  
    }else{  
     $status = false;  
    }    
    return $status;  
} 
//获取推广员父类id
function get_fu_id($id){
    $map['id']=$id;
    $pro=M("promote","tab_")->where($map)->find();
    if(null==$pro||$pro['parent_id']==0){
        return 0;
    }else{
    return $pro['parent_id'];
    }
}
function get_parent_name($id){
    $map['id']=$id;
    $pro=M("promote","tab_")->where($map)->find();
     if(null!=$pro&&$pro['parent_id']>0){
        $pro_map['id']=$pro['parent_id'];
        $pro_p=M("promote","tab_")->where($pro_map)->find();
        return $pro_p['account'];
     }else if($pro['parent_id']==0){
        return $pro['account'];
     }else{
        return false;
     }
}
/**
*获取推广员类型 一级 二级
*/
 function get_promote_type($id=0){
    $promote = M("Promote","tab_");
    $map["id"] = $id;
    $data = $promote->where($map)->find();
    if(empty($data)){return false;}
    $str="";
    switch ($data['parent_id']) {
        case 0:
            $str = "一级渠道";
            break;
        
        default:
           $str = "二级渠道";
            break;
    }
    
    return $str;
 }

 /**
*获取推广员父类账号
*@param  $promote_id 推广id
*@param  $isShow bool 
*@return string
*@author whh
*/
 function get_parent_promotename($parent_id=0,$isShwo=true)
 {
    $promote = M("promote","tab_");
    $map['id'] = $parent_id;
    $data = $promote->where($map)->find();
    if(empty($data)){return false;}
    $result = "";
    if($isShwo){
        $result = "[{$data['account']}]";
    }
    else{
        $result = $data['account'];
    }
    return $result;
 }


 /**
*获取推广员账号
*@param  $promote_id 推广id
*@return string
*@author 小纯洁
*/
 function get_promote_name($prmote_id=0)
 {
    $promote = M("promote","tab_");
    $map['id'] = $prmote_id;
    $data = $promote->where($map)->find();
    if(empty($data)){return '自然注册';}
    if(empty($data['account'])){return "未知推广";}
    $result = $data['account'];
    return $result;
 }


/**
*获取推广员父类账号
*@param  $promote_id 推广id
*@param  $isShow bool 
*@return string
*@author 小纯洁
*/
 function get_parent_promote($prmote_id=0,$isShwo=true)
 {
    $promote = M("promote","tab_");
    $map['parent_id'] = $prmote_id;
    $data = $promote->where($map)->find();
    if(empty($data)){return false;}
    $result = "";
    if($isShwo){
        $result = "[{$data['account']}]";
    }
    else{
        $result = $data['account'];
    }
    return $result;
 }

/**
*获取推广员子账号
*/
 function get_prmoote_chlid_account($id=0){
    $promote = M("promote","tab_");
    $map['status'] = 1;
    $map["parent_id"] = $id;
    $data = $promote->where($map)->select();
    if(empty($data)){return "";}
    return $data;
 }
/**
*获取推广员子账号
*/
 function get_prmoote_chlid_account1($id=0,$account=null){
    $promote = M("promote","tab_");
    $map['status'] = 1;
    $map["parent_id"] = $id;
    $data = $promote->where($map)->select();

    $map1['status'] = 1;
    $map1["id"] = $id;
    $data1 = $promote->where($map1)->select();
    $data2=array_merge($data,$data1);
    
    if(empty($data2)){return "";}
    return $data2;
 }
 /**
*获取推广员子用户
*/
 function get_prmoote_chlid_user($id=0){
    $user = M("user","tab_");
    //$map['status'] = 1;
    $map["promote_id"] = $id;
    $data = $user->where($map)->select();
    if(empty($data)){return "";}
    return $data;
 }

/**
*获取管理员昵称
*/
 function get_admin_name($id=0){
    $data = M("Member")->find($id);
    if(empty($data)){return "";}
    return $data['nickname'];
 }

 
 /**
 *获取用户实体
 */
 function get_user_entity($id=0,$isAccount = false){
    $user = M('user',"tab_");
    if($isAccount){
        $map['account'] = $id;
        $data = $user->where($map)->find();
    }
    else{
        $data = $user->find($id);
    }
    if(empty($data)){
        return false;
    }
    return $data;
 }

/**
*设置状态文本
*/
 function get_status_text($index=1,$mark=1){
    $data_text = array(
        0  => array( 0 => '失败' ,1 => '成功'),
        1  => array( 0 => '锁定' ,1 => '正常'),
        2  => array( 0 => '未申' ,1 => '已审' , 2 => '拉黑'),
    );
    return $data_text[$index][$mark];
 }


/**
* 生成唯一的APPID
* @param  $str_key 加密key
* @return string
* @author 小纯洁 
*/
function generate_game_appid($str_key=""){
    $guid = '';  
    $data = $str_key;  
    $data .= $_SERVER ['REQUEST_TIME'];     
    $data .= $_SERVER ['HTTP_USER_AGENT']; 
    $data .= $_SERVER ['SERVER_ADDR'];       
    $data .= $_SERVER ['SERVER_PORT'];      
    $data .= $_SERVER ['REMOTE_ADDR'];     
    $data .= $_SERVER ['REMOTE_PORT'];     
    $hash = strtoupper ( hash ( 'MD4', $guid . md5 ( $data ) ) ); //ABCDEFZHIJKLMNOPQISTWARY
    $guid .= substr ( $hash, 0, 9 ) . substr ( $hash, 17, 8 ) ; 
    return $guid;
}


/**
*随机生成字符串
*@param  $len int 字符串长度
*@return string
*@author 小纯洁
*/
function sp_random_string($len = 6) {
    $chars = array(
        "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
        "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
        "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G",
        "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R",
        "S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2",
        "3", "4", "5", "6", "7", "8", "9"
    );
    $charsLen = count($chars) - 1;
    shuffle($chars);    // 将数组打乱
    $output = "";
    for ($i = 0; $i < $len; $i++) {
        $output .= $chars[mt_rand(0, $charsLen)];
    }
    return $output;
}

//判断支付设置
//yyh
function pay_set_status($type){
    $sta=M('tool','tab_')->field('status')->where(array('name'=>$type))->find();
    return $sta['status'];
}
//根据用户名称获取用户的id
function get_user_id($account){
    $map['account']=$account;
    $user=M("User",'tab_')->where($map)->find();
    return $user['id'];
}
/**
 *获取渠道实体
 */
function get_promote_entity($pid=0,$isAccount = false){
    $promote = M('promote',"tab_");
    if($isAccount){
        $map['account'] = $pid;
        $data = $promote->where($map)->find();
    }
    else{
        $data = $promote->find($pid);
    }
    if(empty($data)){
        return false;
    }
    return $data;
}
//获取agent支付方式 zdd_20170512
function get_pay_way_agent($id=null)
{
    if(!isset($id)){
        return false;
    }
    switch ($id) {
        case 0:
          return "支付宝";
            break;
        case 1:
          return "支付宝";
            break;
        case 2:
          return "微信";
            break;
        case 3:
          return "平台币";
            break;
        default:
            return "所有类型";
            break;
    }
}
//获取agent支付方式 zdd_20170512
function get_pay_way_spend($id=null)
{
    if(!isset($id)){
        return false;
    }
    switch ($id) {
        case 0:
          return "平台币";
            break;
        case 1:
          return "支付宝";
            break;
        case 2:
          return "微信";
            break;
        default:
            return "所有类型";
            break;
    }
}
//获取账号类型 zdd_20170512
function get_account_type($id=null)
{
    if(!isset($id)){
        return false;
    }
    switch ($id) {
        case 1:
          return "用户";
            break;
        case 2:
          return "渠道";
            break;
        default:
            return "未知";
            break;
    }
}

/**
 * 获取游戏序列化列表
 * @return array
 * @author sunhao updateby 20170712
 */
function format_get_game_list()
{
    //游戏列表
    $game_list = get_game_list();
    if(empty($game_list)) {
        return array();
    }

    $format_game_list = array();
    foreach($game_list as $key => $value) {
        $format_game_list[$value['id']] = $value['game_name'];
    }

    return $format_game_list;
}

/**
 * 所有推广员后台信息
 * @return array
 * @author sunhao updateby 20170712
 */
function get_promote_admin_info()
{
    //管理员列表
    $format_member_list = array();
    $member_list = get_member_list();
    foreach($member_list as $key => $value) {
        $format_member_list[$value['uid']]['nickname'] = $value['nickname'];
    }

    //推广员列表
    $promote_admin_info = array();
    $promote_list = get_promote_list();
    foreach($promote_list as $key => $value) {
        $promote_admin_info[$value['id']]['nickname'] = $format_member_list[$value['admin_id']]['nickname'];
        $promote_admin_info[$value['id']]['promote_account'] = $value['account'];
    }

    return $promote_admin_info;
}
//根据渠道id获取渠道名称
function get_promote_account_by_id($promote_id)
{
    if($promote_id)
    {
        $where['id'] = $promote_id;
        $promote_account = M('Promote','tab_')->where($where)->getField('account');
    }else
    {
        $promote_account = '';
    }
    return $promote_account;
}