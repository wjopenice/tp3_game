<?php
namespace Deal\Controller;
use User\Api\MemberApi;
class DealController{
	public $user;	//实例化user模型
	public $userModel;
	public $secretkey;
	public $record;
	
	/**
	 * 构造函数，初始化参数
	 */
	public function __construct(){
		$this->user = new MemberApi();
		$this->userModel = M('user','tab_');
		$this->record = M('record_8868','tab_');
		$this->secretkey = 'moguwansignkey';
		

	} 
	/**
	 * 验证用户信息
	 */
	public function verifyUser(){

		if(IS_POST){
			cmdVerify(__FUNCTION__);
			

			$game_account = I('post.game_account');
			$password_base = I('post.password');
			$vcode_now = getVcode($game_account,$password_base,$this->secretkey);
			vcodeVerify($vcode_now);
			
				if(empty($game_account) || empty($password_base)){	
				$msg='账户名和密码不能为空';
				toBack('false',$msg,5000);
				}else{
					$password = aes_decode($password_base);
					$res = $this->user->login($game_account,$password);
					if($res > 0 ){
						$mobile = $this->userModel->where('id = '.$res)->getField('phone');
						if(!empty($mobile)){
							toBack('true','ok' ,'', $mobile);
						}else{
							toBack();
						}
				
					}else if($res == -2 ){
						$msg='用户名或密码错误';
						toBack('false',$msg,1010);
					}else if($res == -1){
						$msg = '用户不存在或者被禁用';
						toBack('false',$msg,1000);
					}else{
						$msg='账号未知错误';
						toBack('false',$msg,5001);
					}
				}
			
		
		}else{
			$msg='请求方式不对哦';
			toBack('false',$msg,5002);
		}
	}
	/**
	 * 根据游戏账号修改账户信息
	 */
	public function modifyUser(){
		if(IS_POST){
			cmdVerify(__FUNCTION__);
			$game_account = I('post.game_account');
			$old_password_base = I('post.old_password');
			$old_password = aes_decode($old_password_base);

			
			$new_password_base = I('post.new_password');
			$new_password = aes_decode($new_password_base);
			$phone = trim(I('post.mobile'));
			if(empty($game_account) || empty($old_password_base) || empty($new_password_base)){	
				$msg='账户名和密码不能为空';
				toBack('false',$msg,5000);
				}
			$vcode_now = getVcode($game_account,$phone,$new_password_base,$old_password_base,$this->secretkey);
			vcodeVerify($vcode_now);
			
			
			$res = $this->user->login($game_account,$old_password);
			if($res < 1){
				$msg = '密码已被修改';
				toBack('false',$msg,5005);
			}
			$type = $this->userModel->where('id = '.$res)->getField('type');
			if($type != 1){
				$msg = '对不起，该账号未开启授权';
				toBack('false',$msg,5009);
			}
			if($phone){
				$mobile_now = $this->userModel->where('phone = '.$phone)->getField('phone');
				if($mobile_now){
					$msg = '该手机号已绑定';
					toBack('false',$msg,4001);
				}
			}
			
			
			$data['password'] = think_ucenter_md5($new_password, UC_AUTH_KEY);
			$data['email']='';
			$data['phone']=$phone;
			$data['real_name']='';
			$data['idcard']='';
			$result = $this->userModel->where('id = '.$res)->save($data);
			if($result === false){
				$msg = '修改密码失败，服务器故障';
				
				toBack('false',$msg,5007);
			}else{
				$msg = 'ok';
				toBack('true',$msg);
			}

		}else{
			$msg='请求方式不对哦';
			toBack('false',$msg,5002);
		}
	}
	/**
	 * 暂时不用集成到了上面的一个方法中了
	 * 根据游戏账号清空绑定手机和密保信息
	 */
	public function cleanSecurity(){
			$msg='敬请期待';
			toBack('false',$msg,5010);
		/*if(IS_POST){
			cmdVerify(__FUNCTION__);
			$game_account = I('post.game_account');
			$remark = I('post.remark');
			$vcode_now = getVcode($game_account,$remark,$this->secretkey);
			vcodeVerify($vcode_now);
			$type = $this->userModel->where('id = '.$res)->getField('type');
			if($type != 1){
				$msg = '对不起，该账号未开启授权';
				toBack('false',$msg,5009);
			}
			$data['email']='';
			$data['phone']='';
			$data['real_name']='';
			$data['idcard']='';
			$where['account'] = $game_account;
			$where['type'] = 1;
			$result = $this->userModel->where($where)->save($data);
			if($result === false){
				$msg = '清空账号信息失败';
				
				toBack('false',$msg,5006);
			}else{
				$msg = 'ok';
				toBack('true',$msg);
			}

		}else{
			$msg='请求方式不对哦';
			toBack('false',$msg,5002);
		}*/
	}
 /**
	 * 接收账号发布/交易成功信息
	 */
      public function notify(){
		if(IS_POST){
			cmdVerify(__FUNCTION__);
			$game_account = I('post.game_account');
			$type = I('post.type');
	        $ext = I('post.ext');
	        

            $pay=json_decode($ext,true);
			$vcode_now =getVcode($game_account,$type,$this->secretkey);
			vcodeVerify($vcode_now);	
			if(empty($game_account) || empty($type)){	
				$msg='账户名和类型不能为空';
				toBack('false',$msg,5000);
				}		
			$data['type']=$type;
			$where['account'] = $game_account;
			$res = $this->userModel->where($where)->find();
			if($res){
				$result = $this->userModel->where($where)->save($data);
			}else{
				$msg = '用户不存在或者被禁用';
				toBack('false',$msg,1000);
			}
			

			
			
			if($result === false){
				$msg = '修改状态失败，服务器故障';
				$sql = $this->userModel->getlastsql();		
				file_put_contents('/home/wwwroot/www.u7858.com/Application/Deal/Controller/err.sql', $sql,FILE_APPEND);
				
				toBack('false',$msg,5008);
			}else{
				$msg = 'ok' ;
				$data['user_account']=$game_account;
				$data['create_time']= time();
				if(!empty($pay)){
					$data=array_merge($data,$pay);
				}
				
                $this->record->add($data);
				toBack('true',$msg);
			}

		}else{
			$msg='请求方式不对哦';
			$this->toBack('false',$msg,5002);
		}
	}
















	
}