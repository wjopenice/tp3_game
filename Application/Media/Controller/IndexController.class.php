<?php
namespace Media\Controller;
use Think\Controller;
use User\Api\MemberApi;
use Org\XiguSDK\Xigu;
use Org\Util\Memcache as Memcache;

class IndexController extends BaseController {
    private $_cache;
    private $_timeout;
    public function __construct() {
        parent::__construct();
        //初始化
        $this->_cache = Memcache::instance();
    }

    //new7858首页展示
    public function index(){
        $this->get_games();
        $this->slide();
        //$this->hot();
        //$this->recommend();
        //$this->xin();
        //$this->juese();
        //$this->celue();
        //$this->xiuxian();
        //$this->kapai();
        $this->gift();
        //$this->appdown();
        $this->area();
        $this->rank();
        $this->zixun_adv();
        $this->display();
    }



    public function appdown(){
        $this->display();
    }

    private function _get_game_list() {
        //取所有在线游戏列表
        $key = "media_all_game_list";
        $game_list = $this->_cache->get($key);
        if(empty($game_list)) {
            $model = array(
                'm_name'=>'Game',
                'prefix'=>'tab_',
                'map'   =>array('game_status'=>1),
                'field' =>true,
                'order' =>'sort DESC'
            );
            $game_list = parent::list_data($model);
            $this->_cache->set($key, $game_list);
        }

        return $game_list;
    }

    public function get_games() {
        $game_list = $this->_get_game_list();

        //推荐游戏
        $reco = $this->get_game_by_type($game_list, 'recommend_status', '1', 4);

        //热门游戏
        $hot = $this->get_game_by_type($game_list, 'recommend_status', '2', 8);

        //最新游戏
        $xin = $this->get_game_by_type($game_list, 'recommend_status', '3', 6);

        //卡牌游戏
        $kapai = $this->get_game_by_type($game_list, 'game_type_id', '9', 8);

        //角色扮演
        $juese = $this->get_game_by_type($game_list, 'game_type_id', '6', 8);

        //策略养成
        $celue = $this->get_game_by_type($game_list, 'game_type_id', '8', 8);

        //休闲益智
        $xiuxian = $this->get_game_by_type($game_list, 'game_type_id', '13', 8);

        $this->assign('recommend',$reco);
        $this->assign('hot',$hot);
        $this->assign('xin',$xin);
        $this->assign('kapai',$kapai);
        $this->assign('juese',$juese);
        $this->assign('celue',$celue);
        $this->assign('xiuxian',$xiuxian);
    }

    private function get_game_by_type($game_list, $type_name, $type_value, $limit) {
        if(empty($game_list) || empty($limit)) {
            return array();
        }

        $result = array();

        foreach($game_list as $key => $value) {
            if($value[$type_name] == $type_value) {
                if(!$result[$value['id']] && count($result) < $limit) {
                    $result[$value['id']] = $value;
                }
            }
        }

        return $result;
    }

    //首页轮播图
    public function slide(){
        $adv = M("Adv","tab_");
        $map['status'] = 1;
        $map['pos_id'] = 1; #首页轮播图广告id
        $carousel = $adv->where($map)->order('sort ASC')->select();
        $this->assign("carousel",$carousel);
    }


    /***
     *推荐游戏  
     */
    public function recommend(){
        $model = array(
            'm_name'=>'Game',
            'prefix'=>'tab_',
            'map'   =>array('game_status'=>1,'recommend_status'=>1),
            'field' =>true,
            'order' =>'sort DESC',
            'limit' =>4,
        );
        $reco = parent::list_data($model);
        //print_r($reco);exit;
        $this->assign('recommend',$reco);
    }


    /***
     *热门游戏
     */
    public function hot(){
        $model = array(
            'm_name'=>'Game',
            'prefix'=>'tab_',
            'map'   =>array('game_status'=>1,'recommend_status'=>2),
            'field' =>true,
            'order' =>'sort DESC',
            'limit' =>8,
        );
        $hot = parent::list_data($model);
        $this->assign('hot',$hot);
    }


    /***
     *卡牌游戏
     */
    public function kapai(){
        $model = array(
            'm_name'=>'Game',
            'prefix'=>'tab_',
            'map'   =>array('game_status'=>1,'game_type_id'=>9),
            'field' =>true,
            'order' =>'sort DESC',
            'limit' =>8,
        );
        $kapai = parent::list_data($model);
        /*echo "<pre>";
        print_r($kapai);exit;
        echo "</pre>";*/
        $this->assign('kapai',$kapai);
    }


