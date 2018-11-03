<?php
namespace Phone\Controller;
use Media\Controller\MemberController;
use User\Api\MemberApi;
use Org\XiguSDK\Xigu;
use Media\Controller\DownController;

class UserController extends BaseController{
	public $sign_getaccount;
  public $user_id;
	public function __construct(){
		$this->is_login();
    parent::__construct();

	}
  /**
   * 判断用户是否登录
   */
    protected function is_login(){
    $login_sign = I('post.sign');
    $account = I('post.account');
    $where['login_sign'] = $login_sign;
    $res = M('login_sign','tab_')->where($where)->find();
    if (empty($login_sign) || !$res) {
        $this->out(array('status'=>'-1','msg'=>'请先登录'));exit;
    }else{
    	if($account != $res['user_account']){
    		$this->out(array('status'=>'-9','msg'=>'参数错误'));exit;
    	}else{
        $this->user_id=$res['user_id']; 
      }

    }

  }
  /**
   * 个人中心接口
   */
  public function user_center(){
    
    $account = I('post.account');
    
    $where['account'] = $account; 
    $userinfo = M('user','tab_')->field('id,nickname,balance,points')->where($where)->find();
    $this->out($userinfo);exit;
  }
   		
  /**
    * 平台币充值记录接口
    * @author zky
    */
  public function paidrecord(){
      $account = I('post.account');
      $obj = M('deposit','tab_');
      $where['user_account'] = $account;
      $where['pay_status'] = 1;
      $where['pay_way'] = 1;
      $data = $obj
          ->field('user_account,create_time,pay_amount,pay_status,pay_way')
          ->where($where)
          ->order('create_time desc')
          ->select();
     
      $back['data']=$data;
     
      //print_r($back);exit;
      $this->out($back);exit;
  }
  /**
   * APP消费记录页面接口(游戏内)
   * @author whh
   */
  public function game_spend(){
      
      $where['pay_status']=1;
      $where['user_account']=I('post.account');
      $spend_model=M('spend','tab_');
      $spend_data1=$spend_model
                  ->field('id,game_name,pay_amount,pay_time')
                  ->where($where)
                  ->order("pay_time desc")
                  ->limit(10)
                  ->select();
      $spend_data['spend']=$spend_data1;
      $bindspend_model=M('bind_spend','tab_');
      $bindspend_data1=$bindspend_model
                  ->field('id,game_name,pay_amount,pay_time')
                  ->where($where)
                  ->order("pay_time desc")
                  ->limit(10)
                  ->select();
      $spend_data['bind_spend']=$bindspend_data1;
      $this->out($spend_data);
  }
  /**
   * 充值页面接口
   */
  public function user_recharge(){
    $deposit = M('deposit','tab_');
    $info =$deposit->create();
    $info['create_time']=time();
    $info['pay_status']=0;
    $res = $deposit->add($info);
    if($res){
      $data['status'] = 1;
      $data['msg'] = 'ok';
      $this->out($data);exit;
    }else{
      $data['status'] = 0;
      $data['msg'] = '服务器故障，请重试';
      $this->out($data);exit;
    }
  }
   /**
     * 绑定平台币余额接口
     * @author zky
     */
    public function  bindplatcoin(){
        $account = I('post.account');
        $data = M('user_play','tab_');
        $obj = $data
            ->field('a.user_account,a.game_name,a.bind_balance,b.icon')
            ->alias('a')
            ->join('left join tab_game as b on a.game_name = b.game_name')
            ->where("user_account = $account")
            ->group('game_name')
            ->select();
        foreach($obj as $k=> &$v){
            $v['icon']=get_cover($v['icon'],'path');
        }
        $coin['bindplatcoin']=$obj;
        $this->out($coin);exit;
    }
  /**
   * APP站内信列表(未读信息展示)
   */
  public function messages(){
      
      $user_account=I('post.account');
      $where['rec_account'] =I('post.account');
      $read_ids = M('message_letter','tab_')->where($where)->getfield('message_id',true);
     
      unset($where['rec_account']);
      if($read_ids){
          $read_str = implode($read_ids, ',');
          $where['id'] = array('not in ',$read_ids);
      }
      $where['status'] = 0;
      //print_r($where);exit;
      $messages_data = M('inside_letter','tab_')->where($where)->order('id desc')->select();
      //echo M('inside_letter','tab_')->getlastsql();exit;
      $unread_data['unread_data']=$messages_data;
     // print_r($unread_data);exit;
      $this->out($unread_data);
      
  }

