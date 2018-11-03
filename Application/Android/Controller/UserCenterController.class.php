<?php
//个人中心
namespace Android\Controller;

use User\Api\MemberApi;

class UserCenterController extends BaseController
{
	//当前登陆用户的id
    protected $user_id;
	public function __construct()
	{
		  $this->is_login();
    	parent::__construct();

	}
	/**
	 * 判断用户是否登录
	 * @param sign string 登陆标示
	 * @param id int 用户id
     */
    protected function is_login()
    {
	    $login_sign_post = I('post.token');
	    $id = I('post.id');
	    $where['user_id'] = $id;
	    $login_sign = M('Login_sign','tab_')->where($where)->getField('login_sign');
	    if (empty($login_sign))
	    {
	        $this->output(-114);
	    }else
	    {
	    	if($login_sign != $login_sign_post)
	    	{
	    	    $this->output(-905);
	    	}else
	    	{
	        	$this->user_id=$id; 
	      }

	    }

  	}
  	/**
   	 * 个人中心接口
   	 */
  	public function user_center()
    {
        $where['id'] = $this->user_id; 
        $userinfo = M('User','tab_')->field('id,account,nickname,balance,points,phone,real_name,idcard')->where($where)->find();
      if(empty($userinfo['phone']))
      {
        $userinfo['bind_phone'] = false;
      }else
      { 
        $userinfo['my_phone']=substr_replace($userinfo['phone'],'*****',3,5);
        $userinfo['phone']=base64_encode($this->aes_encode($userinfo['phone']));
        $userinfo['bind_phone'] = true;
      }

      if (empty($userinfo['real_name']) || empty($userinfo['idcard'])) 
      {
        $userinfo['is_person']=false; 
        unset($userinfo['real_name']);
        unset($userinfo['idcard']);  
      } else 
      {
        $userinfo['real_name']=substr_replace($userinfo['real_name'],'**',3);
        $userinfo['idcard']=substr_replace($userinfo['idcard'],'*************',3,13);
        $userinfo['is_person']=true;  
      }
        $this->output($userinfo);
    }
  	/**
  	 * 绑币余额接口
  	 */
  	Public function bind_balance()
  	{
  		$where['user_id'] = $this->user_id;
  		$data = M('user_play','tab_')
            ->field('a.game_name,a.bind_balance,b.icon')
            ->alias('a')
            ->join('left join tab_game as b on a.game_name = b.game_name')
            ->where($where)
            ->group('game_name')
            ->select();
        foreach($data as $k=> &$v)
        {
            $v['icon']=get_cover($v['icon'],'path');
        }
        $this->output($data);
    }

    /**
  	 * 修改密码接口
  	 */
	public function update_pwd()
	{
		$user_data = I('post.');

		$old_password = $user_data['old_password'];
		$new_password = $user_data['new_password'];
		$vcode = $user_data['vcode'];

		unset($user_data['vcode']);
		unset($user_data['id']);
		unset($user_data['token']);

		//登录uid
		if(empty($this->user_id))
		{
			$this->output(-114);
		}

		if(empty($old_password) || empty($new_password) || empty($vcode))
		{
			$this->output(-901);
		}

		$old_password_dec = $this->aes_decode(base64_decode($old_password));
		$new_password_dec = $this->aes_decode(base64_decode($new_password));

		//密码解密判断
		if($old_password_dec === false || $new_password_dec === false)
		{
			$this->output(-902);
		}

		if ($this->validation_sign(array('old_password'=>$old_password_dec,'new_password'=>$new_password_dec), $vcode) === false)
		{
			//验签失败
			$this->output(-100);
		}

		$user_id = $this->user_id;

		//取用户信息
		$user_info = user_info($user_id);

		$userApi = new MemberApi();

		//验证原密码
		$flag = $userApi->checkPassword($user_info['account'], $old_password_dec);
		if($flag === false)
		{
			$this->output(-102);
		}

		//原密码和新密码相同
		if($old_password_dec == $new_password_dec)
		{
			$this->output(-116);
		}

		//修改密码
		$status = $userApi->updatePassword($user_id, $new_password_dec);
		if($status === false)
		{
			$this->output(-900);
		}

		$this->output(1);
	}
	
