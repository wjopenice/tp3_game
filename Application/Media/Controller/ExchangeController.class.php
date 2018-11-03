<?php
namespace Media\Controller;
/**
 * 激活码控制器
 * @author 采蘑菇的巳寸
 */
class ExchangeController extends BaseController{
	
	public function is_login(){
		$mid = parent::is_login();
            if ($mid<1) {
                $this->ajaxReturn(array('status'=>0,'msg'=>'请先登录'));
            }else{
            	$this->ajaxReturn(array('status'=>1,'msg'=>'ok'));
            }
	}
	/**
	 * 激活码兑换
	 */
	public function index(){
		if(IS_POST){
			$mid = parent::is_login();
            if ($mid<1) {
                $this->ajaxReturn(array('status'=>0,'msg'=>'请先登录'));
            }

            //验证码
            $verify = new \Think\Verify();
            $vcode = trim(I('post.verify'));
            if ($verify->check($vcode)) {

            $cdkey = trim(I('post.code'));
			$exchange = M('Exchange','tab_');
			$exchange_record = M('Exchange_record','tab_');
			if(!$cdkey){
				$this->ajaxReturn(array('status'=>-1,'msg'=>'激活码不能为空'));
			}else{
				$where_rec['cdkey'] = $cdkey;
				$res_rec =$exchange_record ->where($where_rec)->find();
				if($res_rec){
					$this->ajaxReturn(array('status'=>-2,'msg'=>'该激活码已领取过'));
				}
			}
			$where['sign'] = substr($cdkey,0,4);
			$rec_exchange = $exchange->where($where)->find();
			if(!$rec_exchange){
				$this->ajaxReturn(array('status'=>-3,'msg'=>'激活码输入有误'));
			}else{
				$time = time();
				$novice_arr = explode(',',$rec_exchange['novice']);
				if(in_array($cdkey,$novice_arr)){
					if($time < $rec_exchange['start_time']){
					$this->ajaxReturn(array('status'=>-5,'msg'=>'还未开放兑换'));
					}else if($time >$rec_exchange['end_time'] || $rec_exchange['status'] != 1){
						$this->ajaxReturn(array('status'=>-6,'msg'=>'激活码已失效'));
					}
					$k = array_search($cdkey,$novice_arr);
					unset($novice_arr[$k]);

					$novice_data['novice'] = implode(',',$novice_arr);
					$where_save['id'] = $rec_exchange['id'];
					$novice_save = $exchange->where($where_save)->save($novice_data);
					if($novice_save === false){
						$this->ajaxReturn(array('status'=>-4,'msg'=>'服务器故障'));
					}
					$user = session("member_auth");
					$where_user['id'] = $user['mid'];
					$res_save = M('User','tab_')->where($where_user)->setInc('balance',$rec_exchange['goods_num']);
					if($res_save !== false){
						$record_data['user_account'] = $user['account'];
						$record_data['user_id'] = $user['mid'];
						$record_data['describe'] = $rec_exchange['describe'];
						$record_data['goods_num'] = $rec_exchange['goods_num'];
						$record_data['status'] = 1;
						$record_data['cdkey'] = $cdkey;
						$record_data['create_time'] = time();
						$exchange_record->add($record_data);
						$this->ajaxReturn(array('status'=>1,'msg'=>'ok'));
					}else{
						$this->ajaxReturn(array('status'=>-4,'msg'=>'服务器故障1'));
					}
				}else{
						$this->ajaxReturn(array('status'=>-3,'msg'=>'激活码输入有误'));
				}
			}

			} else {
            	return $this->ajaxReturn(array('status'=>-7,'msg'=>'验证码错误'));
            }

		}else{
			$adv = M("Adv","tab_");
	        $map['status'] = 1;
	        $map['pos_id'] = 13; #充值页广告图id
	        $adv_duihuan = $adv->where($map)->order('sort ASC')->select();
	        $adv_data=$adv_duihuan['0'];
	        $this->assign("adv_duihuan",$adv_data);
			$this->display();
		}
	}
	/**
	 * 激活码兑换记录
	 */
	public function record(){
		$mid = parent::is_login();
            if ($mid<1) {
                $this->ajaxReturn(array('status'=>0,'msg'=>'请先登录'));
            }
		$user = session("member_auth");
		$where_user['user_id'] = $user['mid'];
		$list = M('Exchange_record','tab_')->where($where_user)->order('id desc')->limit(10)->select();
		$this->assign('list',$list);
		$adv = M("Adv","tab_");
        $map['status'] = 1;
        $map['pos_id'] = 13; #充值页广告图id
        $adv_duihuan = $adv->where($map)->order('sort ASC')->select();
        $adv_data=$adv_duihuan['0'];
        $this->assign("adv_duihuan",$adv_data);
		$this->display();
	}
}