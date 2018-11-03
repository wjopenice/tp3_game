<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
namespace Media\Controller;
use User\Api\MemberApi;
use Org\XiguSDK\Xigu;
use Org\Util\Memcache as Memcache;


/**
 * 文档模型控制器
 * 文档模型列表和详情
 */
//http://www.new7858.com/media.php?s=/Member/personalCenter
class MemberController extends BaseController {
  public $userClass;
  private $_cache;
	public function __construct(){
        parent::__construct();
        //初始化并传memcache前缀
        $this->_cache = Memcache::instance();
        $arr = array(
             "Member/game_list","Member/getGameGift","Member/checkUser",
             "Member/checkPhone","Member/telregister","Member/user_register",
             "Member/getE","Member/is_login","Member/telsvcode","Member/emailbangcheck",
             "Member/login","Member/sort_array","Member/bindemailfinish","Member/preg","Member/register","Member/telregister_promote"
        );
        $this->userClass = new MemberApi();
        
        $finish="bindemailfinish";
        $path=$_SERVER['PATH_INFO'];
        $pos=strpos($path,$finish);
        $finish2="preg";
        $pos2=strpos($path,$finish2);
        //file_put_contents('E:/aaa.html',json_encode($pos.'---'.$path.'----'.$finish));exit;
        //file_put_contents('E:/aaa.html',json_encode($_SERVER['PATH_INFO']),FILE_APPEND);
        if ($pos===false ) {
            if($pos2 !== false){

            }else{
                $bool=in_array($_SERVER['PATH_INFO'],$arr,true);
                if ($bool !==true) {
                    //file_put_contents('E:/aaa.html',json_encode($_SERVER['PATH_INFO']),FILE_APPEND);
                    $mid = parent::is_login();
                    if ($mid<1) {
                        $this->redirect("Public/login");
                    }
                }
            }
            
        }else{
            $newpath=mb_substr($path,0,22);
            $_SERVER['PATH_INFO']=$newpath;
            //file_put_contents('E:/aaa.html',json_encode($_SERVER['PATH_INFO']),FILE_APPEND);exit;
             $bool=in_array($_SERVER['PATH_INFO'],$arr,true);
            if ($bool !==true) {
                //file_put_contents('E:/aaa.html',json_encode($_SERVER['PATH_INFO']),FILE_APPEND);
                $mid = parent::is_login();
                if ($mid<1) {
                    $this->redirect("Public/login");
                }
            }
       }
    }

     /**
	 * 个人中心 基本信息模块
	 * @author  whh <[email address]>
	 */
     public function personalcenter(){
        $user = M('User','tab_');
        $map['id']=session('member_auth.mid');
        $data=$user->where($map)->find();
        if($data['idcard']){
            $data['idcard'] = substr_replace($data['idcard'],'*************',3,13);
        }
        $this->assign('user',$data);
     	$this->display();
     }

     /**
	 * 个人中心 账户余额模块
	 * @author  whh <[email address]>
	 */
     public function pcaccountyue(){
        $user = session("member_auth");
        if(I('get.p')){
            $p = I('get.p');
        }else{
            $p = 1;
        }
        if(I('post.pagesize')){
            $row = I('post.pagesize');
        }else{
            $row = 10;
        }
        $map['user_id']=$user['mid'];
        $binddata = M("user_play","tab_")
            // 查询条件
            ->where($map)
            ->group('user_account,game_name')
            ->page($p,$row)
            ->select();
        $count = M('user_play','tab_')->where($map)->count();
        $this->assign('list_data', $binddata);
        $this->assign('count', $count);
        $map1['id']=$user['mid'];
        $data=M('user','tab_')->where($map1)->getfield('balance');
        $this->assign('balance',$data);
     	$this->display();
     }
    

    /**
     * 登录
     * @author  whh <[email address]>
     */
    public function login(){
        //file_put_contents('E:/aaa.html',json_encode($_POST));
        if(empty($_POST['account']) || empty($_POST['password'])){
            return $this->ajaxReturn(array('status'=>0,'msg'=>'账号或密码不能为空'));
        }
        //用户名正则判断
        if(!preg_match("/^[a-zA-Z0-9_]{6,15}$/",$_POST['account'])){
            //用户名格式不正确
            return $this->ajaxReturn(array('status'=>0,'msg'=>'用户名格式不正确！'));
        }
        /*if(!preg_match("/^[a-zA-Z0-9_]{6,15}$/",empty($_POST['password'])))
        {
            return $this->ajaxReturn(array('status'=>0,'msg'=>'密码格式不正确！'));
        }*/
        //密码输错超过3次判断图片验证码
        $user_ip=get_client_ip();
        $key = "media_login_password_".$user_ip;
        $is=$this->_cache->get($key);
        if (!empty($_POST['verify']) || $is>=3) {
           $verify = new \Think\Verify();
           if (!$verify->check($_POST['verify'])) {
             $data = array('status'=>0,'msg'=>'验证码错误！！');
             return $this->ajaxReturn($data);
           }
           
        }
        
        $data = array();
        $member = new MemberApi();
        $res = $member->login($_POST['account'],$_POST['password']);
        if($res > 0){
            parent::autoLogin($res);
            $refer = session('refer');
            if($refer ){
               $reurl = $refer;
            }else{
               $reurl = U('Index/index');
            }
            //清除memcache  密码错误的ip
            $this->_cache->rm($key);
            return $this->ajaxReturn(array('status'=>1,'reurl'=>$reurl));
        }else{
            switch ($res) {
                case -1:
                    $data = array('status'=>0,'msg'=>'用户不存在或被禁用,请联系客服');
                    break;
                case -2:
                    //获取用户ip
                    $user_ip=get_client_ip();
                    $key = "media_login_password_".$user_ip;
                    $is=$this->_cache->get($key);

                    //设置超时时间1小时
                    //获取当前IP密码输错次数
                    if (empty($is)) {
                       $this->_cache->set($key, 1, 3600);
                       $data = array('status'=>0,'msg'=>'密码错误');
                    }
                    else{
                      //密码输错次数大于三次  用户需输入验证码
                      $is++;
                      if ($is >= 3) {
                        $data = array('status'=>-999,'msg'=>'请输入正确的密码！！');
                      } else {
                        $this->_cache->set($key, $is, 3600);
                         $data = array('status'=>0,'msg'=>'密码错误');
                      }
                    }                    
                    break;
                default:
                    $data = array('status'=>0,'msg'=>'未知错误');
                    break;
            }
            return $this->ajaxReturn($data);
        }
    }

     /**
     * 个人中心  安全中心-密码修改
     * @author  whh <[email address]>
     */
     public function pcsafecenter(){
        $user = session('member_auth');
        
        if (IS_POST) { 
            /*$name = I('name');
            $oldpassword=I('oldpassword');
            $newpassword=I('newpassword');*/
            $name = $_POST['name'];
            $oldpassword=$_POST['oldpassword'];
            $newpassword=$_POST['newpassword'];
            //file_put_contents('E:/aaa.html',json_encode($name.'-----'.$oldpassword.'-----'.$newpassword));
            if (empty($name) || empty($oldpassword) || empty($newpassword)) {
                echo $this->ajaxReturn(array('status'=>-1,'msg'=>'请完善信息！'));exit;
            }
            //判断账号是否登录  后续加上
            $mid = parent::is_login();
            if ($mid <1) {
                session('member_auth', null);
                session('member_auth_sign', null);
                session('[destroy]');
                echo $this->ajaxReturn(array('status'=>-2,'msg'=>'您还没有登录！'));exit;
            }
            if ($name !== $user['account']) {
                session('member_auth', null);
                session('member_auth_sign', null);
                session('[destroy]');
                echo $this->ajaxReturn(array('status'=>-3,'msg'=>'用户名不匹配！'));exit;
            }
            $wherereal['account']=$user['account'];
            $realpwd=M('user','tab_')->where($wherereal)->getField('password');
            if(think_ucenter_md5($oldpassword, UC_AUTH_KEY) === $realpwd){
                $this->pwd($user['mid'],I('newpassword'));  
            } else {
                echo $this->ajaxReturn(array('status'=>-4,'msg'=>'旧密码错误！！！'));exit; //密码错误
            }
            
        } else {
              $user = M('User','tab_')->where("id=".$user['mid'])->find();
              $this->assign('user',$user);
              $this->display();
          }
         //$this->display();
          
     }
     protected function step_verify($m,$accountm,$type){
        if (empty($m) || empty($accountm)) {
            return $data = json_encode(array('status'=>0,'msg'=>'非法操作！！1'));exit;
        }
       // file_put_contents('E:/aaa.html',json_encode($accountm['user_account'].'------'.$type));
        $accountm['type']=$type;
        $step=M('step','tab_')->where($accountm)->find();

        if ($step['m']==$m) {
            return true;
        } else {
            //file_put_contents('E:/aaa.html',json_encode($step['m'].'------'.$m));
            return $data = json_encode(array('status'=>0,'msg'=>'非法操作！！2'));exit;
        }
        
    }

