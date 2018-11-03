<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Home\Controller;
use OT\DataDictionary;
use User\Api\PromoteApi;
use User\Api\UserApi;
use Common\Api\PayApi;
/**
 * 前台首页控制器
 * 主要获取首页聚合数据
 */
class PromoteController extends BaseController {
    //系统首页
    public function index(){
        header("Content-type:text/html;charset=utf-8");
        $user = D('Promote')->isLogin();
        if(empty($user)) {
            $this->redirect("Home/Index/index");
        }
        $probla=M("Promote","tab_")->where(array("id"=>get_pid()))->find();
        $this->assign("balance",$probla['balance_coin']<=0?0:$probla['balance_coin']);
        $this->assign("balance_status",$probla['balance_status']);
        $this->assign("today",$this->total(1));
        $this->assign("month",$this->total(3));
        $this->assign("total",$this->total());
         $this->assign("yesterday",$this->total(5));
        $url="http://".$_SERVER['HTTP_HOST'].__ROOT__."/media.php/member/preg/pid/".get_pid();
        $this->assign("url",$url);
        $this->assign("list",M("Document")->where("category_id=40")->select());
        $this->display();
    }   
    private function total($type) {
        if($_REQUEST['promote_id'] ===null || $_REQUEST['promote_id']==='0'){    
             $ids = M('Promote','tab_')->where('parent_id='.get_pid())->getfield("id",true);     
             if(empty($ids)){
                $ids = array(get_pid());
             }       
          array_unshift($ids,get_pid());      
         } else{         
            $ids = array($_REQUEST['promote_id']);  
         }    $where['promote_id'] = array('in',$ids);   
              $where['pay_status'] = 1;    
                 switch ($type) {    
                 case 1: { // 今天      
                     $start=mktime(0,0,0,date('m'),date('d'),date('Y'));       
                       $end=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;      
                     };
                     break;   
                      case 3: { 
                      // 本月        
                    $start=mktime(0,0,0,date('m'),1,date('Y'));       
                      $end=mktime(0,0,0,date('m')+1,1,date('Y'))-1;       
                    };
                    break;         
                       case 4: { 
                       // 本年    
                        $start=mktime(0,0,0,1,1,date('Y'));   
                        $end=mktime(0,0,0,1,1,date('Y')+1)-1;    
                    };
                    break;   
                       case 5: { // 昨天     
                        $start=mktime(0,0,0,date('m'),date('d')-1,date('Y')); 
                         $end=mktime(0,0,0,date('m'),date('d'),date('Y'));  
                              };                  
                            break;    
                       case 9: { // 前七天                                                                     
                               $start = mktime(0,0,0,date('m'),date('d')-6,date('Y')); 
                               $end=mktime(date('H'),date('m'),date('s'),date('m'),date('d'),date('Y'));      
                              };
                              break; 
                            default:;             
                }
                if (isset($start) && isset($end)) { 
                       $where['pay_time'] = array("BETWEEN",array($start,$end));     
                      }        
                         $total = M('spend',"tab_")->field("SUM(pay_amount) as amount")->where($where)->group("promote_id")->select();
                         $total = $this->huanwei($total[0]['amount']); 
                         return $total;
                     }   

    private function huanwei($total) {
                         $total = empty($total)?'0':trim($total.' '); 
                         $len = strlen($total); 
                         if ($len>16) { 
                         // 兆       
                         $len = $len - 20; 
                          $total = $len>0?($len>4?($len>8?round(($total/1e28),4).'万亿兆':round(($total/1e24),4).'亿兆'):round(($total/1e20),4).'万兆'):round(($total/1e16),4).'兆';          
                           } else if ($len>8) { 
                           // 亿       
                          $len = $len-12;  
                            $total = $len>0?(round(($total/1e12),4).'万亿'):round(($total/1e8),4).'亿';    
                             } else if ($len>4) {
                              // 万 
                               $total = (round(($total/10000),4)).'万';    
                            }  
                         return $total; 
     }

    /**
	* 我的基本信息
    */
    public function base_info(){
    	if(IS_POST){
    		$type = I('request.type',0);
    		$user = new PromoteApi();
    		$data = $_POST;
    		$res  = $user->edit($data);
    		if($res !==false){
    			$this->success('修改成功');
    		}
    		else{
    			$this->error('修改失败');
    		}
        }
        else{
            $model = M('Promote','tab_');
	        $data = $model->find(session("promote_auth.pid"));
	        $this->meta_title = "基本信息";
	        $this->assign("data",$data);
	        $this->display();
        }
    }

    /**
	*子账号
    */
    public function mychlid($p=0){
        $map['parent_id'] = session("promote_auth.pid");
        parent::lists("Promote",$p,$map);
    }

