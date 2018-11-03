<?php
namespace Admin\Controller;
use User\Api\UserApi as UserApi;
use Org\Util\Memcache as Memcache;
ini_set('memory_limit','1500M');
class StatController extends ThinkController {


/**
 * 后台首页控制器
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
    private $_cache;
    private $_timeout;

    public function __construct() {
        parent::__construct();
        //初始化并传memcache前缀
        $this->_cache = Memcache::instance('admin_');
        //超时时间
        //$this->_timeout = 1800;
        $this->_timeout = 1;
    }

    // /**
    // *日常统计
    // */
    public function daily($value='')
    {


        $stat = A('Stat','Event');


        $stat->spend_statistics();

        $stat->register_statistics();

        $stat->spend_statistics_year();

        $stat->register_statistics_year();


        $stat->display();
    }
    
    /**
    *支付方式统计
    */
    public function pay_way($type=null){
        $pay_way = A('Payway','Event');
        switch ($type) {
            case '0':
                $pay_way->this_month();
                break;
            case '1':
                $pay_way->last_month();
                break;
            case '2':
                $pay_way->this_week();
                break;
            case '3':
                $pay_way->last_week();
                break;
            default:
                $pay_way->this_year();
                break;
        }
        $this->display();
    }

    //登录统计
    public function cpa_login_bak()
    {
        $page = intval($_REQUEST['p']);
        $page = $page ? $page : 1;
        $fields = array("promote_id");
        $key    =   "game_name";
        if(isset($_REQUEST['game_name'])){
            if($_REQUEST['game_name']=='全部'){
                unset($_REQUEST['game_name']);
            }
            else{
                $map['tab_game.game_appid'] = get_game_appid($_REQUEST['game_name']);
                unset($_REQUEST['game_name']);
            }
        }

        if(isset($_REQUEST['promote_name'])) {
            if($_REQUEST['promote_name']=='全部'){
                unset($_REQUEST['promote_name']);
            }else if($_REQUEST['promote_name']=='自然注册'){
                $map['promote_id']=array("elt",0);
                unset($_REQUEST['promote_name']);
            }else{
                $map['promote_id'] = get_promote_id($_REQUEST['promote_name']);
                unset($_REQUEST['promote_name']);
            }
        }

        if(isset($_REQUEST[$key])){
            $map[$key]  =   array('like','%'.$_GET[$key].'%');
            unset($_REQUEST[$key]);
        }

        // 条件搜索
        foreach($_REQUEST as $name=>$val){
            if(in_array($name,$fields)){
                $map[$name] =   $val;
            }
        }

        if(isset($_REQUEST['game_id'])){
            $map["tab_game.id"] = $_REQUEST['game_id'];
            unset($_REQUEST['game_id']);
        }

        //字符串where条件
        $map['_string'] = 'tab_user_play.game_appid = tab_game.game_appid';

        $row = 10;

        $key = "cpa_login_{$page}_{$row}_".md5(json_encode($map));
        $result = $this->_cache->get($key);
        if(empty($result)) {
            $data = M('user_play','tab_')
                 //查询指定字段，不指定则查询所有字段 
                ->field("tab_user_play.game_appid,count(tab_user_play.game_appid) as count,tab_user_play.promote_id")
                ->join("left join tab_game on tab_user_play.game_id = tab_game.id")
                // 查询条件
                ->group("tab_user_play.game_appid,tab_user_play.promote_id")
                ->where($map)
                /* 数据分页 */
                ->page($page, $row)
                /* 执行查询 */
                ->select();

            //执行sql输出
            //echo M()->_sql();

            $count = M('user_play','tab_')
                    /* 查询指定字段，不指定则查询所有字段 */
                    ->field("tab_user_play.game_appid,count(tab_user_play.game_appid) as count,tab_user_play.promote_id")
                    ->join("left join tab_game on tab_user_play.game_id = tab_game.id")
                    // 查询条件
                    ->group("tab_user_play.game_appid,tab_user_play.promote_id")
                    ->where($map)
                    /* 执行查询 */
                    ->select();

            /* 查询记录总数 */
            $count = count($count);

            $result = array('data'=>$data, 'count'=>$count);
            $this->_cache->set($key, $result, $this->_timeout);
        }

        $data = $result['data'];
        $count = $result['count'];

        //分页
        if($count > $row){
            $page = new \Think\Page($count, $row);
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $this->assign('_page', $page->show());
        }

        $this->assign('game_name',I("game_name"));
        $this->assign("reco",I("time"));
        $this->assign("list_data",$data);
        $this->assign("guild",I("account"));
        $this->display();
    }

     /**
     * 注册统计
     * @author zdd
     * @param game_id=-1时代表查询的是无效用户数据,-2时代表是空的,-3代表全部
     * @param promote_id=-2时代表是空的,-3代表是全部
     */
    public function cpa_register(){
        $page = intval($_REQUEST['p']);
        $page = $page ? $page : 1;
        $row = intval($_REQUEST['row']);
        $row = $row ? $row : 15;
        $group='p.game_id,p.promote_id';
        if(isset($_REQUEST['time_start'])&&isset($_REQUEST['time_end'])){
                    $map['u.register_time']=array('BETWEEN',array(strtotime($_REQUEST['time_start']),strtotime($_REQUEST['time_end'])+24*60*60-1));
                    unset($_REQUEST['time_start']);unset($_REQUEST['time_end']);
                }
        $game_id = I('get.game_id');
        $promote_id = I('get.promote_id');
        if($game_id > -2){
            $map['p.game_id'] = $game_id;
        }
        if($promote_id > -1){
            $map['p.promote_id'] = $promote_id; 
        }
        switch ($game_id) {
            case '':
                unset($map['p.game_id']);
                unset($map['p.promote_id']);
                $group='p.game_id,p.promote_id';
                $fields =  'p.game_name,p.promote_account,count(*) num';
                break;
            case -1:
                $group = 'p.promote';
                break;
            case -2:
                if($promote_id == -2){
                    $group = 'p.game_id,p.promote_id';
                    $fields =  'p.game_name,p.promote_account,count(*) num';
                }else if($promote_id == -3){
                    $group = 'p.promote_id';
                    $fields =  'p.promote_account,count(*) num ';
                }else{
                    $group = 'p.promote_id';
                    $fields =  'p.game_name,p.promote_account,count(*) num ';
                }
                break;
            case -3;
                if($promote_id == -2){
                    $group = 'p.game_id';
                    $fields =  'p.game_name,count(*) num ';
                }else if($promote_id == -3){
                    $group = 'p.game_id';
                    $fields = 'p.game_name,count(*) num ';
                }else{
                    $group = 'p.promote_id,p.game_id';
                    $fields = 'p.promote_account,p.game_name,count(*) num';
                }
                break;
            default:
                if($promote_id == -2){
                    $group = 'p.game_id';
                    $fields =  'p.game_name,count(*) num ';
                }else{
                    $group = 'p.promote_id,p.game_id';
                    $fields = 'p.promote_account,p.game_name,count(*) num';
                }
                break;
        }
        //game_id为 -1 代表查询的是无效用户的数据
        if($map['p.game_id'] == -1){
            unset($map['p.game_id']);
            $count_user_play= M('UserPlay','tab_')->alias('p')->join('tab_user as u on p.user_id = u.id')->field('u.id')->where($map)->group('user_account')->select();
            $count_user_play = count($count_user_play);
            if($map['p.promote_id'] > -1){
               $map['promote_id'] = $map['p.promote_id']; 
               unset($map['p.promote_id']);
            }
            $count_user = M('User','tab_')->alias('u')->field('id')->where($map)->group('account')->select();
            $count_user = count($count_user);
            $map_promote['id'] = $map['p.promote_id'];
            $promote_account = M('Promote','tab_')->where($map_promote)->getField('account');
            $num = $count_user - $count_user_play;
            $list_data[0]['game_name'] = "无效用户";
            $list_data[0]['promote_account'] = $promote_account;
            $list_data[0]['num'] = $num;
        }else{
          
            $list_data = M('UserPlay','tab_')->alias('p')->join('tab_user as u on p.user_id = u.id')->field($fields)->where($map)->page($page,$row)->group($group)->select();
            $count = M('UserPlay','tab_')->alias('p')->join('tab_user as u on p.user_id = u.id')->field('u.id')->where($map)->group($group)->select();
            $count=count($count);
        }
        $this->assign('list_data',$list_data);
        if($count > $row){
        $page = new \Think\Page($count, $row);
        $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
        $this->assign('_page', $page->show());
        }
        $this->display();
    }