  /**
   * APP用户站内信通过标记已读处理
   */
  public function allread_letter(){
      $ids= I('post.ids'); 
      $ids = explode(',',$ids);       
      $send_account = I('post.send_account');
      $send_account = explode(',',$send_account);  
      $rec_acount = I('post.account');
      $create_time = time();        
      foreach ($ids as $key => &$value) {
      $data[$key]['send_account'] = $send_account[$key];
      $data[$key]['rec_account'] = $rec_acount;
      $data[$key]['create_time'] = time();
      $data[$key]['message_id'] = $value;
      }
      $res = M('message_letter','tab_')->addAll($data);
      
  }
  /**
   * APP用户站内信删除
   */
  public function del_letter(){
      $ids= I('post.ids'); 
      $ids = explode(',',$ids);       
      $send_account = I('post.send_account');
      $send_account = explode(',',$send_account);  
      $rec_acount = I('post.account');
      $create_time = time();       
      foreach ($ids as $key => &$value) {
      $data[$key]['send_account'] = $send_account[$key];
      $data[$key]['rec_account'] = $rec_acount;
      $data[$key]['create_time'] = $create_time;
      $data[$key]['message_id'] = $value;
      $data[$key]['status'] = 1;
      }
      $res = M('message_letter','tab_')->addAll($data);
      
  }

  /**
   * APP用户站内信已读展示
   */
  public function readed(){
      $where['status'] = 0; 
      $where1['status'] = 1; 
      $user_account=I("post.account");
      
      
      //print_r($user_account['user_account']);exit;
      $where1['rec_account'] = I('post.account');
      $mids = M('message_letter','tab_')-> where($where1)->getfield('message_id',true);
      
      if($mids){
          $read_str = implode($mids, ',');
          $where['message_id'] = array('not in ',$read_str);
      }
      $where['rec_account'] = $user_account;
      $read_ids = M('message_letter','tab_')->where($where)->getfield('message_id',true);
      unset($where['rec_account']);
      unset($where['message_id']);
      $read_str = implode($read_ids, ',');
      $where['id'] = array('in ',$read_str);
      $read_data1 = array();

      //已读信息
      if($read_str){
          $where['status'] = 0;
          $read_data1 = M('inside_letter','tab_')->where($where)->order('id desc')->select();
      }
      $read_data['read_data']=$read_data1;
      //print_r($read_data);exit;
      $this->out($read_data);
  }

