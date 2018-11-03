<?php
namespace Android\Controller;

use Media\Controller\HomeController;
use User\Api\MemberApi;
use Org\XiguSDK\Xigu;

class UserController extends BaseController
{	
	public $MemberClass;
	public $key="mgwmd5keyapp";
	public $aes_key="2at7s9lumkgsq6u3";
	public $UserClass;
	public $UserModel;
	public function __construct()
	{   
        $this->MemberClass = new HomeController();
        $this->UserClass = new MemberApi();
        $this->UserModel =M('user','tab_');
        parent::__construct();
	}
	
	/**
     * 登录
     * @author whh
     */
    public function login()
    {
        //获取登录信息
        $user = I('post.');
        //去空格
        foreach ($user as $key => $value) 
        {
            $user[$key]=trim($value);
        }
        //判断数据是否为空
        if (empty($user['account']) || empty($user['password']) || empty($user['vcode'])) 
        {   
            //参数不能为空
           $this->output(-901);
        } else 
        {   
            //用户名正则判断
            if(!preg_match("/^[a-zA-Z0-9_]{6,15}$/",$user['account'])){
                //用户名格式不正确
                $this->output(-101);
            }
            //将密码进行aes_decode解密
            //echo $passworded=base64_encode($this->aes_encode($user['password'])); exit;
            $password=$this->aes_decode(base64_decode($user['password']));
            //print_r($password);exit;
            $user['password']=$password;
            $vcode = $user['vcode'];
            unset($user['vcode']);
            //验证签名
            if ($this->validation_sign($user,$vcode)===false) 
            {   
                //验签失败
                $this->output(-100);
            } else 
            {
                //登录
                $game_id=68;
                $game_name="玩转平台手机助手";
                $type=3;#1:游戏登录，2:PC登录，3:APP登录
                $res = $this->UserClass->login_($user['account'],$password,3,$game_id,$game_name);

                //判断登录状态
                if ($res == '-2')
                {
                    //密码错误
                    $this->output(-102);
                } elseif($res == '-1')
                {
                    //用户名不存在或被禁用
                    $this->output(-103);
                } else 
                {   
                    $res['user_account']=$user['account'];
                    $this->login_sign($res,$vcode);

                }
                

            }
        }
    }

    /**
     * 登录或注册记录login_sign
     * @param $res 数组，user_id,user_account
     * @param $vcode 加密后的字符串
     * @author whh
     */
    public function login_sign($res,$vcode)
    {
        //查询登录记录
        $where['user_id'] = $res['user_id'];
        $where['user_account'] = $res['user_account'];
        $flag = M('login_sign','tab_')->where($where)->find();
        $login_sign=md5($vcode.sp_random_string(7));
        $data['login_sign']=$login_sign;
        $data['create_time']=time();
        //判断登录记录是否存在
        if ($flag) 
        {
            $result=M('login_sign','tab_')->where($where)->save($data);
        } else 
        {
            $data['user_id'] = $res['user_id'];
            $data['user_account'] = $where['user_account'];
            $data['create_time'] = time();
            $result = M('login_sign','tab_')->add($data);
        }
        if ($result) 
        {   
            //返回登录表示login_sign
            $msg=array(
                'user_id'=>$res['user_id'],
                'account'=>$res['user_account'],
                'login_sign'=>$login_sign,
                );
            $this->output($msg);
        } else 
        {
            $this->output(-900);
        }
        

    }
    