    public function add_chlid(){
        if(IS_POST){
            $user = new PromoteApi();
            $check = $user->checkAccount($_POST['account']);
            if ($check==false) {
               $this->error("该渠道名已被注册！！");
            }  
            $res = $user->promote_add($_POST);
            if($res){
                $this->success("子账号添加成功",U('Promote/mychlid'));
            }
            else{
                $this->error("添加子账号失败");
            }
        }
        else{
            $this->display();
        }
        
    }

    public function edit_chlid($id = 0){
        if(IS_POST){
            $user = new PromoteApi();
            $res = $user->edit();
            if($res){
                $this->success("子账号修改成功",U('Promote/mychlid'));
            }
            else{
                $this->error("修改子账号失败");
            }
        }
        else{
            $promote = A('Promote','Event');
            $promote->baseinfo('edit_chlid',$id);
        }
        
    }
    public function alipay($value='')
    {
        $alipay_limit = M('promote','tab_')->field('alipay_limit')->find(session('promote_auth.pid'));
        $this->assign('alipay_limit',$alipay_limit);
        
        $this->assign('promote_auth',session());
        $this->display();
    }
    /**
     * 支付宝充值平台币记录
     */
    public function alipay_list(){
        $p =0;
         $map['promote_id'] = session('promote_auth.pid');
        if(!empty($_REQUEST['time-start'])&&!empty($_REQUEST['time-end'])){

            $map['create_time']  =  array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])+24*60*60-1));

             unset($_REQUEST['time-start']);unset($_REQUEST['time-end']);

        }
        if(isset($_REQUEST['pay_order_number'])&&trim($_REQUEST['pay_order_number'])){
            $map['pay_order_number']=$_REQUEST['pay_order_number'];
            unset($_REQUEST['pay_order_number']);
        }
        if(isset($_REQUEST['pay_status'])){
            if($_REQUEST['pay_status']==''){
                unset($_REQUEST['pay_status']);
            }else{
                $map['pay_status'] = $_REQUEST['pay_status'];
                unset($_REQUEST['pay_status']);
            }
        }
        if(!empty($_REQUEST['p'])){
            $p =$_REQUEST['p'];
             unset($_REQUEST['p']);

        }


         $total = M('promote_deposit',"tab_")->where($map)->sum('pay_amount');
        $this->assign("total_amount",$total); 
        $this->lists("promote_deposit",$p,$map);
    }
    /**
     *支付宝支付
     */
    public function beginPay(){
        #判断账号是否存在
        
        $promote = get_promote_entity($_POST['uname1'],true);
        
        $paccount = M('promote','tab_')->where('id = '.$promote['parent_id'])->find();

        if(empty($promote)){$this->error("渠道不存在");exit();}
        //判断是否开启支付宝充值
        if(pay_set_status('alipay')==0){
            $this->error("网站未启用支付宝充值",'',1);
            exit();
        }

        #支付配置
        $data['order_no'] = 'QD_'.date('Ymd').date ( 'His' ).sp_random_string(4);

        switch ($_POST['apitype']) {
            case 'alipay':
                $data['fee']      = $_POST['amount'];
                $data['pay_type'] = $_POST['apitype'];
                $data['config']   = "alipay";
                $data['service']  = "create_direct_pay_by_user";
                $data['pay_way']  = 1;
                break;
            case 'weixin':
                $data['fee']      = 0.01;//$_POST['amount'];
                $data['pay_type'] = "swiftpass";
                $data['config']   = $_POST['apitype'];
                $data['service']  = "pay.weixin.native";
                $data['pay_way']  = 2;
                break;
            default:
                # code...
                break;
        }

        //页面上通过表单选择在线支付类型，支付宝为alipay 财付通为tenpay
        $pay = new \Think\Pay($data['pay_type'],C($data['config']));
        $vo = new \Think\Pay\PayVo();
        $promote_deposit_data['order_number']     = "";
        $promote_deposit_data['pay_order_number'] = $data['order_no'];
        $promote_deposit_data['promote_id']          = $promote['id'];
        $promote_deposit_data['promote_account']     = $promote['account'];
        $promote_deposit_data['promote_nickname']    = $promote['nickname'];
        $promote_deposit_data['parent_id']       = $promote['parent_id'];
        $promote_deposit_data['parent_account']  = $paccount['account'];
        $promote_deposit_data['pay_amount']       = $data['fee'];
        $promote_deposit_data['pay_status']       = 0;
        $promote_deposit_data['pay_way']          = $data['pay_way'];
        $promote_deposit_data['pay_source']       = 0;
        $promote_deposit_data['pay_ip']           = get_client_ip();
        $promote_deposit_data['create_time']      = NOW_TIME;
        $vo->setBody("平台币充值")
            ->setFee($data['fee'])//支付金额
            ->setTitle("平台币")
            ->setOrderNo($data['order_no'])
            ->setService($data['service'])
            ->setSignType("MD5")
            ->setPayMethod("direct")
            ->setTable("promote_deposit")
            ->setAlipayToPromote($promote_deposit_data);
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

}