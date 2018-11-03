<?php
//app支付类
namespace Android\Controller;

class PayController extends BaseController
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
    *用户平台币充值记录
    */
    public function user_deposit_record()
    {
        $map["user_id"] = $this->user_id;
        $map["pay_status"] = 1;
        $p = (int)I('post.p')?(int)I('post.p'):1;
        $size = (int)I('post.size')?(int)I('post.size'):10;
        $deposit = M("deposit","tab_")->where($map)->order("create_time desc")->page($p,$size)->select();
        $this->output($deposit);
    }
}