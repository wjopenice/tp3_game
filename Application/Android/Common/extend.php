<?php

//公共函数库

/**
 * 根据获取游戏名称
 * @author zdd
 * @param game_id int 游戏id
 * @return 游戏名称
 */
function get_game_name($game_id=null,$field='id')
{
    $map[$field]=$game_id;
    $data=M('Game','tab_')->where($map)->find();
    if(empty($data)){return false;}
    return $data['game_name'];
}
/**
 * 根据获取id获取包名
 * @author zdd
 * @param game_id int 游戏id
 * @return 游戏包名
 */
function get_apk_pck_name($game_id='')
{
    if(!$game_id)
    {
        return false;
    }
    $where['game_id'] = $game_id;
    $apk_pck_name = M('Game_set','tab_')->where($where)->getField('apk_pck_name');
    return $apk_pck_name;
}

/**
 * 验证手机号是否绑定
 * @author sunhao
 * @param phone int 手机号
 * @return bool
 */
function check_bind_phone($phone) 
{
    $user = M('user','tab_');
    $user_info = $user->where("phone = '$phone'")->find();
    //已绑定
    if($user_info)
    {
        return true;
    }

    return false;
}

/**
 * 验证帐号与手机号是否匹配
 * @author sunhao
 * @param phone int 手机号
 * @param account string 帐号
 * @return bool
 */
function check_account_phone($phone, $account) 
{
    $user = M('user','tab_');

    $where['account'] = $account;
    $user_info = $user->where($where)->find();
    
    if($user_info)
    {   
        //手机号存在
        if($user_info['phone']){
            if ($user_info['phone']==$phone) {
                return 1;
            } else {
                //用户名和手机号不匹配
                return -502;
            }
            
        }else{
            //该用户未绑定手机号
            return -501;
        }
    }else{
        //该用户不存在
        return -500;
    }
     
}

/**
 * 发送手机验证码
 * @author sunhao
 * @param phone int 手机号
 * @param delay int 超时时间
 * @return bool
 */
function send_sms($phone = null, $delay = 10)
{
    $xigu = new \Org\XiguSDK\Xigu(C('sms_set.smtp'));

    //产生手机安全码并发送到手机且存到session
    $rand = rand(100000,999999);
    $param = $rand.",".$delay;
    $result = json_decode($xigu->sendSM(C('sms_set.smtp_account'),$phone,C('sms_set.smtp_port'),$param),true);

    $result['create_time'] = time();
    $result['pid'] = 0;
    $result['status'] = $result['status'] ? $result['status'] : 0;
    $result['ratio'] = $result['ratio'] ? $result['ratio'] : 0;
    //$result['create_ip'] = get_client_ip();
    $r = M('Short_message')->add($result);

    #TODO 短信验证数据 
    if($result['send_status'] == '000000')
    {
        $session_data = array();
        $session_data['code'] = $rand;
        $session_data['phone'] = $phone;
        $session_data['time'] = $result['create_time'];
        $session_data['delay'] = $delay;
        session('app_sms_phonecode', $session_data);
        session($phone, array('code'=>$rand, 'create_time'=>NOW_TIME));

        //写数据库
        M('phonecode','tab_')->add($session_data);

        return true;
    }
    else
    {
        return false;
    }
}

/**
 * 验证输入手机验证码
 * @author sunhao
 * @param phone int 手机号
 * @param code int 输入验证码
 * @return bool
 */
function sms_verify($phone="" ,$code="")
{
    $session_name = "app_sms_phonecode";
    $session = session($session_name);
    if(empty($session))
    {
        //数据获取失败！
        return -1;
    }

    #验证码是否超时
    $time = (time() - $session['time']) / 60;
    if($time > $session['delay'])
    {
        session($session_name, null);
        unset($session);

        //验证超时！请重新获取
        return -2;
    }

    #验证短信验证码
    if($session['code'] != $code)
    {
        //输入验证码不正确
        return -3;
    }

    return 1;
}

/**
 * 数据库验证输入手机验证码
 * @author sunhao
 * @param phone int 手机号
 * @param code int 输入验证码
 * @return bool
 */
function sms_verify_for_db($phone="" ,$code="")
{
    $where['phone'] = $phone;
    $session = M('phonecode','tab_')->where($where)->order('time desc')->find();

    if(empty($session))
    {
        //数据获取失败！
        return -1;
    }

    #验证码是否超时
    $time = (time() - $session['time']) / 60;
    if($time > $session['delay'])
    {
        $session_name = "app_sms_phonecode";
        session($session_name, null);
        unset($session);

        //验证超时！请重新获取
        return -2;
    }

    #验证短信验证码
    if($session['code'] != $code)
    {
        //输入验证码不正确
        return -3;
    }

    M('phonecode','tab_')->where($where)->delete();

    return 1;
}

/**
 * 检测数据
 * @param string $type,string $value
 */
function check_data($type, $value)
{
	switch($type)
	{
		//用户名
		case 'username':
			if (!preg_match ('/^[a-zA-Z]+[A-Za-z0-9\_]{5,14}$/',$value))
			{
				return FALSE;
			}
			break;
		//邮箱
		case '2':
			if ( ! (strlen($value) > 6 && preg_match('/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/', $value)) )
			{
				return FALSE;
			}
			break;
		//手机号
		case 'phone':
			if(!preg_match('/^1\d{10}$/',$value))
			{
				return FALSE;
			}
			break;
		//真实姓名
		case 'realname':
			if(!preg_match("/^([\xe4-\xe9][\x80-\xbf]{2}){2,4}$/",$value))
			{
				return FALSE;
			}
			break;
		//身份证号码
		case 'idcard':
			$result = \Org\Util\IdCard::check_card($value);
			if($result !== 1)
			{
				return FALSE;
			}
            break;
        case 'password':
            if(!preg_match("/^(?![0-9]+$)[0-9A-Za-z]{6,15}$/",$value))
            {
                return FALSE;
            }
			break;
		default:
			return false;
	}
    
	return TRUE;
}

function user_info($user_id)
{
    if(empty($user_id)) {
        return array();
    }

    $where['id'] = $user_id;
    $user_info = M('User','tab_')->field('id,account,nickname,phone')->where($where)->find();

    return $user_info;
}