      /**
     * 个人中心  安全中心-验证密码
     * @author  whh <[email address]>
     */
     public function verifypwd(){
            //默认参数
            $iss = I('iss');
            $name = I('name');
            $user = session('member_auth');
            //file_put_contents('E:/aaa.html',json_encode($iss."------".$name.'-----'.I('in').'-----'.I('password')));
            if (empty($iss) || empty($name) || $iss !== 'mgw') {
                session('member_auth', null);
                session('member_auth_sign', null);
                session('[destroy]');
                echo $this->ajaxReturn(array('status'=>0,'msg'=>'参数为空'));exit;
            }
            //判断账号是否登录  
            $mid = parent::is_login();
            if ($mid <1) {
                session('member_auth', null);
                session('member_auth_sign', null);
                session('[destroy]');
                echo $this->ajaxReturn(array('status'=>0,'msg'=>'请先登录！！！'));exit;
            }
            if ($name !== $user['account']) {
                session('member_auth', null);
                session('member_auth_sign', null);
                session('[destroy]');
                echo $this->ajaxReturn(array('status'=>0,'msg'=>'用户名不匹配！！！'));exit;
            }

            $this->userClass = new MemberApi();
            $res = $this->userClass->login($user['account'],I('password'));
            if ($res=='-2') {

                 echo $this->ajaxReturn(array('status'=>-2,'msg'=>'密码错误'));exit;

            } elseif($res=='-1'){
                  echo $this->ajaxReturn(array('status'=>-1,'msg'=>'用户名不存在或被禁用'));exit;
                
            } else{
                   $in=I('post.in');
                   if ($in==='idcard') {
                       echo $this->ajaxReturn(array('status'=>1,'msg'=>'OK',));
                   } else {
                       if ($in==='bindphone') {
                        //绑定手机号防跳步  步骤修改
                        $wherem['type']=2;
                        } elseif($in==='bindemail') {
                            $wherem['type']=4;
                        }elseif($in==='nobindemail') {
                            $wherem['type']=5;
                        }
                            $wherem['user_id']=$user['mid'];
                            $wherem['user_account']=$user['account'];
                            
                            $is=M('step','tab_')->where($wherem)->find();
                            
                            if(empty($is)){
                               $wherem['m']=2;
                               $wherem['create_time']=time();
                               M('step','tab_')->add($wherem);
                            }else{
                               $tepm['m']=2;
                               $tepm['create_time']=time();
                               //file_put_contents('E:/aaa.html',json_encode($tepm['m']));
                               $as=M('step','tab_')->where($wherem)->save($tepm);
                               //file_put_contents('E:/aaa.html',json_encode($as.'wwww'));
                            }        
                           echo $this->ajaxReturn(array('status'=>1,'msg'=>$user['account'],));
                   }
                   
                   
            }      

     }
    /**
     * 个人中心 修改密码
     * @author  whh <[email address]>
     */
    public function pwd($uid,$password) {
        $member = new MemberApi();
        $result = $member->updatePassword($uid,$password);
        if ($result) {
            $data['status']=1;
            $data['msg']='密码修改成功';
            session('member_auth', null);
            session('member_auth_sign', null);
            session('[destroy]');

        } else {
            $data['status']=0;
            $data['msg']='密码修改失败';
        }
        echo json_encode($data);
    }

    /**
     * 注销当前用户
     * @author  whh <[email address]>
     * @return void
     */
    public function logout(){
        session('member_auth', null);
        session('member_auth_sign', null);
        if(session('refer')){
          $reurl = session('refer');
          session('refer', null);
        }else{
          $reurl = U('Index/index');
        }
        
        session('[destroy]');
        $this->ajaxReturn(array('reurl'=>$reurl));
    }


     /**
     * 个人中心 安全中心-绑定手机1
     * @author  whh <[email address]>
     */
     public function bindphone(){
          // print_r(session());exit;
          $user = session('member_auth');
          $user = M('User','tab_')->where("id=".$user['mid'])->find();
          
          //防跳步处理
          $account=I('get.account'); 
          if (empty($account)) {
                $m=1;
            } else {
                $where['user_account']=$account;
                $where['type']=2;
                $step=M('step','tab_')->where($where)->getField('m');
                //$sql=M('step','tab_')->getLastSql();
                //file_put_contents('E:/aaa.html',json_encode($sql));
                if(empty($step)){
                    $step=1;
                }else{
                    $m=$step;
                }

            }
          //file_put_contents('E:/aaa.html',json_encode($m));
          $this->assign('user',$user);
          $this->assign('m',$m);  
          $this->display();
     }

    /**
     * 判断输入的手机号是否被绑定
     * @author whh
     */
      public  function phonebangcheck($phone){
            if (I('post.in')==='bindphone') {
                //绑定手机号步骤判断
                $type=2;
            }elseif(I('post.in')==='nobindphone') {
                $type=3;
            }
            $m=2;
            $user=session('member_auth');
            $account =$user['account'];
            $accountm['user_account']=$account;
            
            $result=$this->step_verify($m,$accountm,$type);
            //file_put_contents('E:/aaa.html',json_encode($result));
            //file_put_contents('E:/aaa.html',json_encode($account));
            if ($result!==true) {
                echo $result;exit; 
            }
            

            
            $where['phone']=I('phone');
            //file_put_contents('E:/aaa.html',json_encode($where['phone']));
            if (empty($where['phone'])) {
               echo json_encode(array('status'=>-1,'msg'=>'手机号不能为空!')); exit; 
            }
            $model=M('User','tab_');
            $count=$model->where($where)->count('id');
            //echo $count;exit;
            if($count>0){
                echo json_encode(array('status'=>-1,'msg'=>'该手机号已被其他账号绑定')); exit;
            }else{
                echo json_encode(array('status'=>1,'msg'=>'OK')); exit;
            }
        }

    /**
     * 发送验证码判断
     * @author whh
     */
    public function sendvcode() {
        //file_put_contents('E:/aaa.html',json_encode(I('phone').'-------'.I('name')));
        if (!IS_POST) {
            echo json_encode(array('status'=>0,'msg'=>'非法操作'));exit;
        }
        $phone = I('phone');
        $name=I('name');

        if (empty($phone) || empty($name)) {
            echo json_encode(array('status'=>0,'msg'=>'请完善信息'));exit;
        }
        $this->telsvcode($phone);             
    }

  
    /**
     * 发送手机安全码
     * @author whh
     */
    public function telsvcode($phone=null,$delay=10,$flag=true) {
        if (empty($phone)) {
            echo json_encode(array('status'=>0,'msg'=>'手机号不能为空'));exit; 
        }
        
        /// 产生手机安全码并发送到手机且存到session
        $rand = rand(100000,999999);
        $xigu = new Xigu(C('sms_set.smtp'));
        //checksendcode($phone,C('SMS_SET_LIMIT'));
        $param = $rand.",".$delay;
        $result = json_decode($xigu->sendSM(C('sms_set.smtp_account'),$phone,C('sms_set.smtp_port'),$param),true); 
                       
        // 存储短信发送记录信息
        $result['create_time'] = time();
        $result['pid']=0;
        //$result['create_ip']=get_client_ip();
        $r = M('Short_message')->add($result);
        
        if ($result['send_status'] != '000000') {
            echo json_encode(array('status'=>0,'msg'=>'发送失败，请重新获取'));exit;
        }        
        $telsvcode['code']=$rand;
        $telsvcode['phone']=$phone;
        $telsvcode['time']=$result['create_time'];
        $telsvcode['delay']=$delay;
        session('telsvcode',$telsvcode);
        
        
        if ($flag) {
            echo json_encode(array('status'=>1,'msg'=>'验证码已发送，请查收'));        
        } else{
            echo json_encode(array('status'=>1,'msg'=>''));
        }
    }
     /**
     * 绑定手机
     * @author whh
     */
    public function phone() {
        if (I('post.in')==='bindphone') {
                //绑定手机号步骤判断
                $m=2;
                $user=session('member_auth');
                $account = $user['account'];
                $accountm['user_account']=$account;
                $type=2;
                $result=$this->step_verify($m,$accountm,$type);
                //file_put_contents('E:/aaa.html',json_encode($result));
                if ($result!==true) {
                    echo $result;exit;
                }
            }elseif(I('post.in')==='nobindphone'){
                //绑定手机号步骤判断
                $m=2;
                $user=session('member_auth');
                $account = $user['account'];
                $accountm['user_account']=$account;
                $type=3;
                $result=$this->step_verify($m,$accountm,$type);
                //file_put_contents('E:/aaa.html',json_encode($result));
                if ($result!==true) {
                    echo $result;exit;
                }
            }

        if (IS_POST) {
            $telsvcode = session('telsvcode');
            $time = (time() - $telsvcode['time'])/60;
            if ($time>$telsvcode['delay']) {
                session('telsvcode',null);unset($telsvcode);
                echo json_encode(array('status'=>0,'msg'=>'时间超时,请重新获取验证码'));exit;
            }
            $phone = $_POST['phone'];
            
            if (!($telsvcode['code'] == $_POST['vcode']) || !($telsvcode['phone'] == $phone)) {
                echo json_encode(array('status'=>0,'msg'=>'验证码输入有误'));exit;
            }
            $user = session("member_auth");
            $res = $user['mid'];
            M('User','tab_')->where("id=$res")->setField('phone',$phone);
            $flag = M('User','tab_')->where("id=$res and phone = $phone")->find();
            if ($flag['phone']==$phone) {

                if(I('post.in')==='bindphone'){
                    $wherem['type']=2;

                }elseif(I('post.in')==='nobindphone'){
                     $wherem['type']=3;
                }
                $wherem['user_account']=$user['account'];
                
                $is=M('step','tab_')->where($wherem)->find();
                //file_put_contents('E:/aaa.html',json_encode($is));
                if(empty($is)){
                   //file_put_contents('E:/aaa.html',json_encode(111));
                   echo json_encode(array('status'=>0,'msg'=>'非法操作！！'));exit;  
                }else{
                   $tepm['m']=3;
                   $tepm['create_time']=time();
                   //file_put_contents('E:/aaa.html',json_encode($tepm['m']));
                   $as=M('step','tab_')->where($wherem)->save($tepm);
                   //file_put_contents('E:/aaa.html',json_encode($as.'wwww'));
                }

                $data['status']=1;
                $data['msg']=$user['account'];
            } else {
                $data['status']=0;
                $data['msg']='手机绑定失败';
            }
            session('telsvcode',null);unset($telsvcode);
            echo json_encode($data);
        } /*else {
            $res = session('member_auth.mid');
            
            $ph = M('User','tab_')->field("phone")->where("id=$res")->find();
            
            if (!empty($ph) && is_array($ph)) {
                $this->assign('phone',$ph['phone']);
            }

            $this->assign('name',session('member_auth.account'));
            $this->display();
        }*/
        
    }
    