    /***
     *角色扮演
     */
    public function juese(){
        $model = array(
            'm_name'=>'Game',
            'prefix'=>'tab_',
            'map'   =>array('game_status'=>1,'game_type_id'=>6),
            'field' =>true,
            'order' =>'sort DESC',
            'limit' =>8,
        );
        $juese = parent::list_data($model);
        $this->assign('juese',$juese);
    }

    /***
     *策略养成
     */
    public function celue(){
        $model = array(
            'm_name'=>'Game',
            'prefix'=>'tab_',
            'map'   =>array('game_status'=>1,'game_type_id'=>8),
            'field' =>true,
            'order' =>'sort DESC',
            'limit' =>8,
        );
        $juese = parent::list_data($model);
        $this->assign('celue',$juese);
    }
    /***
     *休闲益智
     */
    public function xiuxian(){
        $model = array(
            'm_name'=>'Game',
            'prefix'=>'tab_',
            'map'   =>array('game_status'=>1,'game_type_id'=>13),
            'field' =>true,
            'order' =>'sort DESC',
            'limit' =>8,
        );
        $juese = parent::list_data($model);
        $this->assign('xiuxian',$juese);
    }


    /***
     *最新游戏
     */
    public function xin(){
        $model = array(
            'm_name'=>'Game',
            'prefix'=>'tab_',
            'map'   =>array('game_status'=>1,'recommend_status'=>3),
            'field' =>true,
            'order' =>'sort DESC',
           // 'order' =>'create_time desc',
            'limit' =>6,
        );
        $xin = parent::list_data($model);
        $this->assign('xin',$xin);
    }


    /***
     *游戏礼包
     */
    public function gift(){
        $key = 'media_index_game_gift';
        $gift = $this->_cache->get($key);
        if(empty($gift)){
            $model = array(
                'm_name'=>'Giftbag',
                'prefix'=>'tab_',
                'field' =>'tab_giftbag.id as gift_id,game_id,tab_giftbag.game_name,giftbag_name,giftbag_type,tab_game.icon,tab_giftbag.create_time',
                'join'	=>'tab_game on tab_giftbag.game_id = tab_game.id',
                'map'   =>array('game_status'=>1),
                //'order' =>'giftbag_name desc',
                'group' =>'tab_giftbag.game_id',
                'order' =>'tab_giftbag.id desc',
                'limit' =>10    ,
            );
            $gift = parent::join_data($model);

            $this->_cache->set($key, $gift);
        }
        $this->assign('gift',$gift);
    }


    /***
     *游戏开服
     */
    public function area(){
        $key = 'media_index_openserver';
        $area = $this->_cache->get($key);
        if(empty($area)) {
            $daytime = strtotime(date("Y-m-d"),time());
            $model = array(
                'm_name'=>'server',
                'prefix'=>'tab_',
                'field' =>'tab_server.*,tab_game.icon,tab_game.cover',
                'join'	=>'tab_game on tab_server.game_id = tab_game.id',
                'map'   =>array('game_status'=>1,'start_time'=>array('egt',$daytime)),
                'order' =>'start_time',
                //'limit' =>15,
            );
            $area = parent::join_data($model);

            //超时10分钟
            $this->_cache->set($key, $area, 600);
        }
        //print_r($area);exit;
        
        $this->assign('area',$area);
    }

    /**
     *游戏排行
     */
    public function rank() {
        $key = 'media_index_game_rank';
        $rank = $this->_cache->get($key);
        if(empty($area)) {
            $model = array(
                'm_name'=>'Game',
                'prefix'=>'tab_',
                'map'   =>array('game_status'=>1),
                'field' =>true,
                'order' =>'dow_mynum desc',
                'limit' =>10,
            );
            $rank = parent::list_data($model);

            //超时10分钟
            $this->_cache->set($key, $rank, 600);
        }
        //print_r($rank);exit;

        $this->assign('rank',$rank);
        $this->assign('num',1);
    }

    /**
     * 注册协议
     * @author zky
     * Date 2017-4-12
     */
    public function regareement(){
        $this->display();
    }


    /**
     *网络游戏协议
     * @author zky
     * Date 2017-4-12
     */
    public function interareement(){
        $this->display();
    }