    /**
     * 忘记密码-手机号找回-验证用户名
     * @author whh
     */
    public function forget_check_account()
    {
        $account=I('post.account');
        if(empty($account))
        {   //参数不能为空
            $this->output(-901);
        }

        if(!preg_match("/^[a-zA-Z0-9_]{6,30}$/",$account))
        {
            //用户名格式不正确
            $this->output(-101);
        }
        
        $vcode = I('post.vcode');
        if($this->validation_sign($account,$vcode) === false)
        {
            //验签失败
            $this->output(-100);
        }else
        {
            $map['account'] = $account;
            $map['lock_status'] = 1;
            $is = $this->UserModel->where($map)->find();
            if (empty($is)) 
            {
                //用户名不存在或被禁用
                $this->output(-103);
            } else {
                if (empty($is['phone'])) 
                {
                    $this->output(-110);
                } else {
                    $data=array(
                     'account' => $account,
                     'phone'   => $is['phone'],
                    );
                    $data['data'] = base64_encode($this->aes_encode($data));
                    $this->output($data);
                }
            }
            
        }

    }
    /**
     * 忘记密码-手机号找回-验证手机号并修改密码
     * @author whh
     */
    public function forget_change_password()
    {
        $password = I('post.password');
        $user_data = I('post.');
        //print_r($user_data);exit;
        $vcode = $user_data['vcode'];
        unset($user_data['vcode']);
        //print_r($user_data);exit;
        if (empty($user_data['account']) || empty($user_data['phone']) || empty($user_data['phonecode']) || empty($user_data['password']) || empty($vcode)) 
        {   //参数不能为空
            $this->output(-901);
        } else {
            
            $password=$this->aes_decode(base64_decode($user_data['password']));
            if(check_data('password',$password)=== false){
                 $this->output(-902);
            }
            $user_data['password']=$password;
            if ($this->validation_sign($user_data,$vcode) === false) 
            {
                //验签失败
                $this->output(-100);
            } else {
                $whereis['account']=$user_data['account'];
                $whereis['phone']=(int)$user_data['phone'];
                $is=$this->UserModel->where($whereis)->find();
                if ($is) {
                    //验证手机验证码
                    $result = sms_verify_for_db($user_data['phone'],$user_data['phonecode']);
                    //验证码输入错误
                    if($result == "-3")
                    {
                        $this->output(-106);
                    }
                    //验证码超时或获取失败
                    else if($result == '-1' || $result == '-2')
                    {
                        $this->output(-107);
                    }

                    //验证通过清除此次验证码session
                    session('app_sms_phonecode', null);
                    //修改密码
                    $save['password']=think_ucenter_md5($user_data['password'],UC_AUTH_KEY);
                    $res=$this->UserModel->where($whereis)->save($save);
                    if ($res !== false) {
                        //修改密码成功
                        $msg['msg']='修改密码成功！';
                        $this->output($msg);
                    } else {
                        //数据更新或插入失败
                        $this->output(-900);
                    }
                    

                } else {
                    //未找到相关数据
                    $this->output(-903);
                }
                
            }
            
        }
        

    }
    /**
     * 客服中心-客服信息
     * @author whh
     */
    public function service()
    {
        $data= array(
            'serverqq1' => C('CH_SET_SERVER_QQ'), 
            'serverqq2' => C('CH_SET_SERVER_QQ1'),
            'phone1'    => C('CH_SET_ZUOJI_PHONE'),
            'phone2'    => C('CH_SET_SERVER_PHONE'),
            'time'      =>'10:00--21:00'
            );
        $this->output($data);
    }

	public function reg()
	{
		$user_data = I('post.');

		//验证注册参数
		$user_data = $this->check_register($user_data);
        //file_put_contents('/home/wwwroot/test.898sm.com/Data/bbbbb.log',$user_data['promote_id'].'----'.$user_data['promote_account'].'---'.'1111');
		//推广参数
		if(!isset($user_data['promote_id'])) {
			$user_data['promote_id'] = 0;
			$user_data['promote_account'] = '自然注册';
		}

        $user_data['register_way']=2;//注册方式app
		switch ($user_data['reg_type'])
		{
			//普通注册
			case '1':
				$this->reg_personl($user_data);
				break;
			//手机注册
			case '2':
				$this->reg_mobile($user_data);
				break;
			default:
				//注册类型错误
				$this->output(-901);
				break;
		}
	}

