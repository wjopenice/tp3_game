<?php
namespace Android\Controller;

class BaseController extends EncryptController
{    
	public function __construct()
	{
        /* 读取站点配置 */
        $config = api('Config/lists');
        C($config); //添加配置
        parent::__construct();
	}
	/**
	 * @author zdd
	 * 获取对应错误输出信息
	 * @param $code 状态码
	 */
	protected function codeToData($code='')
	{
		if(empty($code))
		{
			$code = 1;
		}
		$data['status'] = $code;
		$data['data'] = '';
		//如果返回数据为空，则返回未找到相关数据
		switch ($code)
		{
		  case 1: $data['msg'] = 'ok'; break;
		  case -100:$data['msg'] = '验签失败！';break;
		  case -101:$data['msg'] = '用户名格式不正确！';break;
		  case -102:$data['msg'] ='密码错误！';break;
		  case -103:$data['msg'] ='用户名不存在或被禁用！';break;
		  case -104:$data['msg'] ='用户名已注册';break;
		  case -105:$data['msg'] ='图片验证码为空或错误';break;
		  case -106:$data['msg'] ='手机验证码为空或错误';break;
		  case -107:$data['msg'] ='手机验证码验证超时！请重新获取';break;
		  case -108:$data['msg'] ='发送手机验证码失败';break;
		  case -109:$data['msg'] ='注册失败';break;
		  case -110:$data['msg'] ='该用户未绑定手机号！';break;
		  case -111:$data['msg'] ='用户已领取此礼包';break;
		  case -112:$data['msg'] ='礼包码已领取完';break;
		  case -113:$data['msg'] ='领取礼包码失败';break;
		  case -114:$data['msg'] ='请先登录';break;
		  case -115:$data['msg'] ='旧密码错误';break;
		  case -116:$data['msg'] ='新密码不能与旧密码相同';break;
		  case -117:$data['msg'] ='该手机号码已被绑定';break;
		  case -118:$data['msg'] ='删除消息失败！';break;
		  case -119:$data['msg'] ='退出失败！';break;
		  case -120:$data['msg'] ='用户名与手机号码不匹配';break;
		  case -200:$data['msg'] ='下载地址未找到';break;
		  case -201:$data['msg'] ='没有需要更新的数据';break;
		  case -900:$data['msg'] ='数据更新或插入失败！';break;
		  case -901:$data['msg'] ='参数不能为空！';break;
		  case -902:$data['msg'] ='参数格式有误！';break;
		  case -903:$data['msg'] ='未找到相关数据';break;
		  case -904:$data['msg'] ='发送验证码失败！';break;
		  case -905:$data['msg'] ='非法操作';break;
		  case -906:$data['msg'] ='暂无数据！';break;
		  default: $data['status'] = '-9999'; $data['msg'] = '未知错误'; break;
		}
		return $data;
	}
	/**
	 *设置接口输出信息
	 *@param  int     $status 提示状态 
	 *@param  string  $return_code 提示代码
	 *@param  string  $return_msg  提示信息
	 *@return string  base64加密后的json格式字符串
	 *@author 小纯洁
	 */
	protected function output($msg)
	{
		if(is_array($msg))
		{
			$info['status'] = 1;
			$info['msg'] = 'ok';
			$info['data'] = $msg;
		}else
		{
			$info = $this->codeToData($msg);
		}
	    echo json_encode($info);
	    exit();
	}
	/**
	 * 获取版本信息
	 * @author zdd
	 */
	public function version()
	{
        $data = M('app_version','tab_')->order('serverVersion desc')->find();
        $where['game_id'] = 68;
        $file_url = M('game_source','tab_')->where($where)->getField('file_url');
        $file_url = substr($file_url,1);
        $data['updateurl']='http://'.$_SERVER['HTTP_HOST'].$file_url;
        //$data['updateurl']=$file_url;
        $this->output($data);
    }
    /**
   	 * 绑定手机号功能 检查手机号是否被绑定  未绑定调用发送验证码接口
   	 *@author whh
   	 */
    public function is_bind_phone()
    {
    	$phone = I('post.phone');
		$account = I('post.account');
		//手机号是否绑定
		$check_flag = check_bind_phone($phone);
		if ($check_flag===true) 
		{
			$this->output(-117);
		} else 
		{
			if (empty($phone))
			{
				$this->output(-901);
			}

			/*//帐号不为空，查询帐号和手机号
			if(!empty($account))
			{
				//用户名与手机号码不匹配
				if(check_account_phone($phone, $account) === false)
				{
					$this->output(-120);
				}
			}
           */
			$delay = 10;
			$flag = send_sms($phone, $delay);

			if ($flag)
			{
				$this->output(1);
			}

			$this->output(-904);
		}
			

    }
	/**
	* 发送手机验证码
	* @author sunhao
	*/
	public function telsvcode() 
	{
		$phone = I('post.phone');
		$account = I('post.account');
		
		if (empty($phone))
		{
			$this->output(-901);
		}
        
		//帐号不为空，查询帐号和手机号
		if(!empty($account))
		{
			//用户名与手机号码不匹配
			$result = check_account_phone($phone, $account);
			//该用户名不存在
			if($result == -500)
			{
				$this->output(-103);
			}
			//该用户未绑定手机号
			if($result == -501)
			{
                $this->output(-110);
			}
			//用户名和手机号不匹配
			if($result == -502)
			{
                $this->output(-120);
			}
		}else{
			//手机号是否绑定
		    $check_flag = check_bind_phone($phone);
		    if ($check_flag===true) 
			{
				$this->output(-117);
			}
		}

		$delay = 10;
		$flag = send_sms($phone, $delay);

		if ($flag)
		{
			$this->output(1);
		}

		$this->output(-904);
	}

}