     /**
     * 注册时短信验证
     * @author whh
     */
    public function checktelsvcode($phone,$vcode,$flag=true) {       
        $telsvcode = session('telsvcode');
        $time = (time() - $telsvcode['time'])/60;
        if ($time>$telsvcode['delay']) {
            session('telsvcode',null);unset($telsvcode);
            echo json_encode(array('status'=>0,'msg'=>'时间超时,请重新获取验证码'));exit;
        }
        if (!($telsvcode['code'] == $vcode) || !($telsvcode['phone'] == $phone)) {
            echo json_encode(array('status'=>0,'msg'=>'验证码输入有误'));exit;
        }
        session('telsvcode',null);
        unset($telsvcode); 
        if ($flag) {
            echo json_encode(array('status'=>1));
        }
    }
     
     /**
     * 个人中心 安全中心-换绑手机1
     * @author  whh <[email address]>
     */
     public function nobindphone(){
          $user = session('member_auth');
          //防跳步处理
          $account=I('get.account'); 
          if (empty($account)) {
                $m=1;
            } else {
                $where['user_account']=$account;
                $where['type']=3;
                $step=M('step','tab_')->where($where)->getField('m');
                //$sql=M('step','tab_')->getLastSql();
                //file_put_contents('E:/aaa.html',json_encode($sql));
                if(empty($step)){
                    $step=1;
                }else{
                    $m=$step;
                }

            }
          $user = M('User','tab_')->where("id=".$user['mid'])->find();
          $this->assign('m',$m);
          $this->assign('user',$user);
          $this->display();
     }
      /**
     * 个人中心 安全中心-换绑手机2
     * @author  whh <[email address]>
     */
     public function changeph() {
        if (IS_POST) {

            $telsvcode = session('telsvcode');

            $time = (time() - $telsvcode['time'])/60;

            if ($time>$telsvcode['delay']) {

                session('telsvcode',null);unset($telsvcode);

                echo json_encode(array('status'=>0,'msg'=>'时间超时,请重新获取验证码'));exit;

            }

            $phone = $_POST['phone'];

            if (!($telsvcode['code'] == $_POST['vcode']) || !($telsvcode['phone'] == $phone)) {

                echo json_encode(array('status'=>0,'msg'=>'验证码输入有误'));exit;

            }
            $res = session("member_auth.mid");
            
            //M('User','tab_')->where("id=$res")->setField('phone','');
            
            $flag = M('User','tab_')->where("id=$res and phone = $phone")->find();
            
            if ($flag) {
                //防跳步验证  修改步数
                   $in=I('post.in');
                   if ($in==='nobindphone') {
                        //绑定手机号防跳步  步骤修改
                        $user = session('member_auth');
                        $wherem['user_id']=$user['mid'];
                        $wherem['user_account']=$user['account'];
                        $wherem['type']=3;
                        $is=M('step','tab_')->where($wherem)->find();
                        
                        if(empty($is)){
                           $wherem['m']=2;
                           $wherem['create_time']=time();
                           M('step','tab_')->add($wherem);
                        }else{
                           $tepm['m']=2;
                           $tepm['create_time']=time();
                           //file_put_contents('E:/aaa.html',json_encode($tepm['m']));
                           $as=M('step','tab_')->where($wherem)->save($tepm);
                           //file_put_contents('E:/aaa.html',json_encode($as.'wwww'));
                        }
                   }

                $data['status']=1;

                $data['msg']=$user['account'];

            } else {

                $data['status']=0;

                $data['msg']='手机号解绑失败';

            }

            session('telsvcode',null);unset($telsvcode);
            //file_put_contents('E:/aaa.html',json_encode($data));
            echo json_encode($data);
            
        } else {
            echo json_encode(array('status'=>0,'msg'=>'服务器故障'));exit;
        }
     }
     /**
     * 个人中心 安全中心-换绑手机2
     * @author  whh <[email address]>
     */
     public function bindemail(){
          //print_r(session());exit;
          $user = session('member_auth');
          $user = M('User','tab_')->where("id=".$user['mid'])->find();
          //防跳步处理
          $account=I('get.account'); 
          if (empty($account)) {
                $m=1;
            } else {
                $where['user_account']=$account;
                $where['type']=4;
                $step=M('step','tab_')->where($where)->getField('m');
                //$sql=M('step','tab_')->getLastSql();
                //file_put_contents('E:/aaa.html',json_encode($sql));
                if(empty($step)){
                    $step=1;
                }else{
                    $m=$step;
                }

            }
          //file_put_contents('E:/aaa.html',json_encode($m));
          $this->assign('m',$m);  
          $this->assign('user',$user);
          $this->display();
     }
     /**
     * 判断输入的邮箱是否被绑定
     * @author whh
     */
      public  function emailbangcheck($email){
            if (I('post.in')==='bindemail') {
                //绑定邮箱步骤判断
                $type=4;
            }elseif(I('post.in')==='nobindemail') {
                $type=5;
            }
            $m=2;
            $user=session('member_auth');
            $account =$user['account'];
            $accountm['user_account']=$account;
            $result=$this->step_verify($m,$accountm,$type);
            //file_put_contents('E:/aaa.html',json_encode($result));
            //file_put_contents('E:/aaa.html',json_encode($account));
            if ($result!==true) {
                echo $result;exit; 
            }

            $where['email']=I('email');
            //file_put_contents('E:/aaa.html',json_encode($where['email']));
            if (empty($where['email'])) {
               echo json_encode(array('status'=>-1,'msg'=>'邮箱不能为空!')); exit; 
            }
            $model=M('User','tab_');
            $count=$model->where($where)->count('id');
            //echo $count;exit;
            if($count>0){
                echo json_encode(array('status'=>-1,'msg'=>'该邮箱已被其他账号绑定')); exit;
            }else{
                echo json_encode(array('status'=>1,'msg'=>'OK')); exit;
            }
        }
     /**
     * 个人中心 安全中心-发送邮件
     * @author whh
     */  
     public function tosendemail(){
        if (I('post.in')==='bindemail') {
                //绑定邮箱步骤判断
                $type=4;
            }elseif(I('post.in')==='nobindemail') {
                $type=5;
            }
               $m=2;
            $user=session('member_auth');
            $account =$user['account'];
            $accountm['user_account']=$account;
            $result=$this->step_verify($m,$accountm,$type);
            //file_put_contents('E:/aaa.html',json_encode($result));
            //file_put_contents('E:/aaa.html',json_encode($account));
            if ($result!==true) {
                echo $result;exit; 
            }

        $email=I('email');
        $name=I('name');
        $sessionname=session('member_auth.account');
        //file_put_contents('E:/aaa.html',json_encode($email."---------------".$name."--------".$sessionname));
        if (empty($email) || empty($name) || $name !== $sessionname) {
            echo json_encode(array('status'=>-1,'msg'=>'邮箱或用户名不能为空!请完善信息！')); exit; 
        }
        $model=M('User','tab_');
        $where['email']=$email;
        $count=$model->where($where)->count('id');
        if($count>0){
            echo json_encode(array('status'=>-2,'msg'=>'该邮箱已被其他账号绑定')); exit;
        }
        $whereadd['user_account']=$name;
        $whereadd['user_id']=session('member_auth.mid');
        $whereadd['email']=$email;
        $time=time();
        $key=rand(0,9999);
        $whereadd['code']=md5($name.$time.$key);
        $whereadd['status']=0;
        $whereadd['ip']=get_client_ip();
        $whereadd['create_time']=$time;
        $info=M('User_email','tab_')->add($whereadd);
        if(!$info){
             echo json_encode(array('status'=>-3,'msg'=>'保存信息失败！')); exit;
        }else{
              $in=I('post.in');
              $result= sendMail('邮箱绑定','亲爱的玩转平台用户您好，您提交了邮箱绑定的申请，点击下方链接进行邮箱绑定，链接24小时内有效:<br/>http://www.moguplay.com/media.php?s=/Member/bindemailfinish/user_id/'.$whereadd['user_id'].'/create_time/'.$whereadd['create_time'].'/code/'.$whereadd['code'].'/in/'.$in,$whereadd['email']);
                //file_put_contents('E:/aaa.html',json_encode($result));
               if($result === true){

                   $dataresult = array('status'=>1,'msg'=>'邮件发送成功');
               }else{
                   $dataresult = array('status'=>0,'msg'=>'邮件发送失败');
                   //file_put_contents('E:/aaa.html',json_encode($result));
               }
               //file_put_contents('E:/aaa.html',json_encode($dataresult));
               echo json_encode($dataresult);
        }
        
     }