    /**
     * 消费统计   加缓存
     * @author whh
     * @param  
     * @param 
     */
    public function cpa_spend_bak(){
        $page = intval($_REQUEST['p']);
        $page = $page ? $page : 1;
        $where_str = "pay_status=1";
        if(isset($_REQUEST['promote_id'])){
                if($_REQUEST['promote_id']=='全部'){
                    unset($_REQUEST['promote_id']);
                }else if($_REQUEST['promote_id']=='自然注册'){
                    $promote_id=array("elt",0);
                    $where_str .= " and promote_id >= 0";
                    #unset($_REQUEST['promote_name']);
                }else{
                    $promote_id=$_REQUEST['promote_id'];
                    $where_str .= " and promote_id = '{$promote_id}'";
                   # unset($_REQUEST['promote_name']);
                }
        }
        if(isset($_REQUEST['game_id'])){
            if($_REQUEST['game_id']=='全部'){
                unset($_REQUEST['game_id']);
            }else{
                $game_id=$_REQUEST['game_id'];
                $where_str .= " and game_id = '{$game_id}'";
            }
            unset($_REQUEST['game_id']);
        }
        if(isset($_REQUEST['time-start'])&&isset($_REQUEST['time-end'])){
            //$pay_time =array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])));
            //unset($_REQUEST['time-start']);unset($_REQUEST['time-end']);
            $where_str .= " and pay_time between '".strtotime($_REQUEST['time-start'])."' and '".(strtotime($_REQUEST['time-end'])+24*60*60-1)."'";
        }
        if(isset($_REQUEST['start'])&&isset($_REQUEST['end'])){
            //$pay_time  =array('BETWEEN',array(strtotime($_REQUEST['start']),strtotime($_REQUEST['end'])));
            $where_str .= " and pay_time between '".strtotime($_REQUEST['start'])."' and '".(strtotime($_REQUEST['end'])+24*60*60-1)."'";
        }
       

        $row = 10;
        $size = ($page-1)*$row;
        $key = "cpa_spend_{$page}_{$row}_".md5(json_encode($map));
        $result = $this->_cache->get($key);
        if(empty($result)) {
            $Model = D();
            $sql = "select sum(f1) as count, sum(f2) as ucount,game_id, promote_id from (select sum(s.pay_amount) as f1, count(DISTINCT s.user_id) as f2,s.game_id, s.promote_id from tab_spend as s where {$where_str} group by s.game_id, s.promote_id union all select sum(b.pay_amount) as f1, count(DISTINCT b.user_id) as f2,b.game_id, b.promote_id from tab_bind_spend as b where {$where_str} group by b.game_id, b.promote_id) A GROUP BY game_id, promote_id order by game_id desc limit {$size},{$row}";
            $data = $Model->query($sql);
            //print_r($data);exit;
            $countsql = "select count(DISTINCT game_id,promote_id) as count from (select sum(s.pay_amount) as f1, count(DISTINCT s.user_id) as f2, s.game_id, s.promote_id from tab_spend as s where {$where_str} group by s.game_id, s.promote_id union all select sum(b.pay_amount) as f1, count(DISTINCT b.user_id) as f2, b.game_id, b.promote_id from tab_bind_spend as b where {$where_str} group by b.game_id, b.promote_id) A";
            $num =$Model->query($countsql);
            $count=intval($num[0]['count']);
            //print_r($count);exit;
            $totalsql = "select sum(f1) as total from (select sum(s.pay_amount) as f1 from tab_spend as s where {$where_str}  union all select sum(b.pay_amount) as f1 from tab_bind_spend as b where {$where_str} ) A";
            $tot=$Model->query($totalsql);
            $total=intval($tot[0]['total']);
            $total = sprintf("%.2f",$total);
            
            $result = array('data'=>$data, 'count'=>$count,'total'=>$total);
            $this->_cache->set($key, $result, $this->_timeout);
        }
        $total = $result['total'];
        $data = $result['data'];
        $count = $result['count'];

        //分页
        if($count > $row){
            $page = new \Think\Page($count, $row);
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $this->assign('_page', $page->show());
        }
        $this->assign('gamename',I("game_name"));
        $this->assign("reco",I("time"));
        $this->assign("list_data",$data);
        $this->assign("guild",I("account"));
        $this->assign('total',$total);
        $this->display();
    }
     /**
     * 消费统计  不加缓存
     * @author whh
     * @param  
     * @param 
     */
    public function cpa_spend(){
        $page = intval($_REQUEST['p']);
        $page = $page ? $page : 1;
        $where_str = "pay_status=1";
        if(isset($_REQUEST['promote_id'])){
                if($_REQUEST['promote_id']=='全部'){
                    unset($_REQUEST['promote_id']);
                }else if($_REQUEST['promote_id']=='自然注册'){
                    $promote_id=array("elt",0);
                    $where_str .= " and promote_id >= 0";
                    #unset($_REQUEST['promote_name']);
                }else{
                    $promote_id=$_REQUEST['promote_id'];
                    $where_str .= " and promote_id = '{$promote_id}'";
                   # unset($_REQUEST['promote_name']);
                }
        }
        if(isset($_REQUEST['game_id'])){
            if($_REQUEST['game_id']=='全部'){
                unset($_REQUEST['game_id']);
            }else{
                $game_id=$_REQUEST['game_id'];
                $where_str .= " and game_id = '{$game_id}'";
            }
            unset($_REQUEST['game_id']);
        }
        if(isset($_REQUEST['time-start'])&&isset($_REQUEST['time-end'])){
            //$pay_time =array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])));
            //unset($_REQUEST['time-start']);unset($_REQUEST['time-end']);
            $where_str .= " and pay_time between '".strtotime($_REQUEST['time-start'])."' and '".(strtotime($_REQUEST['time-end'])+24*60*60-1)."'";
        }
        if(isset($_REQUEST['start'])&&isset($_REQUEST['end'])){
            //$pay_time  =array('BETWEEN',array(strtotime($_REQUEST['start']),strtotime($_REQUEST['end'])));
            $where_str .= " and pay_time between '".strtotime($_REQUEST['start'])."' and '".(strtotime($_REQUEST['end'])+24*60*60-1)."'";
        }
       

        $row = 10;
        $size = ($page-1)*$row;
        //$key = "cpa_spend_{$page}_{$row}_".md5(json_encode($map));
        //$result = $this->_cache->get($key);
        //if(empty($result)) {
            $Model = D();
            $sql = "select sum(f1) as count, sum(f2) as ucount,game_id, promote_id from (select sum(s.pay_amount) as f1, count(DISTINCT s.user_id) as f2,s.game_id, s.promote_id from tab_spend as s where {$where_str} group by s.game_id, s.promote_id union all select sum(b.pay_amount) as f1, count(DISTINCT b.user_id) as f2,b.game_id, b.promote_id from tab_bind_spend as b where {$where_str} group by b.game_id, b.promote_id) A GROUP BY game_id, promote_id order by game_id desc limit {$size},{$row}";
            $data = $Model->query($sql);
            //print_r($data);exit;
            $countsql = "select count(DISTINCT game_id,promote_id) as count from (select sum(s.pay_amount) as f1, count(DISTINCT s.user_id) as f2, s.game_id, s.promote_id from tab_spend as s where {$where_str} group by s.game_id, s.promote_id union all select sum(b.pay_amount) as f1, count(DISTINCT b.user_id) as f2, b.game_id, b.promote_id from tab_bind_spend as b where {$where_str} group by b.game_id, b.promote_id) A";
            $num =$Model->query($countsql);
            $count=intval($num[0]['count']);
            //print_r($count);exit;
            $totalsql = "select sum(f1) as total from (select sum(s.pay_amount) as f1 from tab_spend as s where {$where_str}  union all select sum(b.pay_amount) as f1 from tab_bind_spend as b where {$where_str} ) A";
            $tot=$Model->query($totalsql);
            $total=intval($tot[0]['total']);
            $total = sprintf("%.2f",$total);
            
            $result = array('data'=>$data, 'count'=>$count,'total'=>$total);
            //$this->_cache->set($key, $result, $this->_timeout);
        //}
        $total = $result['total'];
        $data = $result['data'];
        $count = $result['count'];

        //分页
        if($count > $row){
            $page = new \Think\Page($count, $row);
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $this->assign('_page', $page->show());
        }
        $this->assign('gamename',I("game_name"));
        $this->assign("reco",I("time"));
        $this->assign("list_data",$data);
        $this->assign("guild",I("account"));
        $this->assign('total',$total);
        $this->display();
    }