    /**
     * 忘记密码用户账号验证
     * @author whh
     * Date 2017-4-10
     */
    public function wjmm(){

        
        if (IS_POST) {
            $account = I('post.account');
             //file_put_contents('E:/aaa.html',json_encode(22222));
            if(empty($account)){
                $data = array('status'=>0,'msg'=>'用户名不能为空');
                echo json_encode($data);exit;
            }
            //$_SESSION['account'] = $account;
            //session('account',$account);
            $user = M('User','tab_')->where("account='$account'")->find();
            //file_put_contents('E:/aaa.html',json_encode(22222));
            if (!empty($user) && is_array($user) && ($user['lock_status'] == 1)) {
                
                $data = array('status'=>1,'msg'=>$account);
                //self::$m=2;
                
                $m['user_id']=$user['id'];
                $m['user_account']=$account;
                $m['type']=1;
                $is=M('step','tab_')->where($m)->find();
                //file_put_contents('E:/aaa.html',json_encode($is));
                if(empty($is)){
                  $m['m']=2;
                  $m['create_time']=time();
                  M('step','tab_')->add($m);  
                }else{

                  $datam['m']=2;
                  $datam['create_time'] = time();
                  $wherem['user_id']=$user['id'];
                  $wherem['type']=1;
                  $wherem['user_account']=$account;
                  M('step','tab_')->where($wherem)->save($datam);
                }
                
                echo json_encode($data);
              
            } else {
                $data = array('status'=>0,'msg'=>'没有此账号！');
                echo json_encode($data);exit;

            }
           
        } else {
            $account=I('get.account');
            
            if (empty($account)) {
                $m=1;

            } else {
                $where['user_account']=$account;
                $where['type']=1;
                $step=M('step','tab_')->where($where)->getField('m');
                //$sql=M('step','tab_')->getLastSql();
                //file_put_contents('E:/aaa.html',json_encode($sql));
                if(empty($step)){
                    $step=1;
                }
                $m=$step; 

            }
            $data=M('User','tab_')->where("account='$account'")->find();
            //邮箱总长度
            $allen=strlen($data['email']);
            $one=stripos($data['email'],'@');
            $num=$allen-$one;
            $phone1 = substr($data['phone'],3,-4);
            $email1 = substr($data['email'],2,-$num);
            //file_put_contents('E:/aaa.html',json_encode($allen.'--'.$one.'--'.$num));
            
            $this->assign('ph',str_replace($phone1,'*******',$data['phone']));
            $this->assign('em',str_replace($email1,'*******',$data['email']));
            $this->assign('data',$data);
            $this->assign('m',$m);
            $this->display();
        }
    }
    protected function step_verify($m,$accountm){
        if (empty($m) || empty($accountm)) {
            return $data = json_encode(array('status'=>0,'msg'=>'非法操作！！'));exit;
        }
        $accountm['type']=1;
        $step=M('step','tab_')->where($accountm)->find();
        if ($step['m']==$m) {
            return true;
        } else {
            //file_put_contents('E:/aaa.html',json_encode($step['m'].'------'.$m));
            return $data = json_encode(array('status'=>0,'msg'=>'非法操作！！'));exit;
        }
        
    }

    /**
     * 忘记密码数据库验证手机是否存在
     * @author whh  XXXXXX
     * Date 2017-4-13
     */
    public function phone_verify(){
        $m=2;
        $account = I('post.account');
        $accountm['user_account']=$account;
        $result=$this->step_verify($m,$accountm);
        //file_put_contents('E:/aaa.html',json_encode($result));
        if ($result!==true) {
            echo $result;exit;
        }
        $phone= I('post.phone');
        //file_put_contents('E:/aaa.html',json_encode($account.'-------'.$phone));
        if(empty($phone) || empty($account)){
            $data = array('status'=>0,'msg'=>'请完善信息！');
            echo json_encode($data);exit;
        }
        //file_put_contents('E:/aaa.html',json_encode($account));
        $phone_now = M('User','tab_')->where(array('account'=>$account))->getField('phone');
        if(empty($phone_now)){
            $data = array('status'=>-1,'msg'=>'您还没有绑定手机号，请用邮箱找回或联系客服！');
            echo json_encode($data);exit;
            //$msg['status'] = -1;
        } else if($phone == $phone_now){
            $data = array('status'=>1,'msg'=>'手机号正确');
            echo json_encode($data);
        }else{
            $data = array('status'=>0,'msg'=>'手机号与绑定的不符！');
            echo json_encode($data);exit;
        }
        
    }