	private function check_register($user_data)
	{
		if(empty($user_data))
		{
			$this->output(-901);
		}

		//注册类型为空
		if(empty($user_data['reg_type']))
		{
			$this->output(-901);
		}

		//用户名或密码为空
		if(empty($user_data['username']) || empty($user_data['password']))
		{
			$this->output(-901);
		}

		//身份认证信息为空
		if(empty($user_data['realname']) || empty($user_data['idcard']))
		{
			$this->output(-901);
		}

		//真实姓名不合法
		if (check_data('realname', $user_data['realname']) === false) {
			$this->output(-902);
		}

		//身份证号不合法
		if (check_data('idcard', $user_data['idcard']) === false) {
			$this->output(-902);
		}

		//手机注册验证手机号
		if($user_data['reg_type'] == 2)
		{
			if(check_data('phone', $user_data['username']) === false)
			{
				$this->output(-902);
			}

			//手机号是否绑定
			$check_flag = check_bind_phone($user_data['username']);
			if($check_flag === true)
			{
				$this->output(-104);
			}
		} else if($user_data['reg_type'] == 1) {
			//用户名不合法
			if(check_data('username', $user_data['username']) === false)
			{
				$this->output(-101);
			}
		}

		$userApi = new MemberApi();

		//验证用户名是否注册
		$check_flag = $userApi->checkUsername($user_data['username']);
		if($check_flag === false)
		{
			$this->output(-104);
		}

		//解密验证密码
		$user_password = $this->aes_decode(base64_decode($user_data['password']));

		//验证解密，密码格式
		if($user_password === false || check_data('password', $user_password) === false)
		{
			$this->output(-102);
		}
		$user_data['password'] = $user_password;

		return $user_data;
	}

	//普通用户名注册
	private function reg_personl($user_data)
	{
		$userApi = new MemberApi();
		$uid = $userApi->register($user_data['username'],$user_data['password'],'',$user_data['realname'],$user_data['idcard'],$user_data['register_way'],$user_data['promote_id'],$user_data['promote_account']);
		if($uid > 0) {
			//设置登录返回
			$this->set_login($uid, $user_data['username']);
		}

		//注册失败
		$this->output(-109);
	}

	//手机注册
	private function reg_mobile($user_data)
	{
		//手机验证码为空
		if(empty($user_data['phone_code']))
		{
			$this->output(-106);
		}

		//验证手机验证码
		$result = sms_verify_for_db($user_data['username'], $user_data['phone_code']);
		//验证码输入错误
		if($result == "-3")
		{
			$this->output(-106);
		}
		//验证码超时或获取失败
		else if($result == '-1' || $result == '-2')
		{
			$this->output(-107);
		}

		//验证通过清除此次验证码session
		session('app_sms_phonecode', null);

		$userApi = new MemberApi();
		$uid = $userApi->register($user_data['username'],$user_data['password'],'',$user_data['realname'],$user_data['idcard'],$user_data['register_way'],$user_data['promote_id'],$user_data['promote_account'],$user_data['username']);
		if($uid > 0) {
			//设置登录返回
			$this->set_login($uid, $user_data['username']);
		}

		//注册失败
		$this->output(-109);
	}

	private function set_login($uid, $username)
	{
		$data = array();
		$data['user_id'] = $uid;
		$data['user_account'] = $username;
		$vcode = md5($data['user_id'].$data['user_account'].$this->key);

		$this->login_sign($data, $vcode);
	}
    //注册协议
    public function register_agreement()
    {
        $this->display();
    }
     /**
    * 登陆状态退出
    */
    public function logout(){
    $user_id = I('post.user_id');
    $where['user_id'] = $user_id ;
    $data['login_sign']="";
    $res = M('login_sign','tab_')->where($where)->save($data);
    if ($res !== false) {
        $msg['msg']='退出成功！';
        $this->output($msg);
    } else{
        $this->output(-119);
    }
  }


  public function user_letter_detail()
    {   
        //接收用户名和消息id
        $data = I('get.');
        //查找这条消息这个用户是否读过
        $wherered['message_id']=$data['message_id'];
        $wherered['rec_account']=$data['account'];
        if (empty($data['account']) || empty($data['message_id'])) {
            $this->output(-901);
        } 
        
        $is_detail=M('message_letter','tab_')->where($wherered)->find();
        //查询该消息详情
        $detail_data=M('inside_letter','tab_')->where()->find($data['message_id']);
        //判断该条消息是否已读
        if ($is_detail) 
        {   //已读直接展示
            $return_data= $detail_data;
        } else 
        {   //未读插入已读记录在展示
            $data['send_account']=$detail_data['send_account'];
            $data['rec_account']=$data['account'];
            $data['message_id']=$detail_data['id'];
            $data['create_time']=time();
            $red_add = M('message_letter','tab_')->add($data);
            $wherenum['id']=$detail_data['id'];
            $add_num = M('inside_letter','tab_')->where($wherenum)->setInc('number',1);
            $return_data= $detail_data;
        }
        $this->assign('vo',$return_data);
        //print_r($return_data);exit;
        $this->display();
        
    }

  

}