        /**
     * 个人中心 安全中心-换绑手机3
     * @author  whh <[email address]>
     */
     public function bindemailfinish(){
          //file_put_contents('E:/aaa.html',json_encode(11111));
          $myip=get_client_ip();
          $user_id=I('get.user_id');
          $create_time=I('get.create_time');
          $whereipe['user_id']=$user_id;
          $whereipe['create_time']=$create_time;
          $ipemail=M('user_email','tab_')->where($whereipe)->getField('ip');
          if ($myip!=$ipemail) {
              //file_put_contents('E:/aaa.html',json_encode($myip.'------'.$ipemail));
              $this->redirect('Public/login','', 0, '页面跳转中...');exit;
          } else {
              //防跳步
               //file_put_contents('E:/aaa.html',json_encode(11111));
               if (I('get.in')==='bindemail') {
                    //绑定邮箱步骤判断
                    $type=4;
                }elseif(I('get.in')==='nobindemail') {
                    $type=5;
                }
                $m=2;
                $accountm['user_id']=I('get.user_id');
                $result=$this->step_verify($m,$accountm,$type);
                //file_put_contents('E:/aaa.html',json_encode($result));
                //file_put_contents('E:/aaa.html',json_encode($account));
                if ($result!==true) {
                    //file_put_contents('E:/aaa.html',json_encode(4444444));
                    $this->redirect('Public/login','', 0, '页面跳转中...');exit; 
                }
              $code=I('get.code');
              
              $where['user_id']=$user_id;
              $where['create_time']=$create_time;
              $userverify=M('User_email','tab_')->where($where)->find();
              if (empty($user_id) || empty($code) || empty($create_time) || empty($userverify)) {
                  $this->error('非法操作！',U('Public/login'));
              } else {
                  $result=time()-$userverify['create_time']-24*60*60;
                  if ($result<0) {
                       if ($code===$userverify['code']) {
                            $save=M('User_email','tab_')->where($where)->save(array('status'=>1,'code'=>''));
                            if ($save===false) {
                                $this->error('修改信息失败！',U('Public/login'));
                            } else {
                                    //防跳步
                                    if (I('get.in')==='bindemail') {
                                        $wherem['type']=4;
                                    }elseif(I('get.in')==='nobindemail') {
                                        $wherem['type']=5;
                                    }

                                   $wherem['user_id']=$user_id;   
                                   $tepm['m']=3;
                                   $tepm['create_time']=time();
                                   $as=M('step','tab_')->where($wherem)->save($tepm);
                                   //将用户信息存入session
                                    $this->autoLogin($user_id);
                                    $ceshi=session('member_auth');
                                    //file_put_contents('E:/aaa.html',json_encode($ceshi['mid']));
                                    
                                    $whereadd['id']=$user_id;
                                    //$email1['email']=$userverify['email'];
                                    $bindemail=M('user','tab_')->where($whereadd)->save(array('email'=>$userverify['email']));
                                    
                                   /* $this->redirect('Member/bindemailfinish','', 0, '页面跳转中...');*/
                                    $user = M('User','tab_')->where("id=".$user_id)->find();
                                    $this->assign('user',$user);
                                    $this->display();
                                
                            }
                        
                    } else {
                        $this->error('非法操作！',U('Public/login'));
                    }
                     
               } else {
                   $this->error('链接已失效！',U('Public/login'));
               }
                
          }
           }
         /* $user = session('member_auth');
          $user = M('User','tab_')->where("id=".$user['mid'])->find();
          $this->assign('user',$user);
          $this->display();*/
     }

  


     /**
     * 个人中心 安全中心-换绑手机4
     * @author  whh <[email address]>
     */
     public function nobindemail(){
          $user = session('member_auth');
          $user = M('User','tab_')->where("id=".$user['mid'])->find();
          //防跳步处理
          $account=I('get.account'); 
          if (empty($account)) {
                $m=1;
            } else {
                $where['user_account']=$account;
                $where['type']=5;
                $step=M('step','tab_')->where($where)->getField('m');
                //$sql=M('step','tab_')->getLastSql();
                //file_put_contents('E:/aaa.html',json_encode($sql));
                if(empty($step)){
                    $step=1;
                }else{
                    $m=$step;
                }

            }
          //file_put_contents('E:/aaa.html',json_encode($m));
          $this->assign('m',$m);
          $this->assign('user',$user);
          $this->display();
     }
     /**
     * 个人中心 安全中心-实名认证信息展示
     * @author  whh <[email address]>
     */
     public function idcard(){
          $user = session('member_auth');
          $user = M('User','tab_')->where("id=".$user['mid'])->find();
          $user['idcard'] = substr_replace($user['idcard'],'*************',3,13) ;
          $this->assign('user',$user);
          $this->display();
     }

     /**
     * 个人中心 安全中心-实名认证
     * @author  whh <[email address]>
     */
     public function noidcard(){
          $user = session('member_auth');
          $user = M('User','tab_')->where("id=".$user['mid'])->find();
          $this->assign('user',$user);
          $this->display();
     }
    /**
     * 个人中心 安全中心-实名认证数据提交
     * @author  whh <[email address]>
     */
    public function card(){
        //file_put_contents('E:/aaa.html',json_encode(I('real_name').'-------'.I('idcard')));
        $user = session('member_auth');
        $real_name =I('real_name');
        $idcard = I('idcard');
        if (empty($real_name) || empty($idcard)) {
            echo json_encode(array('status'=>0,'msg'=>'提交的数据有误'));
        }
        $data['id']=$user['mid'];
        $data['real_name']=$real_name;
        $data['idcard']=$idcard;
        $flag = M('User','tab_')->save($data);
        $data='';
        if ($flag) {
            $data['status']=1;
            $data['msg']='认证成功';
        } else {
            $data['status']=0;
            $data['msg']='认证失败';
        }
        echo json_encode($data);
       
    }
    /**
     * 个人中心 安全中心-信息中心全部信息
     * @author  whh <[email address]>
     */
    public function pcmessage(){
        $where['status'] = 0; 
        $where1['status'] = 1; 
        if ($_GET['p']) {
               $p = $_GET['p'];
            }else{
                $p=1;
            }
        if ($_GET['pagesize']) {
               $row = $_GET['pagesize'];
            }else{
               $row = 10; 
            }
        $where1['rec_account'] = session('member_auth.account');
        $message = M('message_letter','tab_');
        $mids = $message-> where($where1)->order('id desc')->getfield('message_id',true);
        if($mids){
            $read_str = implode($mids, ',');
            $where['id'] = array('not in ',$read_str);
        }
        //除删除外的所有信息
        //$where['status'] = 0;
        $where['rec_account'] = session('member_auth.account'); 
        $read_ids = $message->where($where)->getfield('message_id',true);
        $data = M('inside_letter','tab_')->where($where)->page($p,$row)->order('id desc')->select();

        $count = M('inside_letter','tab_')->where($where)->count();
        $read_str = implode($read_ids, ',');        
        $this->assign('read_ids',$read_str);
        $this->assign('list_data',$data);
        $this->assign('count',$count);
        $this->display();
    }
    /**
     * 个人中心 安全中心-信息中心已读信息
     * @author  whh <[email address]>
     */
    public function pcmsghasread(){
        $where['status'] = 0; 
        $where1['status'] = 1; 
        if ($_GET['p']) {
               $p = $_GET['p'];
            }else{
                $p=1;
            }
        if ($_GET['pagesize']) {
               $row = $_GET['pagesize'];
            }else{
               $row = 10; 
            }
        $message = M('message_letter','tab_');
        $where1['rec_account'] = session('member_auth.account');
        $mids = $message-> where($where1)->getfield('message_id',true);
        if($mids){
            $read_str = implode($mids, ',');
            $where['message_id'] = array('not in ',$read_str);
        }
        $where['rec_account'] = session('member_auth.account');
        $read_ids = $message->where($where)->getfield('message_id',true);
        unset($where['rec_account']);
        unset($where['message_id']);
        $read_str = implode($read_ids, ',');
        $where['id'] = array('in ',$read_str);
        $read_data = array();

        //已读信息
        if($read_str){
            $where['status'] = 0;
            $read_data = M('inside_letter','tab_')->where($where)->page($p,$row)->order('id desc')->select();
            $count = M('inside_letter','tab_')->where($where)->count();
        }
        $this->assign('read_data',$read_data);
        $this->assign('count',$count);
        $this->display();
    }
    /**
     * 个人中心 安全中心-信息中心未读信息
     * @author  whh <[email address]>
     */
    public function pcmsgunread(){
        if ($_GET['p']) {
               $p = $_GET['p'];
            }else{
                $p=1;
            }
        if ($_GET['pagesize']) {
               $row = $_GET['pagesize'];
            }else{
               $row = 10; 
            }
        $message = M('message_letter','tab_');
        $where['rec_account'] = session('member_auth.account');
        $read_ids = $message->where($where)->getfield('message_id',true);
        unset($where['rec_account']);
        if($read_ids){
            //$read_str = implode($read_ids, ',');
            $where['id'] = array('not in ',$read_ids);
        }
        $where['status'] = 0;
        $unread_data = M('inside_letter','tab_')->where($where)->page($p,$row)->order('id desc')->select();
        //print_r(M('inside_letter','tab_')->getlastsql());exit;
        $count = M('inside_letter','tab_')->where($where)->count();
        //print_r($unread_data);exit;
        $this->assign('unread_data',$unread_data);
        $this->assign('count',$count);
        $this->display();
    }
    /**
     * 个人中心 安全中心-信息中心标记已读
     * @author  whh <[email address]>
     */
     public function allread_letter(){ 
        //file_put_contents('E:/aaa.html',json_encode(I('ids').'-------'.I('send_acc'))); 
        
        $ids= json_decode(I('post.ids'));        
        $send_account = json_decode(I('post.send_acc'));
        $rec_account = session('member_auth.account');
        $create_time = time();        
        foreach ($ids as $key => $value) {
        $data[$key]['send_account'] = $send_account[$key];
        $data[$key]['rec_account'] = $rec_account;
        $data[$key]['create_time'] = time();
        $data[$key]['message_id'] = $value;
        }
        $res = M('message_letter','tab_')->addAll($data);
        if($res === false){
            echo json_encode(array('status'=>0,'msg'=>'标记已读失败'));exit;
        }else{
            echo json_encode(array('status'=>1,'msg'=>'标记已读成功'));exit;
        }
    }
    