  /**
   * 未读站内信详情
   */
  public function detail(){
      $mid = I('post.id');
      
      //print_r($mid['id']);exit;
      $detail_data1 = M('inside_letter','tab_')->find($mid); 
      $detail_data['detail_data']=$detail_data1;
      //print_r($detail_data);exit;
      
      $data['send_account'] = $detail_data1['send_account'];
      $data['rec_account'] = I('post.account');
      $data['message_id'] = $detail_data1['id'];
      $data['create_time'] = time();

      $res = M('message_letter','tab_')->add($data);
      $where['id'] = $mid;
      $res = M('inside_letter','tab_')->where($where)->setInc('number',1);
      $this->out($detail_data);

  }
  /**
   * 已读站内信详情
   */
  public function detail_readed(){
      $mid = I('post.id');
      //$mid = json_decode($mid,true);
      //print_r($mid['id']);exit;
      $detail_data1 = M('inside_letter','tab_')->find($mid);
      //print_r($detail_data);exit;
      $detail_data['detail_data']=$detail_data1;
      $this->out($detail_data);

  }
  /**
   * 客服中心接口
   * @author zky
   */
  public function callcenter(){
      //工作时间
      $service['call_data'][]['time']="10:00--21:00";
      //服务热线
      $service['call_data'][]['phone1']="01085655922";
      $service['call_data'][]['phone2']="18301467532";
      //客服QQ
      $service['call_data'][]['serverqq']="2305194405";
      //官方玩家群
      $service['call_data'][]['playersqq']="545102333";
      //客服邮箱
      /*$service['call_data'][]['email1']="1620214981@qq.com";
      $service['call_data'][]['email2']="2356643339@qq.com";*/
      //投诉邮箱
      //$service['call_data'][]['email3']="2356643339@qq.com";
      $this->out($service);exit;
  }
  /**
  * APP密码修改接口
  * @author  whh <[email address]>
  */
  public function changepwd(){
      
      $user=I('post.');
      $old_password =$user['old_password'];
      $new_password = $user['new_password'];
      $old_password_dec=aes_decode(base64_decode($old_password));
      $new_password_dec=aes_decode(base64_decode($new_password));
      $user_account=$user['account'];
      
      $user_vcode=$user['vcode'];
      if (empty($user_account) || empty($old_password_dec)|| empty($new_password_dec) || empty($user_vcode)) {
          //APP传过来的东西为空
           $msg = json_encode(array('status'=>-3,'msg'=>'请完善信息'));
                      echo $msg;exit;
      } else {
          $vcode_now=getVcode($user_account,$old_password,$new_password,$this->key);

          $msg=vcodeVerify($vcode_now);
          if (!$msg) {

              $msg = json_encode(array('status'=>-4,'msg'=>'签名验证失败'));
                      echo $msg;exit;
          } else {
              $where=array('account'=>$user_account);
               
              $old_pwd_now = think_ucenter_md5($old_password_dec,UC_AUTH_KEY);
              $old_pwd =  M('user','tab_')->where($where)->getfield('password');
               
              if($old_pwd != $old_pwd_now){
                 $msg = json_encode(array('status'=>-5,'msg'=>'原始密码输入有误'));
                      echo $msg;exit;
              }
              $data['password'] = think_ucenter_md5($new_password_dec,UC_AUTH_KEY);
              $result=M('user','tab_')->where($where)->save($data);
              if ($result===false) {

                  $msg = json_encode(array('status'=>-2,'msg'=>'修改密码失败，服务器故障'));
                      echo $msg;exit;
              } else if($result == 0){

                  $msg = json_encode(array('status'=>-6,'msg'=>'新密码不能与原始密码相同'));
                  
                      echo $msg;exit;
              }else{
                  $msg = json_encode(array('status'=>1,'msg'=>'ok'));
                  
                      echo $msg;exit;
              }
              
          }
          
          
      }
  }
  /**
  * APP实名认证第一步密码判断接口
  * @author  whh <[email address]>
  */
  public function real_verifypwd(){    
      $where['account']=I('post.account');
      if (empty($where['account']) ) {
          $msg = json_encode(array('status'=>-3,'msg'=>'账号或密码不能为空'));
                      echo $msg;exit;
      } else {
                  $result=M('user','tab_')->where($where)->field('password,idcard')->find();
                  if ($result['idcard']) {

                      $msg = json_encode(array('status'=>-1,'msg'=>'您已实名认证'));
                      echo $msg;exit;
                  } else {
                      $msg = json_encode(array('status'=>1,'msg'=>'您还没有实名认证'));
                      echo $msg;exit;
                  }
      } 
  }


