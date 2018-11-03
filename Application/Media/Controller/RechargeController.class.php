<?php
namespace Media\Controller;
use Think\Controller;
use Admin\Model\GameModel;
use Common\Api\PayApi;
use Org\Itppay\itppay;
/**
 * 文档模型控制器
 * 文档模型列表和详情
 */
class RechargeController extends BaseController {
    public function chongzhi(){
    	$wheresign['name']='ALIPAY_POINT_SIGN';
        $alipay_points_sign=M('config','sys_')->where($wheresign)->getfield('value');
        //$sql=M('config','sys_')->getlastsql();
        //print_r($sql);exit;
        $this->assign('points',$alipay_points_sign);
        $this->assign('account',session('member_auth.account'));
    	$this->adv_recharge();
        $this->display();
    }

    /**
     *支付宝充值判断用户是否存在
     *@author whh 
    */
    public function checkUser(){
    	//file_put_contents('E:/bbbbb.html',json_encode($_POST['username']));
        #判断账号是否存在
		$user = get_user_entity($_POST['username'],true);
		//file_put_contents('E:/bbbbb.html',json_encode($user));
		if(empty($user)){
			$msg="用户不存在";
			echo json_encode($msg);exit();
		}
		//判断是否开启支付宝充值
		if(pay_set_status('alipay')==0){
			$msg="网站未启用支付宝充值";
		    echo json_encode($msg);
			exit();
		}

    }

    /**
     *支付宝充值
     *@author whh 
    */
    public function beginPay(){
    	//file_put_contents('E:/sssssss.html',json_encode($_POST));
    	$user = get_user_entity($_POST['account'],true);
        #支付配置
		$data['order_no'] = 'PF_'.date('Ymd').date ( 'His' ).sp_random_string(4);
		
		switch ($_POST['apitype']) {
			case 'alipay':
				$data['fee']      = $_POST['money'];
				$data['pay_type'] = $_POST['apitype'];
				$data['config']   = "alipay";
				$data['service']  = "create_direct_pay_by_user";
				$data['pay_way']  = 1;
				break;
			case 'weixin':
                $data['fee']      = $_POST['amount'];
                $data['pay_type'] = "swiftpass";
                $data['config']   = $_POST['apitype'];
                $data['service']  = "pay.weixin.native";
                $data['pay_way']  = 2;
                break;
            case 'weixin_zl':
                $data['fee']      = $_POST['amount'];
                $data['pay_type'] = "itppay";
                $data['pay_way']  = 2;
                $data['user']     = $user;
                $this->weixin_zl($data);die();
                break;
			default:
				# code...
				break;
		}
		
		//页面上通过表单选择在线支付类型，支付宝为alipay 财付通为tenpay
        $pay = new \Think\Pay($data['pay_type'],C($data['config']));
        $vo = new \Think\Pay\PayVo();
        $vo->setBody("平台币充值")
            ->setFee($data['fee'])//支付金额
            ->setTitle("平台币")
            ->setOrderNo($data['order_no'])
            ->setService($data['service'])
            ->setSignType("MD5")
            ->setPayMethod("direct")
            ->setTable("deposit")
            ->setPayWay($data['pay_way'])
            ->setUserId($user['id'])
            ->setAccount($user['account'])
            ->setUserNickName($user['nickname'])
            ->setPromoteId($user['promote_id'])
            ->setPromoteName($user['promote_account']);
        switch ($_POST['apitype']) {
        	case 'alipay':
        		echo $pay->buildRequestForm($vo);
        		break;
        	case 'weixin':
        		$result = $pay->buildRequestForm($vo);
        		if($result['status1'] === 500){
					\Think\Log::record($result['msg']);
					$html ='<div class="d_body" style="height:px;">
							<div class="d_content">
								<div class="text_center">'.$result["msg"].'</div>
							</div>
							</div>';
					$json_data = array("status"=>1,"html"=>$html);
				}else{
					$html ='<div class="d_body" style="height:px;">
							<div class="d_content">
								<div class="text_center">
									<table class="list" width="100%">
										<tbody>
										<tr>
											<td class="text_right">订单号</td>
											<td class="text_left">'.$data["order_no"].'</td>
										</tr>
										<tr>
											<td class="text_right">充值金额</td>
											<td class="text_left">本次充值'.$data["fee"].'元，实际付款'.$data["fee"].'元</td>
										</tr>
										</tbody>
									</table>
									<img src="'.$result["code_img_url"].'" height="301" width="301">
									<img src="/Public/Media/images/wx_pay_tips.png">
								</div>
							</div>
						</div>';
					$json_data = array("status"=>1,"html"=>$html);
				}
				$this->ajaxReturn($json_data);
        		break;
        	default:
        		# code...
        		break;
        }
    }