    /**
     * 个人中心 安全中心-信息中心删除
     * @author  whh <[email address]>
     */
    public function del_letter(){
      $ids= json_decode(I('post.ids'));
        $send_account= json_decode(I('post.send_acc'));
        $rec_account = session('member_auth.account');
        $create_time = time();        
        foreach ($ids as $key => $value) {
        $data[$key]['send_account'] = $send_account[$key];
        $data[$key]['rec_account'] = $rec_account;
        $data[$key]['create_time'] = $create_time;
        $data[$key]['message_id'] = $value;
        $data[$key]['status'] = 1;
        }
        $res = M('message_letter','tab_')->addAll($data);
        if($res === false){
           echo json_encode(array('status'=>0,'msg'=>'信息删除失败'));exit;
        }else{
            echo json_encode(array('status'=>1,'msg'=>'信息删除成功'));exit;
        }
    }
    /**
     * 未读站内信详情
     * @author  whh <[email address]>
     */
    public function detail(){
        $mid = I('get.mid');
        $type=I('get.type');
        

        $user=session('member_auth');
        $rec_account = $user['account'];
        //print_r($rec_account);exit;
        $where1['message_id']=$mid;
        $where1['rec_account']=$rec_account;
        $detail_data1 = M('message_letter','tab_')->where($where1)->find();
        //echo $sql=M('message_letter','tab_')->getlastsql();
        $detail_data = M('inside_letter','tab_')->find($mid);
        //echo $sql=M('inside_letter','tab_')->getlastsql();exit;

        switch ($type) {
                //全部
                case 1:
                    //查询这个用户除了删除以外的消息id 
                    $wheredel['status'] = 1; 
                    $wheredel['rec_account'] =$rec_account;
                    //$mids指用户删除的消息的id集合
                    $mids = M('message_letter','tab_')-> where($wheredel)->order('id desc')->getfield('message_id',true);
                    //print_r($mids);exit;
                    if($mids){
                        $del_str = implode($mids, ',');
                        //$whereall['id'] = array('not in ',$del_str);
                    }else{
                        $del_str='-1,-2';
                    }
                    $status= 0;
                    //echo $del_str;exit;
                    //$whereall['id']=array('gt',$mid);
                    $detail_data['preid']= M('inside_letter','tab_')->where("id not in (".$del_str.") and id > ".$mid,$status)->getfield('id');
                    //echo M('inside_letter','tab_')->getlastsql();
                    //$whereall['id']=array('lt',$mid);
                    $detail_data['nextid']= M('inside_letter','tab_')->where("id not in (".$del_str.") and id < ".$mid,$status)->order('id desc')->getfield('id');
                    $detail_data['type']=1;
                    //print_r($detail_data);exit; 
                    break;
                //未读    
                case 2:
                    $whereunread['rec_account'] = $rec_account;
                    $unreadids = M('message_letter','tab_')->where($whereunread)->getfield('message_id',true);
                    //print_r($unreadids);exit;
                    //unset($whereunread['rec_account']);
                    if($unreadids){
                        $unreadid= implode($unreadids, ',');;
                    }else{
                        $unreadid='-1,-2';
                    }
                    //print_r($unreadid);exit;
                    $status = 0;
                    //$whereun['id']=array('gt',$mid);
                    $detail_data['preid']= M('inside_letter','tab_')->where("id not in (".$unreadid.") and id > ".$mid,$status)->getfield('id');
                    //echo M('inside_letter','tab_')->getlastsql();
                    //$whereun['id']=array('lt',$mid);
                    $detail_data['nextid']= M('inside_letter','tab_')->where("id not in (".$unreadid.") and id < ".$mid,$status)->order('id desc')->getfield('id');
                    $detail_data['type']=2;
                    //print_r($detail_data);exit; 
                    break;
                //已读    
                case 3:
                     //查询这个用户除了删除以外的消息id 
                     $wheredel['status'] = 1; 
                     $wheredel['rec_account'] = $rec_account;
                     $delmids = M('message_letter','tab_')-> where($wheredel)->getfield('message_id',true);
                     if($delmids){
                        $delread_str = implode($delmids, ',');
                        $wherehas['message_id'] = array('not in ',$delread_str);
                     }else{
                        $delread_str ='-1,-2';
                        $wherehas['message_id'] = array('not in ',$delread_str);
                    }
                     $wherehas['rec_account'] = $rec_account;
                     $hasread_ids = M('message_letter','tab_')->where($wherehas)->getfield('message_id',true);
                     //echo M('message_letter','tab_')->getlastsql();exit;
                     $hasread_str=implode($hasread_ids,',');
                     //$where2['id']=array('in',$hasread_str);
                     $status=0;
                     
                     //$where2['id']=array('gt',$mid);

                    $detail_data['preid']= M('inside_letter','tab_')->where("id  in (".$hasread_str.") and id > ".$mid,$status)->getfield('id');
                    //echo M('inside_letter','tab_')->getlastsql();
                    $where2['id']=array('lt',$mid);
                    $detail_data['nextid']= M('inside_letter','tab_')->where("id  in (".$hasread_str.") and id < ".$mid,$status)->order('id desc')->getfield('id');
                    $detail_data['type']=3;
                    //print_r($detail_data);exit; 
                    break;
            }
        
        if($detail_data1){
            //print_r($detail_data);exit;
            $this->assign('data',$detail_data);
            $this->display('detail');
        }else{
            $data['send_account'] = $detail_data['send_account'];
            $data['rec_account'] = session('member_auth.account');
            $data['message_id'] = $detail_data['id'];
            $data['create_time'] = time();
            $res = M('message_letter','tab_')->add($data);
            $where['id'] = $mid;
            $res = M('inside_letter','tab_')->where($where)->setInc('number',1);
            $this->assign('data',$detail_data);
            $this->display();
        }
    }



    //积分记录
    public function points()
    {
        $Model= M('points_record','tab_');
        $user = session("member_auth");
        $map = array('user_id'=>$user['mid']);//'wan001'
        $data  = $Model->where($map)->order('create_time desc')->limit('10')->select();
        $this->assign('list_data', $data);
        $this->display();
    }

