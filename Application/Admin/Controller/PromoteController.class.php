<?php
namespace Admin\Controller;
use User\Api\PromoteApi;
use User\Api\UserApi;
/**
 * 后台首页控制器
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
class PromoteController extends ThinkController {

    const model_name = 'Promote';

    public function lists(){
        if(isset($_REQUEST['account'])){
            $map['account']=array('like','%'.$_REQUEST['account'].'%');
            unset($_REQUEST['account']);
        }
         if(isset($_REQUEST['id'])){
            $map['id']=$_REQUEST['id'];
            unset($_REQUEST['id']);
        }
        if(isset($_REQUEST['status'])){
            $map['status']=$_REQUEST['status'];
            unset($_REQUEST['status']);
        }
         if(isset($_REQUEST['alipayway_sign'])){
            $map['alipayway_sign']=$_REQUEST['alipayway_sign'];
            unset($_REQUEST['alipayway_sign']);
        }
        if(isset($_REQUEST['admin_id'])){
            if($_REQUEST['admin_id']=='全部'){
                unset($_REQUEST['admin_id']);
            }else{
                $map['admin_id'] = $_REQUEST['admin_id'];
                unset($_REQUEST['admin_id']);
            }
        }

        if(isset($_REQUEST['promote_name'])){
                if($_REQUEST['promote_name']=='全部'){
                    #unset($_REQUEST['promote_name']);
                }else if($_REQUEST['promote_name']=='一级渠道'){
                    $map['parent_id']=array("eq",0);
                    #unset($_REQUEST['promote_name']);
                }else{
                    $map['parent_id']=get_promote_id($_REQUEST['promote_name']);
                   # unset($_REQUEST['promote_name']);
                }
        }
    	parent::lists(self::model_name,$_GET["p"],$map);
    }

    public function add($account=null,$password=null,$second_pwd=null,$real_name=null,$email=null,$mobile_phone=null,$bank_name=null,$bank_card=null,$admin=null,$status=null){
        if(IS_POST){
            $data=array('account'=>$account,'password'=>$password,'second_pwd'=>$second_pwd,'real_name'=>$real_name,'email'=>$email,'mobile_phone'=>$mobile_phone,'bank_name'=>$bank_name,'bank_card'=>$bank_card,'admin_id'=>$admin,'status'=>$status);
            $user = new PromoteApi();
            $res = $user->promote_add($data);
            if($res>0){
                $this->success("添加成功",U('lists'));
            }
            else{
                $this->error($res,U('lists'));
            }
        }
        else{
            $this->display();
        }
    }
    public function del($model = null, $ids=null){
        $model = M('Model')->getByName(self::model_name); 
        /*通过Model名称获取Model完整信息*/
        parent::del($model["id"],$ids);
    }
    //代充删除
    public function agent_del($model = null, $ids=null){
        $model = M('Model')->getByName('Agent'); 
        /*通过Model名称获取Model完整信息*/
        parent::del($model["id"],$ids);
    }
    public function edit($id=0){
		$id || $this->error('请选择要查看的用户！');
        $model = M('Model')->getByName(self::model_name); /*通过Model名称获取Model完整信息*/
        $data = array();
        if(IS_POST){
            $data = array(
                "id"         => $_POST['id'],
                "password"   => $_POST['password'],
                "second_pwd"   => $_POST['second_pwd'],
                "alipayway_sign"     => $_POST['alipayway_sign'],
                "status"     => $_POST['status'],
                "admin_id" => $_POST['admin']
            );
            $pwd = trim($_POST['password']);
             $second_pwd = trim($_POST['second_pwd']);
            $use=new UserApi();
            $data['password']=think_ucenter_md5($pwd,UC_AUTH_KEY);
            $data['second_pwd']=think_ucenter_md5($second_pwd,UC_AUTH_KEY);
            //密码修改记录
            if(empty($pwd)){
                unset($data['password']);
            }else{
                //file_put_contents('E:/aaa.html',json_encode($_POST));
                $pdata['admin_id']=session('user_auth.uid');
                $pdata['admin_name']=session('user_auth.username');
                $pdata['promote_id']=$_POST['id'];
                $pdata['promote_name']=$_POST['account'];
                $pdata['type']=1;
                $pdata['ip']=get_client_ip();
                $pdata['create_time']=time();
                $pchange=M('promote_change','tab_');
                $pchange->add($pdata);

            }
            //状态修改记录
            $wherep['id']=$_POST['id'];
            $status=M('promote','tab_')->where($wherep)->getfield('status');
            $statusp=$_POST['status'];
            if ($status != $statusp) {
                $sdata['admin_id']=session('user_auth.uid');
                $sdata['admin_name']=session('user_auth.username');
                $sdata['promote_id']=$_POST['id'];
                $sdata['promote_name']=$_POST['account'];
                $sdata['type']=2;
                $sdata['ip']=get_client_ip();
                $sdata['create_time']=time();
                $schange=M('promote_change','tab_');
                $schange->add($sdata); 
            }

            //直冲流水修改记录
            $alipayway_sign=M('promote','tab_')->where($wherep)->getfield('alipayway_sign');
            $alipayway_signp=$_POST['alipayway_sign'];
            if ($alipayway_sign != $alipayway_signp) {
                $adata['admin_id']=session('user_auth.uid');
                $adata['admin_name']=session('user_auth.username');
                $adata['promote_id']=$_POST['id'];
                $adata['promote_name']=$_POST['account'];
                $adata['type']=3;
                $adata['ip']=get_client_ip();
                $adata['create_time']=time();
                $achange=M('promote_change','tab_');
                $achange->add($adata); 
            }

            if(empty($second_pwd)){unset($data['second_pwd']);}
            $res=M("promote","tab_")->where(array("id"=>$_POST['id']))->save($data);
            if($res !== false){
                $this->success('修改成功',U('lists'));
            }
            else{
                $this->error('修改失败');
            }
        }
        else{
            $model = D('Promote');
            $data = $model->find($id);
            $this->assign('data',$data);
            $this->display();
        }
    }
    //设置状态
    public function set_status($model='Promote'){
        if(isset($_REQUEST['model'])){
            $model=$_REQUEST['model'];
            unset($_REQUEST['model']);
        }
        parent::set_status($model);
    }
    /**
    *渠道注册列表
    */
    public function ch_reg_list(){                                                   //游戏名称要么全部
        //print_r($_REQUEST);exit;
        if(!empty($_REQUEST['game_id'])){
            $map['game_id'] = $_REQUEST['game_id'];
            unset($_REQUEST['game_id']);
    }
         //$map['tab_user.promote_id'] = array("neq",0);
        if(!empty($_REQUEST['promote_name'])){
            if($_REQUEST['promote_name']=='自然注册'){                         //promote_name  推广员姓名 （tab_rebate_list）
                //$map['tab_user.promote_id']=array("eq",0);
                $map['tab_user.promote_account']=$_REQUEST['promote_name'];         //promote_account   推广员账号
            }else{
                $map['tab_user.promote_id']=get_promote_id($_REQUEST['promote_name']); //promote_id 推广id
            }
             unset($_REQUEST['promote_name']);
        }
        
        if(!empty($_REQUEST['account'])){
            $map['tab_user.account']=array('like','%'.$_REQUEST['account'].'%');        //登录账号
            unset($_REQUEST['account']);
        }
      if(!empty($_REQUEST['time_start'])&& !empty($_REQUEST['time_end'])){
            $map['register_time']=array('BETWEEN',array(strtotime($_REQUEST['time_start']),strtotime($_REQUEST['time_end'])+24*60*60-1));
            unset($_REQUEST['time_start']);unset($_REQUEST['time_end']);
        }
       
        $model = array(
            'm_name' => 'UserPlay',
            //'fields' => array('tab_user.account','tab_game.game_name','nickname','email','phone','promote_id'),
            /*'key'    => array('tab_user.account','tab_game.game_name'),*/
            'map'    => $map,
            'order'  => 'tab_user_play.user_id desc',
            'title'  => '渠道注册',
            'template_list' =>'ch_reg_list',
        );
       
        $user = A('User','Event');
        $user->user_left_join($model,$_GET['p']);
    }


    /**
    *渠道充值
    */
    public function spend_list(){
        if(isset($_REQUEST['game_id'])){
            if($_REQUEST['game_id']=='全部'){
                unset($_REQUEST['game_id']);
            }else{
                $map['game_id']=$_REQUEST['game_id'];
                unset($_REQUEST['game_id']);
            }
        }
        /*if(isset($_REQUEST['promote_name'])){
            if($_REQUEST['promote_name']=='全部'){
                unset($_REQUEST['promote_name']);
            }else if($_REQUEST['promote_name']=='自然注册'){
                $map['promote_id']=array("lte",0);
                
                unset($_REQUEST['promote_name']);
            }else{
                $map['promote_id']=get_promote_id($_REQUEST['promote_name']);
                unset($_REQUEST['promote_name']);
            }
        }else{
            $map['promote_id']=array("gt",0);
        }*/
        if(isset($_REQUEST['promote_id'])){
            if($_REQUEST['promote_id']=='全部'){
                unset($_REQUEST['promote_id']);
            }else{
                $map['promote_id']=$_REQUEST['promote_id'];
                unset($_REQUEST['promote_id']);
            }
        }
        if(isset($_REQUEST['pay_way'])){
            $map['pay_way']=$_REQUEST['pay_way'];
            unset($_REQUEST['pay_way']);
        }
        if(isset($_REQUEST['is_check'])&&$_REQUEST['is_check']!="全部"){
            $map['is_check']=check_status($_REQUEST['is_check']);
            unset($_REQUEST['is_check']);
        }
        if(isset($_REQUEST['user_account'])){
            $map['user_account']=array('like','%'.$_REQUEST['user_account'].'%');
            unset($_REQUEST['user_account']);
        }
       /* if(isset($_REQUEST['promote_name'])){
            $map['promote_account']=$_REQUEST['promote_name'];
            unset($_REQUEST['promote_name']);
        }*/
        if(isset($_REQUEST['time-start'])&&isset($_REQUEST['time-end'])){
            $map['pay_time']=array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])));
            unset($_REQUEST['time-start']);unset($_REQUEST['time_end']);
        }
        if(isset($_REQUEST['start'])&&isset($_REQUEST['end'])){
            $map['pay_time']=array('BETWEEN',array(strtotime($_REQUEST['start']),strtotime($_REQUEST['end'])));
            unset($_REQUEST['start']);unset($_REQUEST['end']);
        }
        $model = array(
            'm_name' => 'Spend',
            'map'    => $map,
            'order'  => 'id desc',
            'title'  => '渠道充值',
            'template_list' =>'spend_list',
        );
        $map1=$map;
        $map1['pay_status']=1;
        $total=M('Spend','tab_')->where($map1)->sum('pay_amount');
        $total=sprintf("%.2f",$total);
        $this->assign('total',$total);
        $user = A('Spend','Event');
        $user->spend_list($model,$_GET['p']);
    }

    /**
    *代充记录
    */
    public function agent_list(){
        //$map['promote_id'] = array("neq",0);
        if(isset($_REQUEST['user_account'])){
            $map['user_account']=array('like','%'.$_REQUEST['user_account'].'%');
            unset($_REQUEST['user_account']);
        }
        if(isset($_REQUEST['pay_status'])){
            $map['pay_status']=$_REQUEST['pay_status'];
            unset($_REQUEST['pay_status']);
        }
        if(isset($_REQUEST['pay_type'])){
            $map['pay_type']=$_REQUEST['pay_type'];
            unset($_REQUEST['pay_type']);
        }
        if(isset($_REQUEST['admin_id'])){
            if($_REQUEST['admin_id']=='全部管理员'){
                unset($_REQUEST['admin_id']);
            }else{
                $is['admin_id']=$_REQUEST['admin_id'];
                $promote_id =M('promote','tab_')->where($is)->getField('id',true);
                if (isset($promote_id)) {
                    $map['promote_id'] = array('in',$promote_id);
                }else{
                    $map['promote_id'] = '-1,-2';
                }   
                unset($_REQUEST['admin_id']);
            }
        }
        /*if(isset($_REQUEST['promote_name'])){
            if($_REQUEST['promote_name']=='全部'){
                unset($_REQUEST['promote_name']);
            }else if($_REQUEST['promote_name']=='自然注册'){
                $map['promote_id']=array("elt",0);
                unset($_REQUEST['promote_name']);
            }else{
                $map['promote_id']=get_promote_id($_REQUEST['promote_name']);
                unset($_REQUEST['promote_name']);
            }
        }*/
        if(isset($_REQUEST['promote_id'])){
                if($_REQUEST['promote_id']=='全部'){
                    unset($_REQUEST['promote_id']);
                }else if($_REQUEST['promote_id']=='自然注册'){
                    $map['promote_id']=array("elt",0);
                    #unset($_REQUEST['promote_name']);
                }else{
                    $map['promote_id']=$_REQUEST['promote_id'];
                   # unset($_REQUEST['promote_name']);
                }
        }
        if(isset($_REQUEST['time-start'])&&isset($_REQUEST['time-end'])){
            $map['create_time']=array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])+24*60*60-1));
            unset($_REQUEST['time-start']);unset($_REQUEST['time_end']);
        }
        if(isset($_REQUEST['start'])&&isset($_REQUEST['end'])){
            $map['create_time']=array('BETWEEN',array(strtotime($_REQUEST['start']),strtotime($_REQUEST['end'])+24*60*60-1));
            unset($_REQUEST['start']);unset($_REQUEST['end']);
        }
        /*if(isset($_REQUEST['game_name'])){
            if($_REQUEST['game_name']=='全部'){
                unset($_REQUEST['game_name']);
            }else{
                $map['game_name']=$_REQUEST['game_name'];
                unset($_REQUEST['game_name']);
            }
        }*/
        if(isset($_REQUEST['cp_name'])){
            if($_REQUEST['cp_name'] != '全部'){
                $where_cp_name['cp_name']=I('cp_name');
                $game_id_arr = M('Game','tab_')->where($where_cp_name)->getField('id',true);
                $map['game_id'] = array('in',$game_id_arr);
            }
        }
        if(isset($_REQUEST['game_id'])){
            if($_REQUEST['game_id']=='全部'){
                unset($_REQUEST['game_id']);
            }else{
                $map['game_id']=$_REQUEST['game_id'];
                unset($_REQUEST['game_id']);
            }
        }

        $map1=$map;
        $map1['pay_status']=1;
        $total=M('Agent','tab_')->where($map1)->sum('amount');
        $total=sprintf("%.2f",$total);
        $this->assign('total',$total);
        parent::lists('Agent',$_GET["p"],$map);
    }
    /**
    *代充额度
    */
    public function pay_limit(){
        if(isset($_REQUEST['account'])){
            $map['account']=array('like','%'.$_REQUEST['account'].'%');
            unset($_REQUEST['account']);
        }
        if(isset($_REQUEST['promote_name'])){
            if($_REQUEST['promote_name']=='全部'){
                unset($_REQUEST['promote_name']);
            }else if($_REQUEST['promote_name']=='自然注册'){
                $map['id']=array("elt",0);
                unset($_REQUEST['promote_name']);
            }else{
                $map['id']=get_promote_id($_REQUEST['promote_name']);
                unset($_REQUEST['promote_name']);
            }
        }
        if(isset($_REQUEST['admin_id'])){
            if($_REQUEST['admin_id']=='全部'){
                unset($_REQUEST['admin_id']);
            }else{
                $map['admin_id'] = $_REQUEST['admin_id'];
                unset($_REQUEST['admin_id']);
            }
        }
        $row=10;
        $map['pay_limit']=array('gt','0');
        $page = intval($_GET['p']);
        $page = $page ? $page : 1; //默认显示第一页数据
        $model=D('Promote');
        $data=$model
        ->field('id,account,pay_limit,set_pay_time,admin_id')
        ->where($map)
        ->order('set_pay_time desc')
        ->page($page, 10)
        ->select();
        $count=$model
        ->field('id,account,pay_limit,admin_id')
        ->where($map)
        ->count();
        if($count > $row){
            $page = new \Think\Page($count, $row);
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $this->assign('_page', $page->show());
        }
        $this->assign('list_data', $data);
        $this->display();
    }
    public function pay_limits_add()
    {
        
        if(IS_POST){
            $type = $_REQUEST['type'];
            switch ($type) {
                case 1:
                    $this->limits1();
                    break;
                case 2:
                    $this->limits2();
                    break;
            }
        }else{
            $this->display();
        }
    }
    public function limits1(){
        $limit=D("Promote");
        if(trim($_REQUEST['promote_id'])==''){
        $this->error("请选择管理员推广员");
        }
        if(trim($_REQUEST['limits'])==''){
        $this->error("请输入代充额度");
        }
        $data['id']=$_REQUEST['promote_id'];
        $data['pay_limit']=$_REQUEST['limits'];
        $find=$limit->where(array("id"=>$data['id']))->find();
        if($find['pay_limit']!=0){
        $this->error("已经设置过该推广员",U('pay_limit'));
        }else{
         $limit->where(array("id"=>$data['id']))->setField('pay_limit',trim($_REQUEST['limits']));
         $limit->where(array("id"=>$data['id']))->setField('set_pay_time',time());
         $this->success("添加成功！",U('pay_limit'));
        }
    }
    public function limits2(){
        $limit=D("Promote");
        if(trim($_REQUEST['all_names'])==''){
        $this->error("请填写推广员名称");
        }
        if(trim($_REQUEST['limits'])==''){
        $this->error("请输入代充额度");
        }
        $account=$_REQUEST['all_names'];
        $pay_limit=$_REQUEST['limits'];
        $namearr = explode("\n",$account);
        for($i=0;$i<count($namearr);$i++){
            $user=get_promotelimit_one_list(str_replace(array("\r\n", "\r", "\n"), "", $namearr[$i]));
            if(null!=$user){
                $limit->where(array("id"=>$user['id']))->setField('pay_limit',trim($pay_limit));
                $limit->where(array("id"=>$user['id']))->setField('set_pay_time',time());
                
            }
        }
        $this->success("添加成功！",U('pay_limit'));

        
    }
    public function pay_limit_del()
    {
        $limit=D("Promote");
        if(empty($_REQUEST['ids'])){
            $this->error('请选择要操作的数据');
        }
        if(isset($_REQUEST['ids'])){
            $id=$_REQUEST['ids'];
        }
         $limit
         ->where(array("id"=>$id))
         ->setField('pay_limit','0');
         $this->success("删除成功！",U('pay_limit'));
    }
    public function pay_limit_edit()
    {
        $limit=D("Promote");
        if(IS_POST){
            if(trim($_REQUEST['promote_id'])==''){
            $this->error("请选择管理员推广员");
            }
            if(trim($_REQUEST['limits'])==''){
            $this->error("请输入代充额度");
            }
            $data['id']=$_REQUEST['promote_id'];
             $edit=$limit->where(array("id"=>$data['id']))->setField('pay_limit',trim($_REQUEST['limits']));
             $limit->where(array("id"=>$data['id']))->setField('set_pay_time',time());
             if($edit==0){
                $this->error('数据未更改');
             }else{
                $this->success("编辑成功！",U('pay_limit'));
            }
        }else{
            $edit_data=$limit
            ->where(array('id'=>$_REQUEST['ids']))
            ->find();
            $this->assign('edit_data',$edit_data);
            $this->display();
        }
    }

    public function recall()
    {
        $map['id']=$_REQUEST['id'];
        $user=M("promote","tab_")->where($map)->setField("balance_coin",$_REQUEST['balance']);
        if($user>0){
            $add['cancel_id']=$_REQUEST['id'];
            $add['cancel_name']=get_promote_name($_REQUEST['id']);
            $add['money']=$_REQUEST['balance'];
            $add['prev_money']=$_REQUEST['prev_balance'];
            $add['type']=1;
            $add['admin_id']=session('user_auth.uid');
            $add['admin_name']=session('user_auth.username');            
            $add['create_time']=time();
            M('cancel','tab_')->add($add);
          echo json_encode(array("status"=>1,"msg"=>"修改成功"));
        }else{
          echo json_encode(array("status"=>-1,"msg"=>"修改失败"));
        }
    }
 
    //平台币修改记录
    public function set_balancelist($p=0){
        if(isset($_REQUEST['cancel_name'])){
            $map['cancel_name']=array('like',"%".$_REQUEST['cancel_name']."%");
            unset($_REQUEST['cancel_name']);
        }
        if(isset($_REQUEST['time-start']) && isset($_REQUEST['time-end'])){
            $map['create_time'] =array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])+24*60*60-1));
            unset($_REQUEST['time-start']);unset($_REQUEST['time-end']);
        }
        $map['type']=1;
         $extend=array();
        $extend['map']=$map;
        parent::lists("cancel",$p,$extend['map']);
    }
    /**
    *支付宝代充额度
    */
    public function alipay_limit(){
        if(isset($_REQUEST['account'])){
            $map['account']=array('like','%'.$_REQUEST['account'].'%');
            unset($_REQUEST['account']);
        }
        if(isset($_REQUEST['promote_name'])){
            if($_REQUEST['promote_name']=='全部'){
                unset($_REQUEST['promote_name']);
            }else if($_REQUEST['promote_name']=='自然注册'){
                $map['id']=array("elt",0);
                unset($_REQUEST['promote_name']);
            }else{
                $map['id']=get_promote_id($_REQUEST['promote_name']);
                unset($_REQUEST['promote_name']);
            }
        }
        $row=10;
        $map['alipay_limit']=array('gt','0');
        $page = intval($_GET['p']);
        $page = $page ? $page : 1; //默认显示第一页数据
        $model=D('Promote');
        $data=$model
        ->field('id,account,alipay_limit,set_alipay_time')
        ->where($map)
        ->page($page, 10)
        ->select();
        $count=$model
        ->field('id,account,alipay_limit')
        ->where($map)
        ->count();
        if($count > $row){
            $page = new \Think\Page($count, $row);
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $this->assign('_page', $page->show());
        }
        //print_r($data);exit;
        $this->assign('list_data', $data);
        $this->display();
    }
    public function alipay_limits_add()
    {
        $limit=D("Promote");
        if(IS_POST){
            if(trim($_REQUEST['promote_id'])==''){
            $this->error("请选择管理员推广员");
            }
            if(trim($_REQUEST['limits'])==''){
            $this->error("请输入代充额度");
            }
            $data['id']=$_REQUEST['promote_id'];
            $data['alipay_limit']=$_REQUEST['limits'];
            $find=$limit->where(array("id"=>$data['id']))->find();
            if($find['alipay_limit']!=0){
            $this->error("已经设置过该推广员",U('alipay_limit'));
            }else{
             $limit->where(array("id"=>$data['id']))->setField('alipay_limit',trim($_REQUEST['limits']));
             $limit->where(array("id"=>$data['id']))->setField('set_alipay_time',time());
             $this->success("添加成功！",U('alipay_limit'));
            }
        }else{
            $this->display();
        }
    }
    public function alipay_limit_del()
    {
        $limit=D("Promote");
        if(empty($_REQUEST['ids'])){
            $this->error('请选择要操作的数据');
        }
        if(isset($_REQUEST['ids'])){
            $id=$_REQUEST['ids'];
        }
         $limit
         ->where(array("id"=>$id))
         ->setField('alipay_limit','0');
         $this->success("删除成功！",U('alipay_limit'));
    }
    public function alipay_limit_edit()
    {
        $limit=D("Promote");
        if(IS_POST){
            if(trim($_REQUEST['promote_id'])==''){
            $this->error("请选择管理员推广员");
            }
            if(trim($_REQUEST['limits'])==''){
            $this->error("请输入代充额度");
            }
            $data['id']=$_REQUEST['promote_id'];
             $edit=$limit->where(array("id"=>$data['id']))->setField('alipay_limit',trim($_REQUEST['limits']));
             $limit->where(array("id"=>$data['id']))->setField('set_alipay_time',time());
             if($edit==0){
                $this->error('数据未更改');
             }else{
                $this->success("编辑成功！",U('alipay_limit'));
            }
        }else{
            $edit_data=$limit
            ->where(array('id'=>$_REQUEST['ids']))
            ->find();
            $this->assign('edit_data',$edit_data);
            $this->display();
        }
    }