  	/**
   	 * 绑定手机接口
   	 */
	public function bind_phone()
	{
		$user_data = I('post.');

		$password = $user_data['password'];
		$phone = $user_data['phone'];
		$phone_code = $user_data['phone_code'];
		$vcode = $user_data['vcode'];

		unset($user_data['vcode']);
		unset($user_data['id']);
		unset($user_data['token']);

		$user_id = $this->user_id;

		//登录uid
		if(empty($user_id))
		{
			$this->output(-114);
		}

		//验证为空
		if(empty($password) || empty($phone) || empty($phone_code) || empty($vcode))
		{
			$this->output(-901);
		}

		//验证手机格式
		if(check_data('phone', $phone) === false)
		{
			$this->output(-902);
		}

		//密码解密判断
		$password_dec = $this->aes_decode(base64_decode($password));
		if($password_dec === false)
		{
			$this->output(-902);
		}

		//验签失败
		if ($this->validation_sign(array('password'=>$password_dec,'phone'=>$phone,'phone_code'=>$phone_code), $vcode) === false)
		{
			$this->output(-100);
		}

		//手机号是否绑定
		$check_flag = check_bind_phone($phone);
		if($check_flag === true)
		{
			$this->output(-117);
		}

		//取用户信息
		$user_info = user_info($user_id);

		//已绑定手机
		if(!empty($userinfo['phone']))
		{
			$this->output(-117);
		}

		$userApi = new MemberApi();

		//验证原密码
		$flag = $userApi->checkPassword($user_info['account'], $password_dec);
		if($flag === false)
		{
			$this->output(-102);
		}

		//验证手机验证码
		$result = sms_verify_for_db($phone, $phone_code);
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

		$update_data = array();
		$update_data['id'] = $user_id;
		$update_data['phone'] = $phone;

		$userApi = new MemberApi();
		$result = $userApi->updateUser($update_data);
		if($result === false)
		{
			$this->output(-900);
		}

		$this->output(1);
	}

  	/**
   	 * 用户领取礼包码接口
   	 */
	public function gift_receive()
	{
		$gift_id = I('post.gift_id');

		//登录uid
		if(empty($this->user_id))
		{
			$this->output(-114);
		}

		if(empty($gift_id))
		{
			$this->output(-901);
		}

		$user_id = $this->user_id;

		//是否领取过礼包
		$flag = D('GiftRecord')->check_gift_receive($user_id, $gift_id);
		if($flag === false)
		{
			$this->output(-111);
		}

		$gift_field = 'giftbag_name,game_id,start_time,end_time,novice';
		$gift_info = D('Giftbag')->get_gift_info($gift_id, $gift_field);
		if(empty($gift_info))
		{
			$this->output(-903);
		}

		if(empty($gift_info['novice']))
		{
			$this->output(-112);
		}

		//取用户信息
		$user_info = user_info($user_id);
		$user_account = $user_info['account'];
		$user_nickname = $user_info['nickname'];

		//礼包码
		$gift_novice = explode(",", $gift_info['novice']);
		$user_gift_novice = $gift_novice[0];

		//礼包领取记录
		$add_gift_record['game_id'] = $gift_info['game_id'];
		$add_gift_record['game_name'] = get_game_name($gift_info['game_id']);
		$add_gift_record['gift_id'] = $gift_id;
		$add_gift_record['gift_name'] = $gift_info['giftbag_name'];
		$add_gift_record['server_id'] = $server_id;
		$add_gift_record['status'] = 1;
		$add_gift_record['novice'] = $user_gift_novice;
		$add_gift_record['user_id'] = $user_id;
		$add_gift_record['user_account'] = $user_account;
		$add_gift_record['user_nickname'] = $user_nickname;
		$add_gift_record['create_time'] = time();
		$id = D('GiftRecord')->insert($add_gift_record);

		//插入数据失败
		if(intval($id) <= 0)
		{
			$this->output(-113);
		}

		$new_gift_novice = $gift_novice;
		if(in_array($new_gift_novice[0], $new_gift_novice))
		{
			$pos = array_search($new_gift_novice[0], $new_gift_novice);
			unset($new_gift_novice[$pos]);
		}

		//更新礼包码数据
		$update_gift = array();
		$update_gift['novice'] = implode(",", $new_gift_novice);
		$status = D('Giftbag')->update($gift_id, $update_gift);
		if($status === false)
		{
			$this->output(-113);
		}

		//返回数据
		$return_data = array();
		$return_data['gift_code'] = $user_gift_novice;

		$this->output($return_data);
	}