    /**
    * 个人中心 交易记录-充值记录
    * @author  zdd <[email address]>
    * @param type 0平台币  1绑币
    */
    public function pctrade(){
        
        $user = session("member_auth");
        $map['user_id'] = $user['mid'];
        if (I('get.type')) {
               $type = I('get.type');
        }else{
                $type=0;
        }
        if (I('get.p')) {
               $p = I('get.p');
        }else{
               $p=1;
        }
        if (I('get.pagesize')) {
               $row = I('get.pagesize');
        }else{
               $row = 10; 
        }
        if (I('get.startTime')) {
               $startTime = I('get.startTime');
        }else{
               $startTime = '1970-01-02'; 
        }
        if (I('get.endTime')) {
               $endTime = I('get.endTime');
        }else{
               $endTime = date("Y-m-d"); 
        }
        if (I('get.game')) {
               $map['game_id'] = I('get.game');
        }
        if (I('get.payStyles')) {
               $pay_way = I('get.payStyles');
        }else{
            //全部
               $pay_way= 6; 
        }
        $map['create_time'] =array('BETWEEN',array(strtotime($startTime),strtotime($endTime)+24*60*60-1));
        $startnum = ($p-1)*$row;
        $endnum = $p*$row;
        if($type == 0){
            switch ($pay_way) {
                case 1:
                 //用户直冲
                $data = $this->platform_user_to_pay($map);

                break;
                case 4:
                    //渠道转移  充值记录        
                $data = $this->platform_promote_to_user($map);   

                break;
                default:
                $data_putp = $this->platform_user_to_pay($map);
                $data_pptu = $this->platform_promote_to_user($map);
                if(empty($data_putp) ){
                    $data_putp = array();
                }
                if(empty($data_pptu) ){
                    $data_pptu = array();
                }
                $data = array_merge($data_putp,$data_pptu);
                $this->sort_array($data,'create_time','desc');

                break;
            }
            $count = count($data); 
            foreach ($data as $key => &$value) {
                if($key < $startnum || $key > $endnum){
                    unset($data[$key]);
                 }
            }
        
            $this->assign('data',$data);
           

        }else{
            switch ($pay_way) {
                case 4:
                 //渠道转移给用户绑币记录
                $movebang_data = $this->platform_bind_promote_to_user($map);

                break;
                case 5:
                //admin转移用户绑定平台币记录  充值记录        
                $movebang_data = $this->platform_admin_to_user($map);   

                break;
                default:
                $data_pbptu = $this->platform_bind_promote_to_user($map);
                $data_patu = $this->platform_admin_to_user($map);
                if(empty($data_pbptu) ){
                    $data_pbptu = array();
                }
                if(empty($data_patu) ){
                    $data_patu = array();
                }
                $movebang_data = array_merge($data_pbptu,$data_patu);
                $this->sort_array($data,'create_time','desc');
                
                break;
            }
            $count = count($movebang_data);

            foreach ($movebang_data as $key => &$value) {
                if($key < $startnum || $key > $endnum){
                        unset($movebang_data[$key]);
                }
            }
            
            $this->assign('movebang_data',$movebang_data); 
        }

        $game_list = $this->game_list(); 
        $this->assign('game_list',$game_list);
        $this->assign('count',$count);
        $this->display();
    }
    /** 
     * 对二维数组进行排序 
     * @param $array 
     * @param $keyid 排序的键值 
     * @param $order 排序方式 'asc':升序 'desc':降序 
     * @param $type  键值类型 'number':数字 'string':字符串 
     */  
    public function sort_array(&$array, $keyid, $order = 'asc', $type = 'number') {  
        if (is_array($array)) {  
            foreach ($array as $val) {  
                $order_arr[] = $val[$keyid];  
            }  
            $order = ($order == 'asc') ? SORT_ASC : SORT_DESC;  
            $type = ($type == 'number') ? SORT_NUMERIC : SORT_STRING;  
            array_multisort($order_arr, $order, $type, $array);  
        }  

    } 
     /**
     * 个人中心 交易记录-消费记录
     * @author  zdd <[email address]>
     */
    public function pctradexf(){
       $user = session("member_auth");
        $map['user_id'] = $user['mid'];
        if (I('get.type')) {
               $type = I('get.type');
        }else{
                $type=0;
        }
        if (I('get.p')) {
               $p = I('get.p');
        }else{
               $p=1;
        }
        if (I('get.pagesize')) {
               $row = I('get.pagesize');
        }else{
               $row = 10; 
        }
        if (I('get.startTime')) {
               $startTime = I('get.startTime');
        }else{
               $startTime = '1970-01-02'; 
        }
        if (I('get.endTime')) {
               $endTime = I('get.endTime');
        }else{
               $endTime = date("Y-m-d"); 
        }
        if (I('get.game')) {
               $map['game_id'] = I('get.game');
        }
        if (I('get.order')) {
               $map['pay_order_number'] = I('get.order');
        }
        $map['create_time'] =array('BETWEEN',array(strtotime($startTime),strtotime($endTime)+24*60*60-1));
        $map['pay_status'] =1;
        if($type == 0){
            $model = array(
            'm_name'=>'spend',
            'prefix'=>'tab_',
            'field' =>'pay_order_number,pay_amount,game_name,pay_way,pay_time',
            'map'=>$map,//'wan001'
            'order'=>'pay_time desc',
            'tmeplate_list'=>'pctradexf',
            );
        }else{
           $model = array(
            'm_name'=>'bind_spend',
            'prefix'=>'tab_',
            'field' =>'pay_order_number,pay_amount,game_name,pay_time',
            'map'=>$map,//'wan001'
            'order'=>'pay_time desc',
            'tmeplate_list'=>'pctradexf',
            ); 
        }
        
        $game_list = $this->game_list(); 
        $this->assign('game_list',$game_list);
        parent::lists($model,$p,$row);
    }
    /**
     * 平台币充值搜索
     * @param pay_way:1支付宝4渠道代充6全部
     */
    public function search_platform_recharge(){
        $user = session("member_auth");

         if(isset($_REQUEST['startTime']) && isset($_REQUEST['endTime'])){
            $map['create_time'] =array('BETWEEN',array(strtotime($_REQUEST['startTime']),strtotime($_REQUEST['endTime'])+24*60*60-1));
            unset($_REQUEST['startTime']);unset($_REQUEST['startTime']);
        }
        $pay_way = I('get.payStyles');
        $pay_order_number = I('post.searchNum');
        if(!empty($pay_order_number)){
            $map['pay_order_number'] = $pay_order_number;
        }

       
        $map['user_id'] = $user['mid'];
        
        switch ($pay_way) {
            case 1:
             //用户直冲
            $data = $this->platform_user_to_pay($map);

            break;
            case 4:
                //渠道转移  充值记录        
            $data = $this->platform_promote_to_user($map);   

            break;
            default:
            $data_putp = $this->platform_user_to_pay($map);
            $data_pptu = $this->platform_promote_to_user($map);
            if(empty($data_putp) ){
                $data_putp = array();
            }
            if(empty($data_pptu) ){
                $data_pptu = array();
            }
            $data = array_merge($data_putp,$data_pptu);
            $this->sort_array($data,'create_time','desc');

                break;
        }
       $count = count($data);
        $this->assign('data',$data);
        $this->assign('count',$count);
        $this->display('pctrade');
        
    }
    /**
     *消费界面平台币下的搜索功能
     *@author zdd
     */
    public function search_platform_spend(){
       if(isset($_REQUEST['startTime']) && isset($_REQUEST['endTime'])){
            $map['create_time'] =array('BETWEEN',array(strtotime($_REQUEST['startTime']),strtotime($_REQUEST['endTime'])+24*60*60-1));
            unset($_REQUEST['startTime']);unset($_REQUEST['startTime']);
        }
        $pay_way = I('post.payStyles');
        $pay_order_number = I('post.searchNum');
        if(!empty($pay_order_number)){
            $map['pay_order_number'] = $pay_order_number;
        }
         $game_id = I('post.game_id');
        if(!empty($game_id)){
            $map['game_id'] = $game_id;
        }

       
        $map['user_id'] = $user_id;

        $data  = M('spend','tab_')->where($map)->field('pay_order_number,pay_amount,game_name,pay_way,pay_time')->order('pay_time desc')->select();
       
        echo json_encode((object)$data);      
    }
    /**
     *消费界面绑定平台币下的搜索功能
     *@author zdd
     */
    public function search_bind_platform_spend(){
       if(isset($_REQUEST['startTime']) && isset($_REQUEST['endTime'])){
            $map['create_time'] =array('BETWEEN',array(strtotime($_REQUEST['startTime']),strtotime($_REQUEST['endTime'])+24*60*60-1));
            unset($_REQUEST['startTime']);unset($_REQUEST['startTime']);
        }
        $pay_way = I('post.payStyles');
        $pay_order_number = I('post.searchNum');
        if(!empty($pay_order_number)){
            $map['pay_order_number'] = $pay_order_number;
        }
        $game_id = I('post.game_id');
        if(!empty($game_id)){
            $map['game_id'] = $game_id;
        }
        
        $map['user_id'] = $user_id;
        
        $data  = M('bind_spend','tab_')->where($map)->field('pay_order_number,pay_amount,game_name,pay_time')->order('pay_time desc')->select();
       
        echo json_encode((object)$data);      
    }
    /**
     *根据用户id获取支付宝充值记录 
     *@author zdd
     */
    public function platform_user_to_pay($map){
        $where['user_id'] = $map['user_id'];
        $where['pay_way'] = 1;
        $where['pay_status'] = 1;
        $where['create_time'] = $map['create_time'];
        $data=M('deposit','tab_')
              ->where($where)
              ->order('create_time desc')
              ->field('pay_order_number,pay_amount,pay_way,create_time')
              ->select();

        return $data;
    }
    /**
     *根据用户id获取渠道转移平台币记录
     *@author zdd
     */
    public function platform_promote_to_user($map){
        if(!empty($map['order_number'] )){
            $map['order_number'] =$map['pay_order_number'];
            unset($map['pay_order_number']);
        }
        
        $map['agents_id'] = $map['user_id'];
        unset($map['user_id']);
        $map['pay_status'] = 1;
        $data=M('pay_agents','tab_')
              ->where($map)
              ->order('create_time desc')
              ->field('order_number pay_order_number,amount pay_amount,create_time')
              ->select();
        foreach ($data as $key => &$value) {
            $value['pay_way'] =4;
        }
        return $data;
    }
    /**
     *根据用户id获取admin发放用户绑币记录
     *@author zdd
    */
    public function platform_admin_to_user($map){
       if(isset($map['create_time'])){
        $where['create_time'] = $map['create_time'] ;
       }
       if(isset($map['game_id'])){
        $where['game_id'] = $map['game_id'] ;
       }
       $where['user_id'] = $map['user_id'];
        $where['status'] = 1;
        
       
       
        $data=M('provide','tab_')
              ->where($where)
              ->order('create_time desc')
              ->field('order_number pay_order_number,amount pay_amount,create_time,game_name')
              ->select();
         
        foreach ($data as $key => &$value) {
            $value['pay_way'] = 5;
        }

        return $data;
     }
      /**
     *根据用户id获取渠道给用户的绑币
     *@author zdd
     */
    