/**
    *支付宝充值记录
    */
    public function alipay_list(){
        if(isset($_REQUEST['pay_status'])){
            $map['pay_status']=$_REQUEST['pay_status'];
            unset($_REQUEST['pay_status']);
        }
        if(isset($_REQUEST['pay_way'])){
            $map['pay_way']=$_REQUEST['pay_way'];
            unset($_REQUEST['pay_way']);
        }
        if(isset($_REQUEST['promote_account'])){
            if($_REQUEST['promote_account']=='全部'){
                unset($_REQUEST['promote_account']);
            }else if($_REQUEST['promote_account']=='自然注册'){
                $map['promote_id']=array("elt",0);
                unset($_REQUEST['promote_account']);
            }else{
                $map['promote_id']=get_promote_id($_REQUEST['promote_account']);
                unset($_REQUEST['promote_account']);
            }
        }
        if(isset($_REQUEST['time-start'])&&isset($_REQUEST['time-end'])){
            $map['create_time']=array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])+24*60*60-1));
            unset($_REQUEST['time-start']);unset($_REQUEST['time_end']);
        }
        if(isset($_REQUEST['start'])&&isset($_REQUEST['end'])){
            $map['create_time']=array('BETWEEN',array(strtotime($_REQUEST['start']),strtotime($_REQUEST['end'])+24*60*60-1));
            unset($_REQUEST['start']);unset($_REQUEST['end']);
        }
        $row=15;
        $data = M('Promote_deposit','tab_')
                /* 查询指定字段，不指定则查询所有字段 */
                ->field(empty($fields) ? true : $fields)
                // 查询条件
                ->where($map)
                /* 默认通过id逆序排列 */
                ->order(empty($map['order'])?"id desc":$map['order'])
                /* 数据分页 */
                ->page($_GET["p"], $row)
                /* 执行查询 */
                ->select();
            /* 查询记录总数 */
        $count =M('Promote_deposit','tab_')->where($map)->count();
     
         //分页
        if($count > $row){
            $page = new \Think\Page($count, $row);
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $this->assign('_page', $page->show());
        }
        $map['promote_id'] = array("neq",0);
        $map1=$map;
        $map1['pay_status']=1;
        $total=M('Promote_deposit','tab_')->where($map1)->sum('pay_amount');
        $total=sprintf("%.2f",$total);
        $this->assign('total',$total);
        $this->assign('list_data',$data);
        //parent::lists('Promote_deposit',$_GET["p"],$map);
        $this->display();
    }


            /**
        *渠道信息修改记录
        *@author whh
        */
        public function promote_change(){
        if(isset($_REQUEST['promote_name'])){
            $map['promote_name']=array('like',"%".$_REQUEST['promote_name']."%");
            unset($_REQUEST['promote_name']);
        }
         if(isset($_REQUEST['type'])){
            $map['type']=$_REQUEST['type'];
            unset($_REQUEST['type']);
        }
        if(isset($_REQUEST['time-start']) && isset($_REQUEST['time-end'])){
            $map['create_time'] =array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])+24*60*60-1));
            unset($_REQUEST['time-start']);unset($_REQUEST['time-end']);
        }
        

        $row=10;
        $page = intval($_GET['p']);
        $page = $page ? $page : 1; //默认显示第一页数据
        $model=M('promote_change','tab_');
        $data=$model
        ->field('id,promote_name,type,admin_name,ip,create_time')
        ->where($map)
        ->order('id desc')
        ->page($page, 10)
        ->select();
        //print_r($data);exit;
        $count=$model
        ->field('id,promote_name,type,admin_name,ip,create_time')
        ->where($map)
        ->count();
        if($count > $row){
            $page = new \Think\Page($count, $row);
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $this->assign('_page', $page->show());
        }
        //print_r($data);exit;
        $this->assign('list_data', $data);
        $this->display();
      }

}
