<?php
namespace Quicksdk\Controller;

use Media\Controller\HomeController;
use User\Api\MemberApi;
use Org\XiguSDK\Xigu;
use Common\Api\GameApi;

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
     * 快发用户绑定
     * @param kfUserid  快发用户id  
     * @param timestamp  时间戳
     * @param sign  签名
     * @author whh 
     */
    public function binding(){
        $data = I('post.');
        //判断参数是否为空
        if (empty($data['kfUserid']) || empty($data['timeStamp']) || empty($data['sign'])) 
        {
            $this->output(1,'参数不能为空');
        }
        $sign = $data['sign'];
        unset($data['sign']);
        //验证签名
        if($this->validation_sign($data,$sign)===false)
        {
            $this->output(1,'验签失败');
        }
        //查看该快发id是否已被绑定
        $isbind = is_bindid($data['kfUserid']);
        if($isbind === false)
        {
            $user_data['username'] = get_verify_kfusername($len=8);
            $user_data['password'] = 'w123456';
            $user_data['register_way'] = 3;//快发注册
            $user_data['promote_id'] = 0;
            $user_data['promote_account'] = '自然注册';
            //注册添加新用户
            $userApi = new MemberApi();
            $uid = $userApi->register($user_data['username'],$user_data['password'],'','','',$user_data['register_way'],$user_data['promote_id'],$user_data['promote_account'],'');
            $map['id'] = $uid;
            //进行用户快发id号绑定
            $res = M('user','tab_')->where($map)->setField('bindid',$data['kfUserid']);
            if($res !== false) 
            {
                //绑定成功
                $return_data['user_id'] = $uid;
                $this->output(0,'成功',$return_data);
            }else
            {
                $this->output(1,'绑定失败');
            }
        }else
        {
             //$this->output(1,'该快发id已被绑定');
             $return_data['user_id'] = $isbind;
             $this->output(0,'成功，已被绑定',$return_data);
        }
        
        
    }
    /** 
     * token验证接口
     * @param kfUserid  快发用户id  
     * @param timestamp  时间戳
     * @param sign  签名
     * @author whh 
     */
     public function verify_token($token)
     {
          //$data['timeStamp'] = time();
          $data['timeStamp'] = time();
          $data['token'] = $token;
          //生成sign
          $sign = $this->encrypt_md5($data);
          $data['sign'] = $sign;
          $url = 'http://z.kuaifazs.com/foreign/oauth/mushroom.php';
          //调用快发接口
          $result = $this->post($data,$url);
          //var_dump($result);exit;
          //判断快发返回值
          $result = json_decode($result,true);
          //var_dump($result);exit;
          if($result['statusInfo']['code'] == 0)
          {   
              //print_r($result['data']);exit;
              return $result['data'];
          }else
          {
              return false;
          }

     }
     /** 
     * 快发充值通知接口
     * @param sdk_version,title,game_id,game_name,game_appid,code,account,user_id,extend,price,kfOrder,pay_status,payTime,userIp,out_trade_no  
     * @param timeStamp  时间戳
     * @param sign  签名
     * @author whh 
     */
    public function callback()
    {
        $request = I('post.');
        //file_put_contents('/home/wwwroot/test.898sm.com/Data/bbbbb.log',json_encode($request));
        //判断参数是否为空
        if(empty($request['sign']) || empty($request['game_id']) || empty($request['user_id']) || empty($request['extend']))
        {
            $this->output(1,'参数不能为空');
        }
          //判断支付状态
        if(empty($request['pay_status']))
        {
            $return_msg = "success";
            //echo json_encode($return_msg['msg']); 
            echo $return_msg;
            exit();     
        }
        
        //签名验证
        $sign = $request['sign'];
        unset($request['sign']);
        if($this->validation_sign($request,$sign)===false)
        {
            $this->output(1,'验签失败');
        }
        //校验订单真实性
        $is_realy = $this->is_realy($request);
        if(empty($is_realy))
        {
            $this->output(1,'订单真实性校验失败');
        }
        //查询订单是否已存在
        $is_ordernum = is_ordernum($request['kfOrder'],$request['user_id'],$request['game_id']);
        
        if(empty($is_ordernum))
        {    
            //产生订单号
            $request['out_trade_no'] = "KF_".date('Ymd').date('His').sp_random_string(4);
            //订单不存在 保存订单   并给CP、快发发送回调
            $is_add = $this->add_kfspend($request);
            //var_dump($is_add);exit;
            if(empty($is_add))
            {
                $this->output(1,'添加数据失败');
            }else
            {
                $is_ordernum['pay_order_number'] = $request['out_trade_no'];
            } 
        }//else
        //{   //订单存在  给CP回调, 并给快发发送success
        $callback['out_trade_no'] = $is_ordernum['pay_order_number'];
        $callback['code'] = 1;
        $game = new GameApi();
        $game->game_pay_notify($callback,$callback['code']);
        //给快发发送success
        $return_msg = 'success';
        //echo json_encode($return_msg); 
        echo $return_msg;
        exit();
        //}

    }
    /** 
     * 快发消费记录添加
     * @author whh 
     */
    public function is_realy($param)
    {
        $data['game_id'] = $param['game_id'];
        $data['kfOrder'] = $param['kfOrder'];
        $data['timeStamp'] = time();
        $sign = $this->encrypt_md5($data);
        $data['sign'] = $sign;
        //file_put_contents('/home/wwwroot/test.898sm.com/Data/bbbbb.log',$data['game_id'].'----'.$data['kfOrder'].'----'.$data['timeStamp'].'----'.$data['sign']);
        $url = 'http://z.kuaifazs.com/foreign/recharge/mushroomorder.php';
        //调用快发接口
        $result = $this->post($data,$url);
        //var_dump($result);exit;
        //判断快发返回值
        $result = json_decode($result,true);
        //var_dump($result);exit;
        if($result['statusInfo']['code'] == 0)
        {   
            if($result['data']['price'] == $param['price'])
            {  
                return true;
            }else
            {
                return false;
            }
            
        }else
        {
            return false;
        }
    }
    /** 
     * 快发消费记录添加
     * @author whh 
     */
    public function add_kfspend($param)
    {
        $spend = M("spend","tab_");
        //数据整理
        $kfspend_data =  $this->kfspend_param($param);
        /*$ordercheck = $spend->where(array('pay_order_number'=>$kfspend_data["pay_order_number"]))->find();
        if($ordercheck)
        {
            $this->set_message(0,'fail',"订单已经存在，请刷新充值页面重新下单！");
        }*/
        $result = $spend->add($kfspend_data);
        //echo $spend->getlastsql();exit;
        return $result;
         
    }
    /** 
     * 快发消费记录数据整理
     * @author whh 
     */
    public function kfspend_param($param=array())
    {
        $user_entity = get_user_entity($param['user_id']);
        $data_kfspend['user_id']          = $param["user_id"];
        $data_kfspend['user_account']     = $user_entity["account"];
        $data_kfspend['user_nickname']    = $user_entity["nickname"];
        $data_kfspend['game_id']          = $param["game_id"];
        $data_kfspend['game_appid']       = $param["game_appid"];
        $data_kfspend['game_name']        = $param["game_name"];
        $data_kfspend['server_id']        = 0;
        $data_kfspend['server_name']      = "";
        //$data_kfspend['promote_id']       = $user_entity["promote_id"];
        //$data_kfspend['promote_account']  = $user_entity["promote_account"];
        $data_kfspend['promote_id']       = 0;
        $data_kfspend['promote_account']  = '自然注册';
        $data_kfspend['order_number']     = $param["kfOrder"];
        $data_kfspend['pay_order_number'] = $param["out_trade_no"];
        $data_kfspend['props_name']       = $param["title"];
        $data_kfspend['pay_amount']       = $param["price"];
        $data_kfspend['pay_time']         = $param['payTime'];
        $data_kfspend['pay_status']       = $param["pay_status"];
        $data_kfspend['pay_game_status']  = 0;
        $data_kfspend['extend']           = $param['extend'];
        $data_kfspend['pay_way']          = 4;
        $data_kfspend['spend_ip']         = $param["userIp"];
        $data_kfspend['sdk_version']      = $param["sdk_version"];
        //print_r($data_kfspend);exit;
        return $data_kfspend;

    }

     /**
    *post提交数据
    */
    protected function post($param,$url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($param));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);//设置等待时间
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }



  

}