  /**
  * APP实名认证数据插入接口
  * @author  whh <[email address]>
  */
  public function real_person(){    
      $user_account=I('post.account');
      $real_name=I('post.real_name');
      $idcard = I('post.idcard');
      $idcard_dec=aes_decode(base64_decode($idcard));
      $vcode=I('post.vcode');    
      if (empty($real_name)||empty($idcard_dec)||empty($vcode)) {
          $msg = json_encode(array('status'=>-3,'msg'=>'提交的数据不能为空'));
          echo $msg;exit;
      }
      $vcode_now=getVcode($user_account,$real_name,$idcard,$this->key);
      $msg=vcodeVerify($vcode_now);
      if (!$msg) {       
          $msg = json_encode(array('status'=>-4,'msg'=>'签名验证失败'));
          echo $msg;exit;
      }
      $where['account'] = $user_account;
      $data['real_name']=$real_name;
      $data['idcard']=$idcard_dec;
      $flag=M('User','tab_')->where($where)->save($data);    
      if ($flag) {
           $msg = json_encode(array('status'=>1,'msg'=>'认证成功'));
          echo $msg;exit;
      } else {
          $msg = json_encode(array('status'=>0,'msg'=>'认证失败'));
          echo $msg;exit;
      }    
  }
  // 绑定手机 lwx
  public function phone() {
      if (IS_POST) {
        $account = I('post.account');
        $phone = I('post.phone');
        $phonecode = I('post.phonecode');
        $vcode = I('post.vcode');
          if (empty($account)||empty($phone)||empty($phonecode)||empty($vcode)) {
              $msg = json_encode(array('status'=>-3,'msg'=>'提交的数据不能为空'));
              echo $msg;exit;
          }else{


          $vcode_now=getVcode($account,$phone,$phonecode,$this->key);
          $msg=vcodeVerify($vcode_now);
          if (!$msg) {
              $msg = json_encode(array('status'=>-4,'msg'=>'签名验证失败'));
              echo $msg;exit;
          }else{
             $res_phonecode = $this->check_phonecode('synchronous');
             if($res_phonecode){
                $where['account'] = $account;
                M('User','tab_')->where($where)->setField('phone',$phone);
                
                $where['phone'] = $phone;
                $flag = M('User','tab_')->where($where)->find();
                if ($flag) {
                    $data['status']=1;
                    $data['msg']='手机绑定成功';
                } else {
                    $data['status']=0;
                    $data['msg']='手机绑定失败,服务器故障,请重试';
                }
                
                $this->out($data);
             }
              
          }
          
           }
      } else {
          $msg = json_encode(array('status'=>-5,'msg'=>'请走正确的流程'));
              echo $msg;exit;
      }
      
  }
  /**
   * 手机解绑
   */
   public function changeph() {
       if (IS_POST) {
        $account = I('post.account');
        $phone = I('post.phone');
        $phonecode = I('post.phonecode');
        $vcode = I('post.vcode');
          if (empty($account)||empty($phone)||empty($phonecode)||empty($vcode)) {
              $msg = json_encode(array('status'=>-3,'msg'=>'提交的数据不能为空'));
              echo $msg;exit;
          }else{


          $vcode_now=getVcode($account,$phone,$phonecode,$this->key);
          $msg=vcodeVerify($vcode_now);
          if (!$msg) {
              $msg = json_encode(array('status'=>-4,'msg'=>'签名验证失败'));
              echo $msg;exit;
          }else{
             $res_phonecode = $this->check_phonecode('synchronous');
             if($res_phonecode){
                $where['account'] = $account;
                $where['phone'] = $phone;
                M('User','tab_')->where($where)->setField('phone','');
                $where['phone'] = '';
                
                $flag = M('User','tab_')->where($where)->find();
                if ($flag) {
                    $data['status']=1;
                    $data['msg']='手机解绑成功';
                } else {
                    $data['status']=0;
                    $data['msg']='手机解绑失败,服务器故障,请重试';
                }
                
                $this->out($data);
             }
              
          }
          
           }
      } else {
          $msg = json_encode(array('status'=>-5,'msg'=>'请走正确的流程'));
              echo $msg;exit;
      }
      
  }
     
  /**
    * 登陆状态退出
    */
    public function logout(){
    $account = I('post.account');
    $login_sign = I('post.sign');
    $where['login_sign'] = $login_sign;
    $where['user_account'] = $account;
    
    $res = M('login_sign','tab_')->where($where)->delete();
    if (!$res) {
        $this->out(array('status'=>'-1','msg'=>'退出失败'));exit;
    } else{
      $this->out(array('status'=>'1','msg'=>'ok'));exit;
    }
  }
  /**
   * 个人中心礼包列表
   */
    public function gift_list(){
      $where['user_id'] = $this->user_id;

      $list = M('gift_record','tab_')
      ->alias('g')
      ->field('g.game_name,g.novice,b.giftbag_name,b.desribe,b.end_time')
      ->join ('tab_giftbag as b on g.gift_id = b.id')
      ->where($where)
      ->order('g.create_time desc')
      ->select();
      $time = time();
      foreach ($list as $key => &$value) {
         $value['surplus']=floor(($value['end_time']- $time)/86400);
         unset($value['end_time']);
      }
      $gift_list['gift_list'] = $list;
      $this->out($gift_list);exit;
  }
  /**
     * 修改昵称
     */
  public function modify_nickname(){
    
    $data['nickname'] = I('nickname');
    $where['account'] = I('account');
    if(empty($data['nickname'])){
      $this->out(array('status'=>'-1','msg'=>'昵称不能为空'));exit;
    }
    $res =  M('user','tab_')->where($where)->save($data);
    if($res){
        $this->out(array('status'=>'1','msg'=>'ok'));exit;
    }else{
        $this->out(array('status'=>'0','msg'=>'修改昵称失败，请重试'));exit;
    }
  }
  /**
   * 验证是否绑定手机号
   */
  public function get_phone(){
    $where['account'] = I('post.account');
    $phone = M('user','tab_')->where($where)->getfield('phone');
    if($phone){
      $this->out(array('status'=>'1','msg'=>$phone));exit;
    }else{
      $this->out(array('status'=>'-1','msg'=>''));exit;
    }

  } 
    