  /** 
     * 消息中心所有消息记录
     * @param $array 
     * @param $order 排序方式 'asc':升序 'desc':降序 
     * @author whh 
     */
    public function user_message_letter()
    {   
        //接收user_account
        $data = I('post.');
        $del_map["rec_account"] = $data["account"];
        $del_map["status"]=1;//删除
        $page=$data['p'];
        $size=$data['size'];
        $p=($page=='')?1 : $page;
        $size=($size=='')?10 : $size;
        if (empty($data["account"])) 
        {
            $this->output(-901);
        } 
        
        //查找用户删除的消息id
        $del_mids=M('message_letter','tab_')->where($del_map)->order('id desc')->getfield('message_id',true);
        if($del_mids)
        {
           $del_str=implode($del_mids,',');
           $red_map['id']=array('not in ',$del_str); 
        }

        $red_map['rec_account']=$data["account"];
        $red_map['status']=0;//已读---正常
        //查找用户已读但是未被删除的消息id
        $red_mids=M('message_letter','tab_')->where($red_map)->order('id desc')->getfield('message_id',true);

        //除去删除的消息以外，所有的信息详情
        $all_message=M('inside_letter','tab_')->where($red_map)->page($p,$size)->order('id desc')->select();
        
        if(empty($all_message))
        {   
            //暂无数据
            $this->output($all_message);
        }
        foreach ($all_message as $k => $v) {

            if (in_array($v['id'],$red_mids) && !empty($red_mids)) 
            {
                //已读
                $all_message[$k]['is_read']=1;
            } else 
            {
                //未读
                $all_message[$k]['is_read']=0;
            }
            
        }
        $this->output($all_message);
    }
      /** 
     * 消息中心--消息详情
     * @param $array 
     * @author whh 
     */
    
     /** 
     * 消息中心--删除消息
     * @param $array 
     * @author whh 
     */
    public function user_letter_del()
    {   
        $data=I('post.');
        $wheredel['message_id']=$data['message_id'];
        $wheredel['rec_account']=$data['account'];
        if (empty($data['account']) || empty($data['message_id'])) {
            $this->output(-901);
        } 
        $del['status']=1;
        $is=M('message_letter','tab_')->where($wheredel)->find();
        //查询要删除的这条消息是否已读
        if ($is) 
        {   
            //如果已读直接标记删除状态
            $res=M('message_letter','tab_')->where($wheredel)->save($del);
            if ($res!==false) 
            {
                $msg['msg']='删除消息成功!';
                $this->output($msg);
                
            } else 
            {   
                $this->output(-118);
                
            }
        } else 
        {   //如果未读，将消息插入message_letter表，并标记删除
            $map['id']=$data['message_id'];
            $message=M('inside_letter','tab_')->where($map)->find();
            $del['send_account']=$message['send_account'];
            $del['create_time']=time();
            $del['message_id']=$data['message_id'];
            $del['rec_account']=$data['account'];
            $res=M('message_letter','tab_')->add($del);
            if ($res) 
            {
                $msg['msg']='删除消息成功!';
                $this->output($msg);
                
            } else 
            {   
                $this->output(-118);
                
            }
        }
 
        
        
        
    }
    
    /** 
     * 实名认证
     * @param $array 
     * @author whh 
     */
    public function user_person()
    {
        $user=I('post.');
        //判断参数是否为空
        if (empty($user['id']) || empty($user['real_name']) || empty($user['idcard']))
        {
            $this->output(-901);
        } 

        //判断真实姓名格式
        if(check_data('realname', $user['real_name']) === false)
        { 

            $this->output(-902);
        }

        //判断身份证号格式
        $idcard=$this->aes_decode(base64_decode($user['idcard']));
        //$idcard=base64_encode($this->aes_encode($user['idcard']));
        //print_r($idcard);exit;
        if (check_data('idcard', $idcard) === false) 
        {   
            $this->output(-902);
        } 
        
        $data['id']=$user['id'];
        $data['real_name']=$user['real_name'];
        $data['idcard']=$idcard;
        $res=M('user','tab_')->save($data);
        if ($res !== false) {
            $msg['msg']="实名认证成功！";
            $this->output($msg);
        } else {
            $this->output(-900);
        }
        

        

    }



}