    public function weixin_zl($data){
        $this->add_deposit($data);

        /* *
         * 配置信息
         */
        $itppay_config["appid"]=C('weixin_zl.appid');//交易发起所属app
        $itppay_config["key"]=C('weixin_zl.key');//合作密钥
 
        /* *
         * 请求参数，参数须按字符升序排列
         */
        $parameter=array(
            "amount"         =>$data['fee']*100,//[必填]订单总金额，单位(分)
            "appid"          =>$itppay_config["appid"],//[必填]//交易发起所属app
            "body"           =>"平台币充值",//[必填]商品描述
            "clientIp"       =>get_client_ip(),//[必填]客户端IP
            "cpChannel"      =>"",//CP分发渠道
            "currency"       =>"",//币种，默认RMB
            "description"    =>"",//订单附加描述
            "expireMs"       =>"",//过期时间毫秒数，默认24小时过期
            "extra"          =>"",//附加数据，以键值对形式存放，例如{"key":"value"}
            "mchntOrderNo"   =>$data['order_no'],//[必填]商户订单号，必须唯一性
            "notifyUrl"      => "http://" . $_SERVER['HTTP_HOST'] . "/callback.php/Notify/weixin_zl_notify",
            // "notifyUrl"      =>"http://trans.palmf.cn/notify.php",//[必填]订单支付结果异步通知地址，用于接收订单支付结果通知，必须以http或https开头
            "payChannelId"   =>"2100000001",//支付渠道id
            "returnUrl"      => "http://" . $_SERVER['HTTP_HOST'] . "/callback.php/Notify/weixin_zl_return",
            // "returnUrl"      =>"http://trans.palmf.cn/return.php",//[必填]订单支付结果同步跳转地址，用于同步跳转到商户页面，必须以http或https开头
            "subject"        =>"平台币",//[必填]商品名称
            "version"        =>"api_NoEncrypt",//接口版本号，值为h5_NoEncrypt时,则明天平台返回商户参数时，不进行RSA加密
        );
        /* *
         * 建立请求
         */
        $itpPay = new itpPay($itppay_config);
        $orderInfo=$itpPay->getOrderInfo($parameter);
        $data_info=array('orderInfo'=>$orderInfo);
        $itppay_gateway="http://trans.palmf.cn/sdk/api/v1.0/cli/order_api/0";
        $result=$itpPay->curl($itppay_gateway,$data_info);
        $result=json_decode($result,true);
        if($result['respCode'] == 200){
            $html ='<div class="d_body" style="height:px;">
                        <div class="d_content">
                            <div class="text_center">
                                <table class="list" width="100%">
                                    <tbody>
                                    <tr>
                                        <td class="text_right">订单号</td>
                                        <td class="text_left">'.$data["order_no"].'</td>
                                    </tr>
                                    <tr>
                                        <td class="text_right">充值金额</td>
                                        <td class="text_left">本次充值'.$data["fee"].'元，实际付款'.$data["fee"].'元</td>
                                    </tr>
                                    </tbody>
                                </table>
                                <img src="' . strstr($result["extra"],'https') . '" height="301" width="301">
                                <img src="/Public/Media/image/wx_pay_tips.png">
                            </div>
                        </div>
                    </div>';
                $json_data = array("status"=>1,"html"=>$html);

        }else{
            
            \Think\Log::record($result['respMsg']);
            $html ='<div class="d_body" style="height:px;">
                    <div class="d_content">
                        <div class="text_center">respMsg:'.$result["respMsg"].'</div>
                        <div class="text_center">respCode:'.$result["respCode"].'</div>
                    </div>
                    </div>';
            $json_data = array("status"=>1,"html"=>$html);
        }            

        $this->ajaxReturn($json_data);






    }

     /**
     *平台币充值记录
     */
    private function add_deposit($data)
    {
        
            $user = $data['user'];
            $deposit = M("deposit", "tab_");
            $deposit_data['order_number'] = "";
            $deposit_data['pay_order_number'] = $data['order_no'];
            $deposit_data['user_id'] = $user['id'];
            $deposit_data['user_account'] = $user['account'];
            $deposit_data['user_nickname'] = $user['nickname'];
            $deposit_data['promote_id'] = $user['promote_id'];
            $deposit_data['promote_account'] = $user['promote_account'];
            $deposit_data['pay_amount'] = $data['fee'];
            $deposit_data['pay_status'] = 0;
            $deposit_data['pay_way'] = $data['pay_way'];
            $deposit_data['pay_source'] = 0;
            $deposit_data['pay_ip'] = get_client_ip();
            $deposit_data['pay_source'] = 0;
            $deposit_data['create_time'] = NOW_TIME;
            $result = $deposit->add($deposit_data);
            return $result;
        
    }



     /*
      *  充值页广告图
      *  @author   whh
      */
   
   public function adv_recharge(){
        $adv = M("Adv","tab_");
        $map['status'] = 1;
        $map['pos_id'] = 12; #充值页广告图id
        $adv_recharg = $adv->where($map)->order('sort ASC')->select();
        $adv_data=$adv_recharg['0'];
        $this->assign("adv_recharg",$adv_data);
    }



}