    /**
     * 发送验证码判断
     * @author whh
     */
    public function sendvcode() {
        $m=2;
        $account = I('post.name');
        $accountm['user_account']=$account;
        $result=$this->step_verify($m,$accountm);
        
        if ($result!==true) {
            echo $result;exit;
        }
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
     * 忘记密码手机发送验证码
     * @author whh
     * Date 2017-4-13
     */
    public function telsvcode($phone=null,$delay=10,$flag=true) {
        if (empty($phone)) {
            echo json_encode(array('status'=>0,'msg'=>'手机号不能为空'));exit; 
        }
        //产生手机安全码并发送到手机且存到session
        $rand = rand(100000,999999);
        $xigu = new Xigu(C('sms_set.smtp'));
        $param = $rand.",".$delay;
        $result = json_decode($xigu->sendSM(C('sms_set.smtp_account'),$phone,C('sms_set.smtp_port'),$param),true);
        // 存储短信发送记录信息
        $result['create_time'] = time();
        $result['pid']=0;
        if ($result['send_status'] != '000000') {
            echo json_encode(array('status'=>0,'msg'=>'发送失败，请重新获取'));exit;
        }
        $telsvcode['code']=$rand;
        $telsvcode['phone']=$phone;
        $telsvcode['time']=$result['create_time'];
        $telsvcode['delay']=$delay;
        session('telsvcode',$telsvcode);
        if ($flag) {
                
            echo json_encode(array('status'=>1,'msg'=>'安全码已发送，请查收'));
        } 
    }


    /**
     * 忘记密码 输入短信验证 下一步验证
     * @author whh
     * Date 2017-4-15
     */
    public function verifyvcode() {
        $m=2;
        $account = I('post.account');
        $accountm['user_account']=$account;
        $result=$this->step_verify($m,$accountm);
        //file_put_contents('E:/aaa.html',json_encode($result));
        if ($result!==true) {
            echo $result;exit;
        }
       if (IS_POST) {
            $telsvcode = session('telsvcode');
            $time = (time() - $telsvcode['time'])/60;
            if ($time>$telsvcode['delay']) {
                session('telsvcode',null);unset($telsvcode);
                echo json_encode(array('status'=>0,'msg'=>'时间超时,请重新获取验证码'));exit;
            }
            $phone = $_POST['phone'];
            //file_put_contents('E:/aaa.html',json_encode($phone.'-------'.$telsvcode['code'].'--------'.$_POST['vcode']));
            if (!($telsvcode['code'] == $_POST['vcode']) || !($telsvcode['phone'] == $phone)) {
                echo json_encode(array('status'=>0,'msg'=>'安全码输入有误'));exit;
                //file_put_contents('E:/aaa.html',json_encode($result));
            }else{
                //file_put_contents('E:/aaa.html',json_encode(11));
                
                $wherem['user_account']=$account;
                $wherem['type']=1;
                $is=M('step','tab_')->where($wherem)->find();
                
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
               echo json_encode(array('status'=>1,'msg'=>$account));

               
            }
       } else {
           echo json_encode(array('status'=>0,'msg'=>'非法操作！！'));exit;
       }
       
    }

    

    /**
     * 忘记密码 重置密码ajax接口 条件判断
     * @author whh
     * Date 2017-4-13
     */
    public function reset(){
        $m=3;
        $account = I('post.account');
        $accountm['user_account']=$account;
        $result=$this->step_verify($m,$accountm);
        //file_put_contents('E:/aaa.html',json_encode($result));
        if ($result!==true) {
            echo $result;exit;
        }
        //$account=I('post.account');
        $password=I('post.pwd');
        $repassword=I('post.repwd');
        //file_put_contents('E:/aaa.html',json_encode($result));
        if (empty($account) || empty($password) || empty($repassword)) {
            echo json_encode(array('status'=>0,'msg'=>'请输入密码！！'));exit;
        }
        if ($password != $repassword) {
            echo json_encode(array('status'=>0,'msg'=>'两次密码不一致！！'));exit;
        }
        $user = M('User','tab_')->where("account='$account'")->find();
        if (IS_POST) {
           $this->pwd($user['id'],$password,$account); 
        } else {
            $this->redirect('Public/login','', 0, '页面跳转中...'); 
           // echo json_encode(array('status'=>0,'msg'=>'非法操作！！'));exit;
        }
        
    }
    /**
     * 忘记密码 重置密码修改保存至数据库
     * @author whh
     * Date 2017-4-13
     */
    public function pwd($uid,$password,$account) {
        $m=3;
        $accountm['user_account']=$account;
        $result1=$this->step_verify($m,$accountm);
        //file_put_contents('E:/aaa.html',json_encode($result1));
        if ($result1!==true) {
            echo $result1;exit;
        }
        //file_put_contents('E:/aaa.html',json_encode($uid.'----'.$password));
        $member = new MemberApi();
        $result = $member->updatePassword($uid,$password);

        if ($result) {
            $wherem['user_account']=$account;
            $wherem['type']=1;
            $is=M('step','tab_')->where($wherem)->find();
            //file_put_contents('E:/aaa.html',json_encode($is));
            if(empty($is)){
               //file_put_contents('E:/aaa.html',json_encode(111));
               echo json_encode(array('status'=>0,'msg'=>'非法操作！！'));exit;  
            }else{
               $tepm['m']=4;
               $tepm['create_time']=time();
               //file_put_contents('E:/aaa.html',json_encode($tepm['m']));
               $as=M('step','tab_')->where($wherem)->save($tepm);
               //file_put_contents('E:/aaa.html',json_encode($as.'wwww'));
            }
            echo json_encode(array('status'=>1,'msg'=>$account));
        } else {
            echo json_encode(array('status'=>0,'msg'=>'修改密码失败！'));
        }
       
    }




    /**
     * 忘记密码数据库验证用户邮箱 验证成功之后发送邮件
     * @author whh   
     * Date 2017-4-17
     */
    public function tosendemail(){
        $m=2;
        $account = I('post.account');
        $accountm['user_account']=$account;
        $result1=$this->step_verify($m,$accountm);
        //file_put_contents('E:/aaa.html',json_encode($empty));
        if ($result1!==true) {
            echo $result1;exit;
        }
        $email= I('post.email');
        if(empty($email) || empty($account)){
           echo json_encode(array('status'=>0,'msg'=>'请完善信息！！'));exit;
        }
        $user = M('User','tab_')->where("account='$account'")->find();
        //file_put_contents('E:/aaa.html',json_encode($user));
        if($user['email'] != $email){
            echo json_encode(array('status'=>0,'msg'=>'邮箱与绑定的不符！！'));exit;
        }else{
            $where['user_account']=$account;
            $where['user_id']=$user['id'];
            $where['email']=$email;
            $where['status']=0;
            $time=time();
            $where['create_time']=$time;
            $where['ip']=get_client_ip();
            $key=rand(0,9999);
            $where['code']=md5($account.$time.$key);
            $info=M('User_email','tab_')->add($where);
             if(!$info){
             echo json_encode(array('status'=>0,'msg'=>'保存信息失败！')); exit;
            }else{
                  $result= sendMail('密码找回','亲爱的玩转平台用户您好，您提交了密码找回的申请，点击下方链接进行密码找回，链接24小时内有效:<br/>http://www.moguplay.com/media.php?s=/Index/sendemailfinish/user_id/'.$user['id'].'/create_time/'.$where['create_time'].'/code/'.$where['code'],$where['email']);
                    //file_put_contents('E:/aaa.html',json_encode($result));
                   if($result === true){                        
                        echo json_encode(array('status'=>1,'msg'=>'邮件发送成功！'));
                   }else{
                        echo json_encode(array('status'=>0,'msg'=>'邮件发送失败！')); exit;
                    
                   }
                
            }

             
        }

        
    }


    /**
     * 忘记密码 用户点击链接方法控制
     * @author  whh <[email address]>
     */
     public function sendemailfinish(){
          $m=2;
          $user_id=I('get.user_id');
          $accountm['user_id'] =$user_id;
          $result1=$this->step_verify($m,$accountm);
          //file_put_contents('E:/aaa.html',json_encode($result1));
          if ($result1!==true) {
              $this->error('非法操作！',U('Index/wjmm'));
          }
          //file_put_contents('E:/aaa.html',json_encode(111111111));
          $create_time=I('get.create_time');
          $whereip['create_time']=$create_time;
          $whereip['user_id']=$user_id;
          $usere_ip=M('User_email','tab_')->where($whereip)->find();
          $get_ip=get_client_ip();
          /*$sql=M('User_email','tab_')->getLastSql();
          file_put_contents('E:/aaa.html',json_encode($sql));*/
          if ($usere_ip['ip'] != $get_ip) {
              //file_put_contents('E:/aaa.html',json_encode($usere_ip['ip'].'-----'.$get_ip));
              $this->redirect('Public/login','', 0, '页面跳转中...');exit;
          } else { 
          
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
                            $wherem['user_id']=$user_id;
                            $wherem['type']=1;
                            $is=M('step','tab_')->where($wherem)->find();
                            
                            if(empty($is)){
                               //file_put_contents('E:/aaa.html',json_encode(111));
                               //跳到登录页面
                               $this->redirect('Public/login','', 0, '页面跳转中...'); 
                            }else{
                               $tepm['m']=3;
                               $tepm['create_time']=time();
                               $as=M('step','tab_')->where($wherem)->save($tepm);

                            }


                            $this->redirect('Index/wjmm',array('account'=>$usere_ip['user_account']), 0, '页面跳转中...');
                            
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
     * 忘记密码 点击邮箱链接，跳转重置密码页接口
     * @author zky
     * Date 2017-4-14
     */
    public function wjverify($user_id=0,$code=0) {
        $data['user_id'] = $user_id;
        $data['code'] = $code;
        $gain = M('User_email','tab_')->where($data)->find();
        if(empty($gain)){
           /*$data2 = array('status'=>0,'msg'=>'操作有误，我要报警');
            echo json_encode($data2);exit;*/
            $this->error('链接已失效',U('Index/wjmm'));exit;
        }else{
            $result=time()-$gain['create_time']-24*60*60;
            if($result < 0){
                if($code == $gain['code']){
                    $where['user_id'] = $user_id;
                    $state = M('User_email','tab_')->where($where)->save(array('status' => 1,'code' =>''));
                    //file_put_contents('E:/bbb.html',json_encode($state));
                    if($state === false){
                        $data = array('status'=>0,'msg'=>'非法操作，我要报警');
                        echo json_encode($data);exit;
                    }else{
                        return true;
                    }
                    //$this->redirect('Index/wjreset');
                }else{
                    $data = array('status'=>0,'msg'=>'非法操作');
                    //file_put_contents('E:/ccc.html',json_encode($data));
                    echo json_encode($data);exit;
                }
            }else{
                $data = array('status'=>0,'msg'=>'链接已失效');
                echo json_encode($data);exit;
            }
            $data = array('status'=>0,'msg'=>'邮件发送失败');
        }
    }

     /*
      *  首页游戏资讯广告图
      *  @author   whh
      */

//   public function zixun_adv(){
//        $adv = M("Adv","tab_");
//        $map['status'] = 1;
//        $map['pos_id'] = 4; #首页游戏资讯广告图id
//        $carousel = $adv->where($map)->order('sort ASC')->select();
//        $this->assign("data_zxadv",$carousel);
//    }

    public function zixun_adv(){
        $adv = M("Adv","tab_");

        $map['status'] = 1;
        $map['pos_id'] = 4; #首页游戏资讯广告图id
        $carousel = $adv->where($map)->order('sort ASC')->select();


        $map['status'] = 1;
        $map['pos_id'] = 8; #资讯页广告图id
        $left_top_zixun= $adv->where($map)->order('sort ASC')->select();

        $map['pos_id'] = 9; #资讯页广告图id
        $mid_top_zixun= $adv->where($map)->order('sort ASC')->select();

        $map['pos_id'] = 10; #资讯页广告图id
        $rig_top_zixun= $adv->where($map)->order('sort ASC')->select();

        $map['pos_id'] = 11; #资讯页广告图id
        $adv_zixun= $adv->where($map)->order('sort ASC')->select();
        //file_put_contents('E:/aaa.html',json_encode($left_top_zixun.'-----'.$mid_top_zixun.'-----'.$rig_top_zixun.'-----'.$adv_zixun));
        $this->assign("left_top",$left_top_zixun);
        $this->assign("mid_top",$mid_top_zixun);
        $this->assign("rig_top",$rig_top_zixun);
        $this->assign("adv_zixun",$adv_zixun);
        $this->assign("data_zxadv",$carousel);
    }

    


}