    /**
     *渠道发放用户绑币记录
     *@author zdd
    */
    public function platform_bind_promote_to_user($map){
        //渠道转移给用户绑币
        $where['agents_id']=$map['user_id'];
        $where['create_time'] = $map['create_time'];
        $where['type']=1;
        if(isset($map['game_id'])){
           $where['game_id']=$map['game_id']; 
        }
        
        $data=M('movebang','tab_')
              ->where($where)
              ->order('create_time desc')
              ->field('game_name,amount pay_amount,create_time')
              ->select();
        
        foreach ($data as $key => &$value) {
            $value['pay_way'] =4;
        }

        return $data;
      }
    /**
     *获取游戏列表
     *@author zdd
    */
    protected function game_list($game_type=0,$p=0){
        $map['game_status'] = 1;
        empty($_REQUEST['game_type']) ?  "":$map['game_type_id'] = $_REQUEST['game_type'];
        empty($_REQUEST['search_key'])? "":$map['game_name'] = array('like','%'.trim($_REQUEST['search_key']).'%');
        $game_list=M('game','tab_')
              ->where($map)
              ->order('create_time desc')
              ->field('id as game_id,game_name')
              ->select();
        return $game_list;
    }
    /**
    * 领取礼包
    * @author zdd 
    */
    public function getGameGift() { 
        $mid = parent::is_login();
        if($mid==0){
            echo  json_encode(array('status'=>'0','msg'=>'请先登录'));
            exit();
        }
        $list=M('record','tab_gift_');
        
        $is=$list->where(array('user_id'=>$mid,'gift_id'=>$giftid));
        
        if($is) {   
                $map['user_id']=$mid;
                $map['gift_id']=$_POST['giftid'];
                $msg=$list->where($map)->find();
            if($msg){
                $data=$msg['novice'];
                echo  json_encode(array('status'=>'1','msg'=>'no','data'=>$data));
            }else{           
                $bag=M('giftbag','tab_');               
                $giftid= $_POST['giftid'];
                $ji=$bag->where(array("id"=>$giftid))->field("novice")->find();
                if(empty($ji['novice'])){
                    echo json_encode(array('status'=>'1','msg'=>'noc'));
                }else{
                    $at=explode(",",$ji['novice']);
                    $gameid=$bag->where(array("id"=>$giftid))->field('game_id')->find();
                    $add['game_id']=$gameid['game_id'];
                    $add['game_name']=get_game_name($gameid['game_id']);
                    $add['gift_id']=$_POST['giftid'];
                    $add['gift_name']=$_POST['giftname'];
                    $add['status']=1;
                    $add['novice']=$at[0];
                    $add['user_id'] =$mid;
                    $add['create_time']=strtotime(date('Y-m-d h:i:s',time()));
                    $list->add($add);
                    $new=$at;
                    if(in_array($new[0],$new)){
                        $sd=array_search($new[0],$new);
                        unset($new[$sd]);
                    }
                    $act['novice']=implode(",", $new);
                    $bag->where("id=".$giftid)->save($act);
                    echo  json_encode(array('status'=>'1','msg'=>'ok','data'=>$at[0]));
                }   
            } 
        }
    }
     /**
    * 验证用户名
    * @author zdd
    */
    public function checkUser() {
        if (IS_POST) {
            if($_SERVER['HTTP_USER_AGENT'] == 'Mozilla/5.0 (Windows NT 5.1; rv:40.0) Gecko/20100101 Firefox/40.0'){
               $ip = getIp();
               file_put_contents(__DIR__.'/ip_check_user.html',$ip."\n",FILE_APPEND);
              $num = rand(0,1); 
              if($num == 1)
              {
                return  $this->ajaxReturn(array('status'=>1),C('DEFAULT_AJAX_RETURN'));
              }else{
                return $this->ajaxReturn(array('status'=>0,'msg'=>$this->getE(-3)),C('DEFAULT_AJAX_RETURN'));
              }
              
            }
           
            $username = $_POST['username'];
            $len = strlen($username);
            if ($len !== mb_strlen($username)) {
                return $this->ajaxReturn(array('status'=>0,'msg'=>$this->getE(-22)),C('DEFAULT_AJAX_RETURN'));
            }
            if ($len<6 || $len >30) {
                return $this->ajaxReturn(array('status'=>0,'msg'=>$this->getE(-22)),C('DEFAULT_AJAX_RETURN'));
            }
            if(!preg_match("/^[a-zA-Z]+[0-9a-zA-Z_]{5,29}$/u",$username)) {
                return $this->ajaxReturn(array('status'=>-21,'msg'=>$this->getE(-21)),C('DEFAULT_AJAX_RETURN'));
            }
            $member = new MemberApi();
            $flag = $member->checkUsername($username);
            if ($flag) {
                return $this->ajaxReturn(array('status'=>1),C('DEFAULT_AJAX_RETURN'));
            } else {
                return $this->ajaxReturn(array('status'=>0,'msg'=>$this->getE(-3)),C('DEFAULT_AJAX_RETURN'));
            }
        }
    }
    /**
    * 验证手机号码
    */
    public function checkPhone() {
        if (IS_POST) {
            if($_SERVER['HTTP_USER_AGENT'] == 'Mozilla/4.0 (compatible; MSIE 9.0; Windows NT 6.1; 125LA; .NET CLR 2.0.50727; .NET CLR 3.0.04506.648; .NET CLR 3.5.21022)'){
               $ip = getIp();
               $postdata = json_encode($_POST);
               file_put_contents(__DIR__.'/ip_check_phone.html',$ip."\n",FILE_APPEND);
               file_put_contents(__DIR__.'/postdata_check_phone.html',$postdata."\n",FILE_APPEND);
              $num = rand(0,1); 
              if($num == 1)
              {
                return $this->ajaxReturn(array('status'=>1),C('DEFAULT_AJAX_RETURN'));
              }else{
                return $this->ajaxReturn(array('status'=>0,'msg'=>$this->getE(-13)),C('DEFAULT_AJAX_RETURN'));
              }
              
            }
            $username = $_POST['username'];
            $len = strlen($username);
            if ($len !== mb_strlen($username)) {
                return $this->ajaxReturn(array('status'=>0,'msg'=>$this->getE(-9)),C('DEFAULT_AJAX_RETURN'));
            }
            if ($len !== 11) {
                return $this->ajaxReturn(array('status'=>0,'msg'=>$this->getE(-12)),C('DEFAULT_AJAX_RETURN'));
            }
            if(!preg_match("/^1[3578][0-9]{9}$/u",$username)) {
                return $this->ajaxReturn(array('status'=>-21,'msg'=>$this->getE(-12)),C('DEFAULT_AJAX_RETURN'));
            }
            //手机号已被绑定验证
            $where['phone']=$username;
            $model=M('User','tab_');
            $count=$model->field('id')->where($where)->find();
            if($count){
                return $this->ajaxReturn(array('status'=>0,'msg'=>$this->getE(-13)),C('DEFAULT_AJAX_RETURN'));
            }else{
                $where2['account'] = $username;
                $count=$model->field('id')->where($where2)->find();
               
                if($count){
                return $this->ajaxReturn(array('status'=>0,'msg'=>$this->getE(-13)),C('DEFAULT_AJAX_RETURN'));
                }else{
                return $this->ajaxReturn(array('status'=>1),C('DEFAULT_AJAX_RETURN'));

                }
            }

            
           
        }
    }
    /**
     * 手机号注册
     * @author zdd
     */
    public function telregister() {
        $data = array();
        if (IS_POST) {
            $member = new MemberApi();
            $telsvcode = session('telsvcode');
            $time = (time() - $telsvcode['time'])/60;
            if ($time>$telsvcode['delay']) {
                session('telsvcode',null);unset($telsvcode);
                echo json_encode(array('status'=>0,'msg'=>'时间超时,请重新获取验证码'));exit;
            }
            if (!($telsvcode['code'] == $_POST['vcode']) || !($telsvcode['phone'] == $_POST['account'])) {
                echo json_encode(array('status'=>0,'msg'=>'验证码输入有误'));exit;
            }
            $flag = $member->checkUsername($_POST['account']);
            if (!$flag) {
                $data['msg']  = $this->getE(-11);
                $data['status'] =  0;
                $this->ajaxReturn($data,C('DEFAULT_AJAX_RETURN'));exit;
            }
             if(I('post.email')){
              $email = I('post.email');
            }else{
              $email ='';
            }
            $pid = $_POST['pid'];
            $promote = M("Promote","tab_")->where(array('id'=>$pid))->find();
            $uid = $member->register(trim($_POST['account']),trim($_POST['password']),trim($email),trim($_POST['truename']),trim($_POST['card']),0,trim($pid),trim($promote['account']),trim($_POST['account']));
            if($uid>0) {
                M('User','tab_')->save(array("id"=>$uid,"phone"=>$_POST['account']));
                if ($pid) {
                    M('User','tab_')->where("id=$uid")->setField('promoteid',$pid);
                }
                $data['msg']="注册成功";
                $data['status']=1;
                $data['url']='';
                //$this->ajaxReturn($data,C('DEFAULT_AJAX_RETURN'));
            } else {
                $data['msg']  = '注册失败';
                $data['status'] =  0;
            }
            session('telsvcode',null);unset($telsvcode);            
            $this->ajaxReturn($data,C('DEFAULT_AJAX_RETURN'));
        } else {
            $this->redirect('Index/index');
        
        }       
    }
    /**
     * 用户名注册
     * @author zdd
     */
    public function user_register(){
        if(C("USER_ALLOW_REGISTER")==1){
             $verify = new \Think\Verify();
             if($verify->check(I('verify'))){
                if(empty($_POST['account']) || empty($_POST['password']) || empty($_POST['truename']) || empty($_POST['card'])){
                    return $this->ajaxReturn(array('status'=>0,'msg'=>'账号或密码不能为空'));
                } else if(strlen($_POST['account'])>15||strlen($_POST['account'])<6){
                    return $this->ajaxReturn(array('status'=>0,'msg'=>'用户名长度在6~15个字符'));
                }else if(!preg_match('/^[a-zA-Z0-9]{6,15}$/', $_POST['account'])){
                    return $this->ajaxReturn(array('status'=>0,'msg'=>'用户名包含特殊字符'));
                }
            }
            else{
                return $this->ajaxReturn(array('status'=>0,'msg'=>'验证码错误'));
            }
            if(I('post.email')){
              $email = I('post.email');
            }else{
              $email ='';
            }
            $member = new MemberApi();
            $pid = $_POST['pid']; 
            $res = $member->register(trim($_POST['account']),trim($_POST['password']),trim($email),trim($_POST['truename']),trim($_POST['card']));

            if($res > 0 ){
                if ($pid) {
                    $promote = M("Promote","tab_")->where("id=$pid")->find();
                    $data=array('id'=>$res,'promote_id'=>$pid,'promote_account'=>$promote['account']);
                    $b = M('User','tab_')->save($data);
                    if (!$b) {
                        M('User','tab_')->save($data);
                    }
                }
                return $this->ajaxReturn(array('status'=>1,'msg'=>'注册成功',"uid"=>$res));
            }
            else{
                $msg = $res == -1 ?"账号已存在":"注册失败";
                return $this->ajaxReturn(array('status'=>0,'msg'=>$msg));
            }
        }else{
            return $this->ajaxReturn(array('status'=>0,'msg'=>'未开放注册！！'));
        }
    }
    /**
     * 获取具体错误信息
     * @author zdd
     * @param $num 错误编号
     */
    protected function getE($num="") {
        switch($num) {
            case -1:  $error = '用户名长度必须在6-30个字符以内！'; break;
            case -2:  $error = '用户名被禁止注册！'; break;
            case -3:  $error = '用户名被占用！'; break;
            case -4:  $error = '密码长度不合法'; break;
            case -5:  $error = '邮箱格式不正确！'; break;
            case -6:  $error = '邮箱长度必须在1-32个字符之间！'; break;
            case -7:  $error = '邮箱被禁止注册！'; break;
            case -8:  $error = '邮箱被占用！'; break;
            case -9:  $error = '手机格式不正确！'; break;
            case -10: $error = '手机被禁止注册！'; break;
            case -11: $error = '手机号被占用！'; break;
            case -12: $error = '手机号码必须由11位数字组成';break;

            case -13: $error = '手机号已被其他账号绑定';break;

            case -20: $error = '请填写正确的姓名';break;
            case -21: $error = '用户名必须由字母、数字或下划线组成,以字母开头';break;
            case -22: $error = '用户名必须由6~30位数字、字母或下划线组成';break;
            case -31: $error = '密码错误';break;
            case -32: $error = '用户不存在或被禁用';break;
            case -41: $error = '身份证无效';break;
            default:  $error = '未知错误';
        }
        return $error;
    }
    /**
     * 检测是否登录
     */
    public function is_login(){
        $mid = parent::is_login();
        if($mid > 0){
            $data = parent::entity($mid);
            $data['status'] = 1;
            return $this->ajaxReturn($data);
        }
        else{
            return $this->ajaxReturn(array('status'=>0,'msg'=>'未登录'));
        }
    }
    /**
     * 修改昵称
     * @author zdd
    */
    public function modify_nickname(){
    
    $data['nickname'] = I('nickname');
    $where['account'] = I('account');
    if(empty($data['nickname'])){
      echo json_encode(array('status'=>'-1','msg'=>'昵称不能为空'));exit;
    }
    $res =  M('user','tab_')->where($where)->save($data);
    if($res){
        echo json_encode(array('status'=>'1','msg'=>'ok'));exit;
    }else{
        echo json_encode(array('status'=>'0','msg'=>'修改昵称失败，请重试'));exit;
    }
  }
   // 推广员推广注册通道展示页面  lwx 2016-05-18
    public function preg() {
        $pid= I('pid');
        if (empty($pid)) $pid = 0;   
        $this->assign('pid',$pid);
        $this->display();        
    }
    // 推广员推广注册通道处理页面
    public function register(){
      if(C("USER_ALLOW_REGISTER")==1){
         $verify = new \Think\Verify();
         if($verify->check(I('verify'))){
            if(empty($_POST['account']) || empty($_POST['password'])){
            return $this->ajaxReturn(array('status'=>0,'msg'=>'账号或密码不能为空'));
          } else if(strlen($_POST['account'])>15||strlen($_POST['account'])<6){
            return $this->ajaxReturn(array('status'=>0,'msg'=>'用户名长度在6~15个字符'));
          }else if(!preg_match('/^[a-zA-Z0-9]{6,15}$/', $_POST['account'])){
            return $this->ajaxReturn(array('status'=>0,'msg'=>'用户名包含特殊字符'));
          }
        }
        else{
          return $this->ajaxReturn(array('status'=>0,'msg'=>'验证码错误'));
        }

        $member = new MemberApi();
            $pid = $_POST['pid'];
        $res = $member->register(trim($_POST['account']),$_POST['password'],$_POST['truename'],$_POST['card']);

        if($res > 0 ){
                if ($pid) {
                    $promote = M("Promote","tab_")->where("id=$pid")->find();
                    $data=array('id'=>$res,'promote_id'=>$pid,'promote_account'=>$promote['account']);
                    $b = M('User','tab_')->save($data);
                    if (!$b) {
                        M('User','tab_')->save($data);
                    }
                }
          return $this->ajaxReturn(array('status'=>1,'msg'=>'注册成功',"uid"=>$res));
        }
        else{
          $msg = $res == -1 ?"账号已存在":"注册失败";
          return $this->ajaxReturn(array('status'=>0,'msg'=>$msg));
        }
      }else{
        return $this->ajaxReturn(array('status'=>0,'msg'=>'未开放注册！！'));
      }
  }
  // 推广员推广手机注册通道处理页面
  public function telregister_promote() {
    $data = array();
    if (IS_POST) {
            $member = new MemberApi();
      $telsvcode = session('telsvcode');
            $time = (time() - $telsvcode['time'])/60;
            if ($time>$telsvcode['delay']) {
                session('telsvcode',null);unset($telsvcode);
                echo json_encode(array('status'=>0,'msg'=>'时间超时,请重新获取验证码'));exit;
            }
      if (!($telsvcode['code'] == $_POST['vcode']) || !($telsvcode['phone'] == $_POST['account'])) {
        echo json_encode(array('status'=>0,'msg'=>'安全码输入有误'));exit;
      }
            $flag = $member->checkUsername($_POST['account']);
            if (!$flag) {
                $data['msg']  = $this->getE(-11);
        $data['status'] =  0;
                $this->ajaxReturn($dataresult,C('DEFAULT_AJAX_RETURN'));exit;
            }
            $pid = $_POST['pid'];
            $email = $_POST['email'];
            $promote = M("Promote","tab_")->where(array('id'=>$pid))->find();
      $uid = $member->register(trim($_POST['account']),trim($_POST['password']),$email,$_POST['truename'],$_POST['card'],0,$pid,$promote['account'],$_POST['account']);
      if($uid>0) {
                M('User','tab_')->save(array("id"=>$uid,"phone"=>$_POST['account']));
                if ($pid) {
                    M('User','tab_')->where("id=$uid")->setField('promoteid',$pid);
                }
                $data['msg']="注册成功";
                $data['status']=1;
                $data['url']='';
                //$this->ajaxReturn($data,C('DEFAULT_AJAX_RETURN'));
      } else {
        $data['msg']  = '注册失败';
        $data['status'] =  0;
      }
            session('telsvcode',null);unset($telsvcode);            
            $this->ajaxReturn($data,C('DEFAULT_AJAX_RETURN'));
    } else {
      $this->redirect('Index/index');
    
    }   
  }


}