  /**
   * APP礼包领取接口
   * @author whh
   */
  public function gift_record(){
           $record_data=I('post.');
           //print_r($record_data);exit;
            $record_data['user_id']=$this->user_id;
           
           //print_r($record_data);exit;
           $giftrecord_model=M("gift_record","tab_");
           $giftrecord_map['user_id']=$record_data['user_id'];
           $giftrecord_map['gift_id']=$record_data['gift_id'];
           $is=$giftrecord_model->where($giftrecord_map)->find();
           //print_r($is);exit;
           if ($is) {
                  $gift_status['record'][]['status']=1;//领取过
                  $gift_status['record'][]['novice']=$is['novice'];
                  //print_r($gift_status);exit;
                  echo  json_encode($gift_status);exit;
           } else {
                  $gift_status['record'][]['status']=0;//没有领取过 

                  $bag=M('giftbag','tab_');                              
                  $giftid= $record_data['gift_id'];
                  $ji=$bag->where(array("id"=>$giftid))->field("novice")->find();
                  
                  $at=explode(",",$ji['novice']);
                  $gameid=$bag->where(array("id"=>$giftid))->field('game_id')->find();
                  $add['game_id']=$gameid['game_id'];
                  $add['game_name']=get_game_name($gameid['game_id']);
                  $add['gift_id']=$record_data['gift_id'];
                  $add['gift_name']=$record_data['gift_name'];
                  $add['server_id'] =$record_data['server_id'];
                  $add['status']=1;
                  $add['novice']=$at[0];
                  $add['user_id'] =$record_data['user_id'];
                  $add['user_account'] =$record_data['account'];
                  $add['user_nickname'] =$record_data['user_nickname'];
                  $add['create_time']=strtotime(date('Y-m-d h:i:s',time()));

                  $giftrecord_model->add($add);
                  
                  //echo $giftrecord_model->getlastsql();exit; 
                  
                  $new=$at;
                  if(in_array($new[0],$new)){
                          $sd=array_search($new[0],$new);
                          unset($new[$sd]);
                  }
                  $act['novice']=implode(",", $new);
                  $bag->where("id=".$giftid)->save($act);
                  $gift_status['record'][]['novice']=$at[0];
                  //print_r($gift_status);exit;
                  echo  json_encode($gift_status);exit;
         

           }
           
            
    }
      /**
   * @author zdd
   * 积分记录 默认显示的最新10条
   */
  public function points_record(){
        $map['user_account'] =  I('post.account');
        if(isset($_REQUEST['time-start'])&&isset($_REQUEST['time-end'])){
            $map['create_time'] =array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])+24*60*60-1));
            unset($_REQUEST['time-start']);unset($_REQUEST['time-end']);
        }
        if(isset($_REQUEST['user_account'])){
            $map['user_account']=$_REQUEST['user_account'];
            unset($_REQUEST['user_account']);
        }
        if(isset($_REQUEST['start'])&&isset($_REQUEST['end'])){
            $map['create_time'] =array('BETWEEN',array(strtotime($_REQUEST['start']),strtotime($_REQUEST['end'])+24*60*60-1));
            unset($_REQUEST['start']);unset($_REQUEST['end']);
        }
        if(isset($_REQUEST['pay_way'])){
            $map['pay_way']=$_REQUEST['pay_way'];
            unset($_REQUEST['pay_way']);
        }
        $row=10;
        $page = intval($_GET['p']);
        $page = $page ? $page : 1; //默认显示第一页数据
        $model=M('points_record','tab_');
        $data=$model
        ->where($map)
        ->order('id desc')
        ->page($page, 10)
        ->select();
       /* $count=$model
        ->where($map)
        ->count();*/
        $record['list'] = $data;
        $this->out($record);exit;
       
    }
  /**
   * @author zdd
   * 激活码兑换
   */
  public function cdkey(){
    $cdkey = trim(I('post.code'));
    if(IS_POST){
      $exchange = M('Exchange','tab_');
      $exchange_record = M('Exchange_record','tab_');
      if(!$cdkey){
          //$this->ajaxReturn(array('status'=>-1,'msg'=>'激活码不能为空'));
          $data['status'] = -1;
          $data['msg'] = '激活码不能为空';
          $this->out($data);exit;
      }else{
        $where_rec['cdkey'] = $cdkey;
        $res_rec =$exchange_record ->where($where_rec)->find();
        if($res_rec){
          //$this->ajaxReturn(array('status'=>-2,'msg'=>'该激活码已领取过'));
          $data['status'] = -2;
          $data['msg'] = '该激活码已领取过';
          $this->out($data);exit;
        }
      }
      $where['sign'] = substr($cdkey,0,4);

      

      $rec_exchange = $exchange->where($where)->find();
      if(!$rec_exchange){
        //$this->ajaxReturn(array('status'=>-3,'msg'=>'激活码输入有误'));
        $data['status'] = -3;
        $data['msg'] = '激活码输入有误';
        $this->out($data);exit;
      }else{
        $time = time();
        
        $novice_arr = explode(',',$rec_exchange['novice']);
        if(in_array($cdkey,$novice_arr)){
          if($time < $rec_exchange['start_time']){
           // $this->ajaxReturn(array('status'=>-5,'msg'=>'还未开放兑换'));
            $data['status'] = -5;
            $data['msg'] = '还未开放兑换';
            $this->out($data);exit;
          }else if($time >$rec_exchange['end_time'] || $rec_exchange['status'] != 1){
            //$this->ajaxReturn(array('status'=>-6,'msg'=>'激活码已失效'));
            $data['status'] = -6;
            $data['msg'] = '激活码已失效';
            $this->out($data);exit;
          }
          $k = array_search($cdkey,$novice_arr);
          unset($novice_arr[$k]);

          $novice_data['novice'] = implode(',',$novice_arr);
          $where_save['id'] = $rec_exchange['id'];
          $novice_save = $exchange->where($where_save)->save($novice_data);
          if($novice_save === false){
            //$this->ajaxReturn(array('status'=>-4,'msg'=>'服务器故障'));
            $data['status'] = -4;
            $data['msg'] = '服务器故障';
            $this->out($data);exit;
          }
          $where_user['account'] = I('post.account');
          
          $res_save = M('User','tab_')->where($where_user)->setInc('balance',$rec_exchange['goods_num']);
          if($res_save !== false){
            $record_data['user_account'] = I('post.account');
            $record_data['user_id'] = $this->user_id;
            $record_data['describe'] = $rec_exchange['describe'];
            $record_data['goods_num'] = $rec_exchange['goods_num'];
            $record_data['status'] = 1;
            $record_data['cdkey'] = $cdkey;
            $record_data['create_time'] = time();
            $exchange_record->add($record_data);
            //$this->ajaxReturn(array('status'=>1,'msg'=>'ok'));
            $data['status'] = 1;
            $data['msg'] = 'ok';
            $this->out($data);exit;
          }else{
            //$this->ajaxReturn(array('status'=>-4,'msg'=>'服务器故障1'));
            $data['status'] = -4;
            $data['msg'] = '服务器故障';
            $this->out($data);exit;
          }
          
        }else{
            //$this->ajaxReturn(array('status'=>-3,'msg'=>'激活码输入有误'));
            $data['status'] = -3;
            $data['msg'] = '激活码输入有误';
            $this->out($data);exit;
        }
      }

    }else{
      $data['status'] = -8;
      $data['msg'] = '非法操作';
      $this->out($data);exit;
    }
  }
  /**
   * 激活码兑换记录
   */
  public function cdkey_record(){
    //$user = session("member_auth");
    
    $where_user['user_account'] = I('post.account');
    $list = M('Exchange_record','tab_')->where($where_user)->order('id desc')->limit(10)->select();
    $record['list'] = $list;
    $this->out($record);exit;
  }
}