     /**
     * 平台币总额统计
     * @author whh
     * @param game_id=-1时代表查询的是无效用户数据,-2时代表是空的,-3代表全部
     * @param promote_id=-2时代表是空的,-3代表是全部
     */
    public function ptb_spend()
    {   
        $map['game_id'] = array('GT','0');
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
            }
            unset($_REQUEST['game_id']);
        }
        if(isset($_REQUEST['pay_way'])){
            $map['pay_way']=$_REQUEST['pay_way'];
            unset($_REQUEST['pay_status']);
        }
        if(isset($_REQUEST['time-start'])&&isset($_REQUEST['time-end'])){
            $map['pay_time'] =array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])+24*60*60-1));
            unset($_REQUEST['time-start']);unset($_REQUEST['time-end']);
        }
        if(isset($_REQUEST['start'])&&isset($_REQUEST['end'])){
            $map['pay_time'] =array('BETWEEN',array(strtotime($_REQUEST['start']),strtotime($_REQUEST['end'])+24*60*60-1));
            unset($_REQUEST['start']);unset($_REQUEST['end']);
        }

        $row=10;
        $map['pay_status']=1;
        $page = intval($_GET['p']);
        $page = $page ? $page : 1; //默认显示第一页数据
        $model=D('Spend');
        $data=$model
        ->field('game_id,sum(pay_amount) as total_money,pay_way')
        ->group('game_id')
        ->where($map)
        ->page($page, 10)
        ->select();

        //echo $model->getLastSql();exit;
        //获取cp_name和game_name
        foreach ($data as $key => &$value) 
        {   
            $value['cp_name'] = get_cp_name($value['game_id']);
            $value['game_name'] = get_game_name($value['game_id']);
        }
        /*$count=$model
        ->field('game_id,pay_way')
        ->group('game_id')
        ->where($map)
        ->count();*/
        $have=$model
        ->field('game_id,sum(pay_amount) as total_money,pay_way')
        ->group('game_id')
        ->where($map)
        ->select();
        $count = count($have);
        //file_put_contents('/home/wwwroot/test.898sm.com/Data/bbbbb.log',$count);
        if($count > $row){
            $page = new \Think\Page($count, $row);
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $this->assign('_page', $page->show());
        }
        //print_r($data);exit;
        $this->assign('list_data', $data);
        $this->display();
    }
     /**
     * 绑币总额统计
     * @author whh
     * @param game_id=-1时代表查询的是无效用户数据,-2时代表是空的,-3代表全部
     * @param promote_id=-2时代表是空的,-3代表是全部
     */
    public function bind_spend()
    {   
        $map['game_id'] = array('GT','0');
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
            }
            unset($_REQUEST['game_id']);
        }
        if(isset($_REQUEST['time-start'])&&isset($_REQUEST['time-end'])){
            $map['pay_time'] =array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])+24*60*60-1));
            unset($_REQUEST['time-start']);unset($_REQUEST['time-end']);
        }
        if(isset($_REQUEST['start'])&&isset($_REQUEST['end'])){
            $map['pay_time'] =array('BETWEEN',array(strtotime($_REQUEST['start']),strtotime($_REQUEST['end'])+24*60*60-1));
            unset($_REQUEST['start']);unset($_REQUEST['end']);
        }

        $row=10;
        $map['pay_status']=1;
        $page = intval($_GET['p']);
        $page = $page ? $page : 1; //默认显示第一页数据
        $model=D('bindSpend');
        $data=$model
        ->field('game_id,sum(pay_amount) as total_money')
        ->group('game_id')
        ->where($map)
        ->page($page, 10)
        ->select();

        //echo $model->getLastSql();exit;
        //获取cp_name和game_name
        foreach ($data as $key => &$value) 
        {   
            $value['cp_name'] = get_cp_name($value['game_id']);
            $value['game_name'] = get_game_name($value['game_id']);
        }
        /*$count=$model
        ->field('game_id,pay_way')
        ->group('game_id')
        ->where($map)
        ->count();*/
        $have=$model
        ->field('game_id,sum(pay_amount) as total_money')
        ->group('game_id')
        ->where($map)
        ->select();
        $count = count($have);
        //file_put_contents('/home/wwwroot/test.898sm.com/Data/bbbbb.log',$count);
        if($count > $row){
            $page = new \Think\Page($count, $row);
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $this->assign('_page', $page->show());
        }
        //print_r($data);exit;
        $this->assign('list_data', $data);
        $this->display();
    }

    public function userretention(){
        if($_REQUEST['time-start']){
            $t=$_REQUEST['time-start'];
            //选中当天
            $yi=end($this->getcount($t,0));
            $this->assign("t",$t);
            //一日后
            $yiri=$this->onelogincount($t,$this->getcount($t,0),1);
            //二日后
            $erri=$this->onelogincount($t,$this->getcount($t,0),2);
            //三日后
            $sanri=$this->onelogincount($t,$this->getcount($t,0),3);
            //四日后
            $siri=$this->onelogincount($t,$this->getcount($t,0),4);
            //五日后
            $wuri=$this->onelogincount($t,$this->getcount($t,0),5);
            //六日后
            $liuri=$this->onelogincount($t,$this->getcount($t,0),6);


            //选中第二天
            $dyi=end($this->getcount($t,1));
            //一日后
            $dyiri=$this->onelogincount($t,$this->getcount($t,1),2);
            //二日后
            $derri=$this->onelogincount($t,$this->getcount($t,1),3);
            // //三日后
            $dsanri=$this->onelogincount($t,$this->getcount($t,1),4);
            // //四日后
            $dsiri=$this->onelogincount($t,$this->getcount($t,1),5);
            //  //五日后
            $dwuri=$this->onelogincount($t,$this->getcount($t,1),6);
            //  //六日后
            $dliuri=$this->onelogincount($t,$this->getcount($t,1),7);


             //选中第三天
             $syi=end($this->getcount($t,2));
             $syiri=$this->onelogincount($t,$this->getcount($t,2),3);
             //二日后
             $serri=$this->onelogincount($t,$this->getcount($t,2),4);
             //三日后
             $ssanri=$this->onelogincount($t,$this->getcount($t,2),5);
             //四日后
             $ssiri=$this->onelogincount($t,$this->getcount($t,2),6);
             //五日后
             $swuri=$this->onelogincount($t,$this->getcount($t,2),7);
             //六日后
             $sliuri=$this->onelogincount($t,$this->getcount($t,2),8);


             //选中第四天
             $fyi=end($this->getcount($t,3));
             $fyiri=$this->onelogincount($t,$this->getcount($t,3),4);
             //二日后
             $ferri=$this->onelogincount($t,$this->getcount($t,3),5);
             //三日后
             $fsanri=$this->onelogincount($t,$this->getcount($t,3),6);
             //四日后
             $fsiri=$this->onelogincount($t,$this->getcount($t,3),7);
             //五日后
             $fwuri=$this->onelogincount($t,$this->getcount($t,3),8);
             //六日后
             $fliuri=$this->onelogincount($t,$this->getcount($t,3),9);


             //选中第五天
             $wyi=end($this->getcount($t,4));
             $wyiri=$this->onelogincount($t,$this->getcount($t,4),5);
             //二日后
             $werri=$this->onelogincount($t,$this->getcount($t,4),6);
             //三日后
             $wsanri=$this->onelogincount($t,$this->getcount($t,4),7);
             //四日后
             $wsiri=$this->onelogincount($t,$this->getcount($t,4),8);
             //五日后
             $wwuri=$this->onelogincount($t,$this->getcount($t,4),9);
             //六日后
             $wliuri=$this->onelogincount($t,$this->getcount($t,4),10);


             //选中第六天
             $lyi=end($this->getcount($t,5));
             $lyiri=$this->onelogincount($t,$this->getcount($t,5),6);
             //二日后
             $lerri=$this->onelogincount($t,$this->getcount($t,5),7);
             //三日后
             $lsanri=$this->onelogincount($t,$this->getcount($t,5),8);
             //四日后
             $lsiri=$this->onelogincount($t,$this->getcount($t,5),9);
             //五日后
             $lwuri=$this->onelogincount($t,$this->getcount($t,5),10);
             //六日后
             $lliuri=$this->onelogincount($t,$this->getcount($t,5),11);


             //选中第7天
             $qyi=end($this->getcount($t,6));
             $qyiri=$this->onelogincount($t,$this->getcount($t,5),7);
             //二日后
             $qerri=$this->onelogincount($t,$this->getcount($t,5),8);
             //三日后
             $qsanri=$this->onelogincount($t,$this->getcount($t,5),9);
             //四日后
             $qsiri=$this->onelogincount($t,$this->getcount($t,5),10);
             //五日后
             $qwuri=$this->onelogincount($t,$this->getcount($t,5),11);
             //六日后
             $qliuri=$this->onelogincount($t,$this->getcount($t,5),12);



            $onelist=array( "yicount"=>$yi, "yiri"=>$yiri, "erri"=>$erri,"sanri"=>$sanri,"siri"=>$siri,"wuri"=>$wuri,"liuri"=>$liuri);
            $towlist=array( "dyicount"=>$dyi, "dyiri"=>$dyiri, "derri"=>$derri,"dsanri"=>$dsanri,"dsiri"=>$dsiri,"dwuri"=>$dwuri,"dliuri"=>$dliuri);
            $threelist=array( "syicount"=>$syi, "syiri"=>$syiri, "serri"=>$serri,"ssanri"=>$ssanri,"ssiri"=>$ssiri,"swuri"=>$swuri,"sliuri"=>$sliuri);
            $fourlist=array( "fyicount"=>$fyi, "fyiri"=>$fyiri, "ferri"=>$ferri,"fsanri"=>$fsanri,"fsiri"=>$fsiri,"fwuri"=>$fwuri,"fliuri"=>$fliuri);
            $fivelist=array( "wyicount"=>$wyi, "wyiri"=>$wyiri, "werri"=>$werri,"wsanri"=>$wsanri,"wsiri"=>$wsiri,"wwuri"=>$wwuri,"wliuri"=>$wliuri);
            $sixlist=array( "lyicount"=>$lyi, "lyiri"=>$lyiri,"lerri"=>$lerri,"lsanri"=>$lsanri,"lsiri"=>$lsiri,"lwuri"=>$lwuri,"lliuri"=>$lliuri);
            $sevenlist=array("qyicount"=>$qyi,"qyiri"=>$qyiri,"qerri"=>$qerri,"qsanri"=>$qsanri,'qsiri'=>$qsiri,'qwuri'=>$qwuri,'qliuri'=>$qliuri);

            $this->assign("four",$fourlist);
            $this->assign("tow",$towlist);
            $this->assign("one",$onelist);
            $this->assign("three",$threelist);
            $this->assign("five",$fivelist);
            $this->assign("six",$sixlist);
            $this->assign("seven",$sevenlist);

    }else{
            //6天前
            //一日后
            $yi=end($this->getcount(6));
            $yiri=$this->onelogincount(5,$this->getcount(6));
            //二日后
            $erri=$this->onelogincount(4,$this->getcount(6));
             //三日后
            $sanri=$this->onelogincount(3,$this->getcount(6));
            //四日后
            $siri=$this->onelogincount(2,$this->getcount(6));
             //五日后
            $wuri=$this->onelogincount(1,$this->getcount(6));
            //六日后
            $liuri=$this->onelogincount(0,$this->getcount(6));


            //5天前
             //一日后
            $dyi=end($this->getcount(5));
            $dyiri=$this->onelogincount(4,$this->getcount(5));
            //二日后
            $derri=$this->onelogincount(3,$this->getcount(5));
            //三日后
            $dsanri=$this->onelogincount(2,$this->getcount(5));
            //四日后
            $dsiri=$this->onelogincount(1,$this->getcount(5));
             //五日后
             $dwuri=$this->onelogincount(0,$this->getcount(5));

             //4天前
             //一日后
             $syi=end($this->getcount(4));
             $syiri=$this->onelogincount(3,$this->getcount(4));
             //二日后
             $serri=$this->onelogincount(2,$this->getcount(4));
             //三日后
             $ssanri=$this->onelogincount(1,$this->getcount(4));
             //四日后
             $ssiri=$this->onelogincount(0,$this->getcount(4));

             //3天前
             //一日后
             $fyi=end($this->getcount(3));
             $fyiri=$this->onelogincount(2,$this->getcount(3));
             //二日后
             $ferri=$this->onelogincount(1,$this->getcount(3));
             //三日后
             $fsanri=$this->onelogincount(0,$this->getcount(3));

             //2天前
             //一日后
             $wyi=end($this->getcount(2));
             $wyiri=$this->onelogincount(1,$this->getcount(2));
             //二日后
             $werri=$this->onelogincount(0,$this->getcount(2));

             //1天前
             //一日后
             $lyi=end($this->getcount(1));
             $lyiri=$this->onelogincount(0,$this->getcount(1));
             //当天
             //一日后
             $nowyi=end($this->getcount(0));
             // $lyiri=$this->onelogincount(0,$this->getcount(1));


            $onelist=array( "yicount"=>$yi, "yiri"=>$yiri, "erri"=>$erri,"sanri"=>$sanri,"siri"=>$siri,"wuri"=>$wuri,"liuri"=>$liuri);
            $towlist=array( "dyicount"=>$dyi, "dyiri"=>$dyiri, "derri"=>$derri,"dsanri"=>$dsanri,"dsiri"=>$dsiri,"dwuri"=>$dwuri);
            $threelist=array( "syicount"=>$syi, "syiri"=>$syiri, "serri"=>$serri,"ssanri"=>$ssanri,"ssiri"=>$ssiri);
            $fourlist=array( "fyicount"=>$fyi, "fyiri"=>$fyiri, "ferri"=>$ferri,"fsanri"=>$fsanri);
            $fivelist=array( "wyicount"=>$wyi, "wyiri"=>$wyiri, "werri"=>$werri);
            $sixlist=array( "lyicount"=>$lyi, "lyiri"=>$lyiri);
            $this->assign("one",$onelist);
            $this->assign("tow",$towlist);
            $this->assign("three",$threelist);
            $this->assign("four",$fourlist);
            $this->assign("five",$fivelist);
            $this->assign("six",$sixlist);
            $this->assign("nowyi",$nowyi);
    }
    $this->display();
}
    //根据时间计算注册人数
    public function getcount($day,$n=null){
        if(null!==$n){
            $map['register_time']=get_start_end_time($day,$n);
        }else{
            $map=get_last_day_time($day,"register_time");
        }
        if(isset($_REQUEST['promote_name'])){
            if($_REQUEST['promote_name']=='全部'){
                unset($_REQUEST['promote_name']);
            }else if($_REQUEST['promote_name']=='自然注册'){
                $map['tab_user.promote_id']=array("elt",0);
                unset($_REQUEST['promote_name']);
            }else{
                $map['tab_user.promote_id']=get_promote_id($_REQUEST['promote_name']);
                unset($_REQUEST['promote_name']);
            }
        }
        if(isset($_REQUEST['game_name'])){
            if($_REQUEST['game_name']=='全部'){
                unset($_REQUEST['game_name']);
            }
            else{
                $map['game_id']=get_game_id($_REQUEST['game_name']);
                $r_user_id=D('User')
                ->join('tab_user_play ON tab_user.id=tab_user_play.user_id')
                ->where($map)
                ->select();
                for ($i=0; $i <count($r_user_id); $i++) {
                    $sd[]=$r_user_id[$i]['user_id'];
                }
                $pid=implode(",",$sd);
                $count=D("User")
                ->join("tab_user_play on tab_user.id=tab_user_play.user_id")
                ->where($map)
                ->count();
            }
        }else{
            $r_user_id=D("User")->where($map)->select();
            for ($i=0; $i <count($r_user_id); $i++) {
                $sd[]=$r_user_id[$i]['id'];
            }
            $pid=implode(",",$sd);
            $count=D("User")->where($map)->count();
        }
        $r_count=array($pid,$count);
        return $r_count;
    }
    //计算留存率
    public function onelogincount($day,$count,$n=null){
        if(null!==$n){
        $onetime['login_time']=get_start_end_time($day,$n);
        }else{
        $onetime=get_last_day_time($day,"login_time");
        }
        if(isset($_REQUEST['promote_name'])){
            if($_REQUEST['promote_name']=='全部'){
                unset($_REQUEST['promote_name']);
            }else if($_REQUEST['promote_name']=='自然注册'){
                $onetime['promote_id']=array("elt",0);
                unset($_REQUEST['promote_name']);
            }else{
                $onetime['promote_id']=get_promote_id($_REQUEST['promote_name']);
                unset($_REQUEST['promote_name']);
            }
        }
        if(isset($_REQUEST['game_name'])){
            if($_REQUEST['game_name']=='全部'){
                unset($_REQUEST['game_name']);
            }
            else{
                $onetime['game_id']=get_game_id($_REQUEST['game_name']);
                $onetime['user_id']=array('in',(string)$count[0]);
                $onelogincount=M("user_login_record","tab_")->where($onetime)->count();
            }
        }else{
          $onetime['user_id']=array('in',(string)$count[0]);
          $onelogincount=M("user_login_record","tab_")->where($onetime)->count();
        }
        if($onelogincount!=0){
            if($count[1]==0){
                $baifen='';
            }else{
              $lu=$onelogincount/$count[1];
              $baifen=$lu*100;
              $baifen=$baifen>100?'100%':$baifen.'%';
            }
        }else{
            if($count[1]==0){
                $baifen="";
            }elseif($count[1]!=0){
                $baifen="0%";
            }

        }
        return round($baifen,2).'%';
    }
    public function userarpu()
    {
        $allcount=$this->getallcount()>0?$this->getallcount():0;
        $newcount=end($this->getnewcount($_REQUEST['time-start']))>0?end($this->getnewcount($_REQUEST['time-start'])):0;
        if(!isset($_REQUEST['time-start'])){
            $cicount=0;
        }else{
            $cicount=$this->getplaycount($_REQUEST['time-start'],$this->getnewcount($_REQUEST['time-start']),1);
        }
        $paycount=$this->getpaycount();
        $allpay=$this->getallpaycount();
        $newpaycount=$this->getnewpaycount();
        $cilogin = $this->get_cilogin($newcount,$cicount);
        $allrate=$this->getrate();
        $userarpu=$this->getuserarpu();
        $payarpu=$this->getpayarpu();
        $this->assign("allcount",$allcount);
        $this->assign("newcount",$newcount);
        $this->assign("cicount",$cicount);
        $this->assign("paycount",$paycount);
        $this->assign("newpaycount",$newpaycount);
        $this->assign("allpay",$allpay);
        $this->assign("allrate",$allrate);
        $this->assign("userarpu",$userarpu);
        $this->assign('cilogin',$cilogin);
        $this->assign("payarpu",$payarpu);
        $this->assign("game_name",$_REQUEST['game_name']);


        $this->display();
    }
    //计算留存数
    public function getplaycount($day,$count,$n=null){
        if(null!==$n){
        $onetime['login_time']=get_start_end_time($day,$n);
        }else{
        $onetime=get_last_day_time($day,"login_time");
        }
        if(isset($_REQUEST['promote_name'])){
            if($_REQUEST['promote_name']=='全部'){
                unset($_REQUEST['promote_name']);
            }else if($_REQUEST['promote_name']=='自然注册'){
                $map['promote_id']=array("elt",0);
                unset($_REQUEST['promote_name']);
            }else{
                $map['promote_id']=get_promote_id($_REQUEST['promote_name']);
                unset($_REQUEST['promote_name']);
            }
        }
        if(isset($_REQUEST['game_name'])){
            if($_REQUEST['game_name']=='全部'){
                unset($_REQUEST['game_name']);
            }else{
                $onetime['game_id']=get_game_id($_REQUEST['game_name']);
                $onetime['user_id']=array('in',(string)$count[0]);
                $onelogincount=M("user_login_record","tab_")->where($onetime)->count();
            }
        }else{
          $onetime['user_id']=array('in',(string)$count[0]);
          $onelogincount=M("user_login_record","tab_")->where($onetime)->count();
        }
        return $onelogincount;
    }
    //计算指定日期新用户数
    public function getnewcount($time){
        $map=array();
        if(!empty($time)){
            $map['register_time']=get_start_end_time($_REQUEST['time-start']);
            if(isset($_REQUEST['promote_name'])){
                if($_REQUEST['promote_name']=='全部'){
                    unset($_REQUEST['promote_name']);
                }else if($_REQUEST['promote_name']=='自然注册'){
                    $map['promote_id']=array("elt",0);
                    unset($_REQUEST['promote_name']);
                }else{
                    $map['promote_id']=get_promote_id($_REQUEST['promote_name']);
                    unset($_REQUEST['promote_name']);
                }
            }
        }else{
            $map['register_time']=-1;
        }
        if(isset($_REQUEST['game_name'])){
            if($_REQUEST['game_name']=='全部'){
                unset($_REQUEST['game_name']);
            }else{
                $map['game_id']=get_game_id($_REQUEST['game_name']);
                if(isset($_REQUEST['promote_name'])){
                    if($_REQUEST['promote_name']=='全部'){
                        unset($_REQUEST['promote_name']);
                    }else if($_REQUEST['promote_name']=='自然注册'){
                        $map['a.promote_id']=array("elt",0);
                        unset($_REQUEST['promote_name']);
                    }else{
                        $map['a.promote_id']=get_promote_id($_REQUEST['promote_name']);
                        unset($_REQUEST['promote_name']);
                    }
                }
            }
        }
        if(!empty($map)){
        $r_user_id=M("User as a","tab_")
            ->join("tab_user_play as b on a.id=b.user_id")
            ->where($map)
            ->select();
            for ($i=0; $i <count($r_user_id); $i++) {
                $sd[]=$r_user_id[$i]['user_id'];
            }
        $pid=implode(",",$sd);
        $count=M("User as a","tab_")
            ->field("count(*) as count")
            ->join("tab_user_play as b on a.id=b.user_id")
            ->where($map)
            ->count();
        }else{
            $count=0;
        }
        $count=array($pid,$count);
        return $count;

    }
    //计算次日留存率
    function get_cilogin($newcount,$cicount){
        if($cicount==0){
            return 0;
        }else{
            return round($cicount/$newcount*100).'%';
        }
    }
    //计算指定游戏用户总数
    public function getallcount(){
        $map=array();
        if(isset($_REQUEST['game_name'])){
            if($_REQUEST['game_name']=='全部'){
                unset($_REQUEST['game_name']);
            }else{
                $map['game_id']=get_game_id($_REQUEST['game_name']);
                if(isset($_REQUEST['promote_name'])){
                    if($_REQUEST['promote_name']=='全部'){
                        unset($_REQUEST['promote_name']);
                    }else if($_REQUEST['promote_name']=='自然注册'){
                        $map['a.promote_id']=array("elt",0);
                        unset($_REQUEST['promote_name']);
                    }else{
                        $map['a.promote_id']=get_promote_id($_REQUEST['promote_name']);
                        unset($_REQUEST['promote_name']);
                    }
                }
            }
        }
        if(isset($_REQUEST['time-start'])){
            $map['register_time']=get_start_end_time($_REQUEST['time-start']);
            if(isset($_REQUEST['promote_name'])){
                if($_REQUEST['promote_name']=='全部'){
                    unset($_REQUEST['promote_name']);
                }else if($_REQUEST['promote_name']=='自然注册'){
                    $map['a.promote_id']=array("elt",0);
                    unset($_REQUEST['promote_name']);
                }else{
                    $map['a.promote_id']=get_promote_id($_REQUEST['promote_name']);
                    unset($_REQUEST['promote_name']);
                }
            }
        }
        if(!empty($map)){
        $count=M("User as a","tab_")
            ->field("count(*) as count")
            ->join("tab_user_play as b on a.id=b.user_id")
            ->where($map)
            ->count();
        }else{
            $count=0;
        }
        return $count;
    }
    // 计算活跃用户数(当前日期所在一周的时间)
    public function gethuocount(){
        $count=0;
        if(isset($_REQUEST['game_name'])||isset($_REQUEST['time-start'])){
            if(isset($_REQUEST['game_name'])){
                if($_REQUEST['game_name']=='全部'){
                    unset($_REQUEST['game_name']);
                }else{
                    $map['game_id']=$_REQUEST['game_id'];
                    $time=date("Y-m-d",time());
                    $start=strtotime("$time - 6 days");
                    //周末
                    $end=strtotime("$time");
                    $map['login_time']=array("between",array($start,$end));
                }
            }
            if(isset($_REQUEST['time-start'])){
                $time2=$_REQUEST['time-start'];
                $start2=strtotime("$time2 - 6 days");
                //周末
                $end2=strtotime("$time2");
                $map['login_time']=array("between",array($start2,$end2));
            }
            $data=M("user_login_record","tab_")
            ->group('user_id')
            ->having('count(user_id) > 2')
            ->where($map)
            ->select();
            $count=count($data);
        }
        return $count;
    }
    //计算付费用户数
    public function getpaycount()
    {

        $count=0;
        if(isset($_REQUEST['game_name'])){
            if($_REQUEST['game_name']=='全部'){
                unset($_REQUEST['game_name']);
            }else{
                $map['game_id']=get_game_id($_REQUEST['game_name']);
                $map['pay_status']=1;
                if(isset($_REQUEST['promote_name'])){
                    if($_REQUEST['promote_name']=='全部'){
                        unset($_REQUEST['promote_name']);
                    }else if($_REQUEST['promote_name']=='自然注册'){
                        $map['promote_id']=array("elt",0);
                        unset($_REQUEST['promote_name']);
                    }else{
                        $map['promote_id']=get_promote_id($_REQUEST['promote_name']);
                        unset($_REQUEST['promote_name']);
                    }
                }
            }
        }
        if(isset($_REQUEST['time-start'])){
            $map['pay_time']=array("lt",strtotime($_REQUEST['time-start'])+(60*60*24));
            if(isset($_REQUEST['promote_name'])){
                if($_REQUEST['promote_name']=='全部'){
                    unset($_REQUEST['promote_name']);
                }else if($_REQUEST['promote_name']=='自然注册'){
                    $map['promote_id']=array("elt",0);
                    unset($_REQUEST['promote_name']);
                }else{
                    $map['promote_id']=get_promote_id($_REQUEST['promote_name']);
                    unset($_REQUEST['promote_name']);
                }
            }
        }
        if(!empty($map)){
        $count=M("spend","tab_")
            ->where($map)
            ->count();
        }else{
            $count=0;
        }
        return $count;

    }
    //计算新用户付费金额
    public function getnewpaycount()
    {
         $count=0;
        if(isset($_REQUEST['time-start'])&&isset($_REQUEST['game_name'])){
            if($_REQUEST['game_name']=='全部'){
                unset($_REQUEST['game_name']);
            }else{
             $map['c.game_id']=get_game_id($_REQUEST['game_name']);
             $map['pay_status']=1;
             $map['register_time']=get_start_end_time($_REQUEST['time-start']);
            }
        }
        if(!empty($map)){
           $list=M("User as a","tab_")
           ->field("sum(pay_amount) as sum")
           ->join("tab_user_play as b on a.id = b.user_id")
           ->join("tab_spend as c on c.game_id=b.game_id")
            ->where($map)
            ->find();
                if(!empty($list['sum'])){
                $count=$list['sum'];
            }
        }
        return $count;
    }
    //计算总付费金额
    public function getallpaycount()
    {
        $count=0;
         if(isset($_REQUEST['game_name'])){
            if($_REQUEST['game_name']=='全部'){
                unset($_REQUEST['game_name']);
            }
            else{
                $map['game_id']=get_game_id($_REQUEST['game_name']);
                $map['pay_status']=1;
                $list=M("spend","tab_")
                ->field("sum(pay_amount) as sum")
                ->where($map)
                ->find();
                if(!empty($list['sum'])){
                    $count=$list['sum'];
                }
            }
        }
        return $count;
    }
    //计算总付费率
    public function getrate()
    {
        $pr=$this->getpaycount();
        $all=$this->getallcount();
        $count=$pr/$all;
        $count=$count>1?100:$count*100;
        return $count;
    }
    //获取用户ARPU
    public function getuserarpu()
    {
        $new=$this->getnewpaycount();
        if(isset($_REQUEST['time-start'])){
        $newcount=end($this->getnewcount($_REQUEST['time-start']));
        $count=$new/$newcount;
        }else{
          $count=0;
        }
        return $count;

    }
    //获取活跃ARPU
    public function gethuoarpu()
    {
        if(isset($_REQUEST['game_name'])||isset($_REQUEST['time-start'])){
            if(isset($_REQUEST['game_name'])){
                if($_REQUEST['game_name']=='全部'){
                    unset($_REQUEST['game_name']);
                }
                else{
                    $map['tab_user_login_record.game_id']=get_game_id($_REQUEST['game_name']);
                    $time=date("Y-m-d",time());
                    $start=strtotime("$time - 6 days");
                    //周末
                    $end=strtotime("$time");
                    $map['login_time']=array("between",array($start,$end));
                }
            }
            if(isset($_REQUEST['time-start'])){
                $time2=$_REQUEST['time-start'];
                $start2=strtotime("$time2 - 6 days");
                //周末
                $end2=strtotime("$time2");
                $map['login_time']=array("between",array($start2,$end2));
            }
            $data=M("user_login_record","tab_")
            ->group('user_id')
            ->having('count(user_id) > 2')
            ->where($map)
            ->select();
            foreach ($data as $key => $value) {
                $data1[]=$value['user_id'];
            }
            foreach ($data1 as $value) {
               $user_account[]=get_user_account($value);
            }
            $pid=implode(',',$user_account);
        }
            $map['user_account']=array('in',$pid);
            if($pid!=''){
            $huosum=M("spend ","tab_")
            ->distinct(true)
            ->field("pay_amount")
            ->join("tab_user_login_record on tab_spend.game_id = tab_user_login_record.game_id")
            ->where($map)
            ->select();
            foreach ($huosum as $value) {
                $huosum2[]=$value['pay_amount'];
            }
            $sum=array_sum($huosum2);
            // if($pid!=null&&$huosum/!='')
            $count=count($data);
            $return= $sum/$count;
            }else{
                $return =0;
            }

            return $return;

    }
    //获取付费ARPU
    public function getpayarpu()
    {
        $paysum=$this->getallpaycount();
        $paycount=$this->getpaycount();
        if($paycount!=0){
        $count=$paysum/$paycount;
        }else{
            $count=0;
        }
        return $count;
    }
    //游戏下载统计数据展示
    public function dow_record(){
        if(isset($_REQUEST['game_id'])){
                if($_REQUEST['game_id']=='全部游戏'){
                    unset($_REQUEST['game_id']);
                }else{
                    $map['game_id']=$_REQUEST['game_id'];
                    unset($_REQUEST['game_id']);
                }
            }

            if(isset($_REQUEST['promote_id'])){
                if($_REQUEST['promote_id']=='全部渠道'){
                    unset($_REQUEST['promote_id']);
                }else if($_REQUEST['promote_id']=='自然注册'){
                    $map['promote_id']=array("lte",0);

                    unset($_REQUEST['promote_id']);
                }else{
                    $map['promote_id']=$_REQUEST['promote_id'];
                    unset($_REQUEST['promote_name']);
                }
            }

            if(isset($_REQUEST['down_way'])){
                $map['down_way']=$_REQUEST['down_way'];
                unset($_REQUEST['down_way']);
            }

            if(isset($_REQUEST['user_account'])){
                $map['user_account']=array('like','%'.$_REQUEST['user_account'].'%');
                unset($_REQUEST['user_account']);
            }

            if(isset($_REQUEST['time-start'])&&isset($_REQUEST['time-end'])){
                $map['create_time']=array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])));
                unset($_REQUEST['time-start']);unset($_REQUEST['time_end']);
            }
             if(isset($_REQUEST['start']) && isset($_REQUEST['end'])){
                $map['create_time'] = array('BETWEEN',array(strtotime($_REQUEST['start']),strtotime($_REQUEST['end'])));
                #unset($_REQUEST['start']);unset($_REQUEST['end']);
            }
           /* if(isset($_REQUEST['start'])&&isset($_REQUEST['end'])){
                $map['create_time']=array('BETWEEN',array(strtotime($_REQUEST['start']),strtotime($_REQUEST['end'])+24*60*60-1));
                unset($_REQUEST['start']);unset($_REQUEST['end']);
             }*/
            $row=10;
            $page = intval($_GET['p']);
            $page = $page ? $page : 1; //默认显示第一页数据
            $model=M('down_record','tab_');
            $data=$model
            ->where($map)
            ->order('id desc')
            ->page($page, 10)
            ->select();
            //$sql=$model->getLastSql();
            //print_r($sql);exit;
            $count=$model
            ->where($map)
            ->count();
            if($count > $row){
                $page = new \Think\Page($count, $row);
                $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
                $this->assign('_page', $page->show());
            }
            //print_r($data);exit;
            $this->assign('count', $count);
            $this->assign('_list', $data);
            $this->display();

    }
    /**
     * 登陆统计
     * @param $time_start 开始时间
     * @param $time_start 结束时间
     * @param $game_id 游戏id -1:无效用户 -2：请选择游戏 -3：全部
     * @param $promote_id 渠道id 0：自然注册 -3全部 -2：请选择所属渠道
     */
    public function cpa_login()
    {
        $p = (int)I('get.p')?(int)I('get.p'):1;
        $size =  (int)I('get.size')?(int)I('get.size'):15;
        if(isset($_REQUEST['time_start'])&&isset($_REQUEST['time_end']))
        {
            $map['l.login_time']=array('BETWEEN',array(strtotime($_REQUEST['time_start']),strtotime($_REQUEST['time_end'])+24*60*60-1));
            //$map['l.login_time']=array('BETWEEN',array(strtotime($_REQUEST['time_start']),strtotime($_REQUEST['time_end'])));
        }else
        {
            $beginYesterday=mktime(0,0,0,date('m'),date('d')-1,date('Y'));
            $endYesterday=mktime(0,0,0,date('m'),date('d'),date('Y'))-1;
            $map['l.login_time'] = array('between',array($beginYesterday,$endYesterday));
        }
        unset($_REQUEST['time-start']);unset($_REQUEST['time_end']);
        if(isset($_REQUEST['game_id']))
        {
            $game_id = I('game_id');
        }
        if(isset($_REQUEST['promote_id']))
        {
            $promote_id = I('promote_id');
        }
        //每个渠道所有游戏的总的登陆数
        if($game_id == -2 && $promote_id == -3 )
        {
            $field = 'count(distinct(l.user_id)) num,u.promote_id';
            $group = 'u.promote_id';
        //每个游戏所有渠道总的登陆数
        }else if($game_id == -3 && $promote_id == -2 )
        {
            $field = 'l.game_id game_name,count(distinct(l.user_id)) num';
            $group = 'l.game_id';

        }
        //某个游戏所有的登陆数
        else if($game_id > -1 && $promote_id == -2)
        {
            $field = 'l.game_id game_name,count(distinct(l.user_id)) num';
            $group = 'l.game_id';
            $map['l.game_id'] = $game_id;

        }
        //某个游戏每个渠道的登陆数
        else if($game_id > -1 && $promote_id == -3)
        {
            $field = 'l.game_id game_name,count(distinct(l.user_id)) num,u.promote_id,u.promote_account';
            $group = 'l.game_id,u.promote_id';
            $map['l.game_id'] = $game_id;
        }
        //某个渠道所有游戏总的登陆数
        else if($promote_id > -1 && $game_id < -1)
        {
            $field = 'count(distinct(l.user_id)) num,u.promote_id';
            $group = 'u.promote_id';
            $map['u.promote_id'] = $promote_id;
        //某个游戏某个渠道的登陆数
        }else if($game_id > -1 && $promote_id > -1)
        {
            $field = 'l.game_id game_name,count(distinct(l.user_id)) num,u.promote_id';
            $group = 'l.game_id,u.promote_id';
            $map['u.promote_id'] = $promote_id;
            $map['l.game_id'] = $game_id;
        }
        //默认显示所有游戏所有渠道登陆数
        else
        {
            $field = 'l.game_id game_name,count(distinct(l.user_id)) num,u.promote_id,u.promote_account';
            $group = 'l.game_id,u.promote_id';
        }
        $list_data = M('User_login_record','tab_')
              ->alias('l')
              ->field($field)
              ->join('tab_user u on l.user_id = u.id')
              ->where($map)
              ->group($group)
              ->page($p,$size)
              ->select();
        //将游戏id转换成游戏名字，零为官网登陆
        foreach ($list_data as $key => &$value) {
            if($value['game_name'] == 0)
            {
                $value['game_name'] = '官网';
            }else
            {
                $value['game_name'] = get_game_name($value['game_name']);
            }
            if(empty(trim($value['promote_account'])))
            {
                $value['promote_account'] = get_promote_account_by_id($value['promote_id']);
            }

        }
        $count_data = M('User_login_record','tab_')
              ->alias('l')
              ->field($field)
              ->join('tab_user u on l.user_id = u.id')
              ->where($map)
              ->group($group)
              ->select();

        $count = count($count_data);
        if($count > $size){
            $page = new \Think\Page($count, $size);
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $this->assign('_page', $page->show());
            }
        $this->assign('list_data',$list_data);
        $this->display();

    }


}

