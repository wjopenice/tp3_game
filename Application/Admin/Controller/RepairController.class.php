<?php

namespace Admin\Controller;
use User\Api\UserApi as UserApi;
use Common\Api\GameApi;
/**
 * 后台首页控制器
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
class RepairController extends ThinkController {
    
    /**
    *补单列表
    */
    public function repairList($value='')
    {
        if(isset($_REQUEST['user_account'])){
            $map['user_account']=array('like','%'.$_REQUEST['user_account'].'%');
            unset($_REQUEST['user_account']);
        }
        if(isset($_REQUEST['time-start'])&&isset($_REQUEST['time-end'])){
            $map['pay_time'] =array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])+24*60*60-1));
            unset($_REQUEST['time-start']);unset($_REQUEST['time-end']);
        }
        if(isset($_REQUEST['start'])&&isset($_REQUEST['end'])){
            $map['pay_time'] =array('BETWEEN',array(strtotime($_REQUEST['start']),strtotime($_REQUEST['end'])+24*60*60-1));
            unset($_REQUEST['start']);unset($_REQUEST['end']);
        }
        if(isset($_REQUEST['game_id'])){
            if($_REQUEST['game_id']=='全部'){
                unset($_REQUEST['game_id']);
            }else{
                $map['game_id']=$_REQUEST['game_id'];
                unset($_REQUEST['game_id']);
            }
        }
        if(isset($_REQUEST['pay_way'])){
            $map['pay_way']=$_REQUEST['pay_way'];
            unset($_REQUEST['pay_status']);
        }
        if(isset($_REQUEST['pay_order_number'])){
            $map['pay_order_number']=array('like','%'.$_REQUEST['pay_order_number'].'%');
            unset($_REQUEST['pay_order_number']);
        }
        $map["pay_status"] = 1;
        $map["pay_game_status"] = 0;
        $row=15;
        $data = M('spend','tab_')
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

        $count =M('spend','tab_')->where($map)->count();
        //print_r($count);exit;
         //分页
        if($count > $row){
            $page = new \Think\Page($count, $row);
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $this->assign('_page', $page->show());
        }
        //$list = M("spend","tab_")->where($map)->select();
        $this->assign("list_data",$data);
        $this->display();
    }
    public function repairBindlist($value='')
    {
        if(isset($_REQUEST['user_account'])){
            $map['user_account']=array('like','%'.$_REQUEST['user_account'].'%');
            unset($_REQUEST['user_account']);
        }
        if(isset($_REQUEST['time-start'])&&isset($_REQUEST['time-end'])){
            $map['pay_time'] =array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])+24*60*60-1));
            unset($_REQUEST['time-start']);unset($_REQUEST['time-end']);
        }
        if(isset($_REQUEST['start'])&&isset($_REQUEST['end'])){
            $map['pay_time'] =array('BETWEEN',array(strtotime($_REQUEST['start']),strtotime($_REQUEST['end'])+24*60*60-1));
            unset($_REQUEST['start']);unset($_REQUEST['end']);
        }
        if(isset($_REQUEST['game_id'])){
            if($_REQUEST['game_id']=='全部'){
                unset($_REQUEST['game_id']);
            }else{
                $map['game_id']=$_REQUEST['game_id'];
                unset($_REQUEST['game_id']);
            }
        }
        
        if(isset($_REQUEST['pay_order_number'])){
            $map['pay_order_number']=array('like','%'.$_REQUEST['pay_order_number'].'%');
            unset($_REQUEST['pay_order_number']);
        }
        $map["pay_status"] = 1;
        $map["pay_game_status"] = 0;
        $row=15;
        $data = M('bind_spend','tab_')
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

        $count =M('bind_spend','tab_')->where($map)->count();
        //print_r($count);exit;
         //分页
        if($count > $row){
            $page = new \Think\Page($count, $row);
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $this->assign('_page', $page->show());
        }

        //$list = M("bind_spend","tab_")->where($map)->select();
        $this->assign("list_data",$data);
        $this->display();
    }
    /**
    *编辑补单
    */
    public function repairEdit($orderNo=null){
        $param['out_trade_no'] = $orderNo;
        $game = new GameApi();
        $game->game_pay_notify($param,1);
        $this->repairAdd($orderNo);
        $this->success('补单成功',U('repairList'));
        //$this->display();
    }
    /**
    *编辑绑币补单
    */
    public function repairBindEdit($orderNo=null){
        $param['out_trade_no'] = $orderNo;
        $param['distinction'] = 'bind';
        $game = new GameApi();
        $game->game_pay_notify($param,2);
        $this->repairAdd($param);
        $this->success('补单成功',U('repairBindlist'));
        //$this->display();
    }
    /***
    *添加补单记录
    */
    protected function repairAdd($orderNo=null){
        if(is_array($orderNo)){
            $map['pay_order_number'] = $orderNo['out_trade_no'];
            $dis_pf='bind_PF';
            $pay_data = M("Bind_spend","tab_")->where($map)->find();
        }else{
            $map['pay_order_number'] = $orderNo;
            $pay_data = M("Spend","tab_")->where($map)->find();
        }
        if(!empty($pay_data)){
            M("RepairRecord","tab_")->add(
                array(
                    "pay_order_number"=>$pay_data['pay_order_number'],
                    "user_id"=>$pay_data['user_id'],
                    "user_account"=>$pay_data['user_account'],
                    "user_nickname"=>$pay_data['user_nickname'],
                    "game_id"=>$pay_data['game_id'],
                    "game_appid"=>$pay_data['game_appid'],
                    "game_name"=>$pay_data['game_name'],
                    "op_id"=>session("user_auth.uid"),
                    "op_nickname"=>session("user_auth.username"),
                    "create_time"=>NOW_TIME,
                    'dis_pf'=>$dis_pf,
                )
            );
        }
    }

    /**
    *补单记录列表
    */
    public function repairRecordList(){
        if(isset($_REQUEST['user_account'])){
            $map['user_account']=array('like','%'.$_REQUEST['user_account'].'%');
            unset($_REQUEST['user_account']);
        }
        if(isset($_REQUEST['time-start'])&&isset($_REQUEST['time-end'])){
            $map['create_time'] =array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])+24*60*60-1));
            unset($_REQUEST['time-start']);unset($_REQUEST['time-end']);
        }
        if(isset($_REQUEST['start'])&&isset($_REQUEST['end'])){
            $map['create_time'] =array('BETWEEN',array(strtotime($_REQUEST['start']),strtotime($_REQUEST['end'])+24*60*60-1));
            unset($_REQUEST['start']);unset($_REQUEST['end']);
        }
        if(isset($_REQUEST['game_id'])){
            if($_REQUEST['game_id']=='全部'){
                unset($_REQUEST['game_id']);
            }else{
                $map['game_id']=$_REQUEST['game_id'];
                unset($_REQUEST['game_id']);
            }
        }
        
        if(isset($_REQUEST['pay_order_number'])){
            $map['pay_order_number']=array('like','%'.$_REQUEST['pay_order_number'].'%');
            unset($_REQUEST['pay_order_number']);
        }
        $row=15;
        $map['dis_pf']=0;
        $data = M('repair_record','tab_')
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

        $count =M('repair_record','tab_')->where($map)->count();
        //print_r($count);exit;
         //分页
        if($count > $row){
            $page = new \Think\Page($count, $row);
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $this->assign('_page', $page->show());
        }
        //$list = M("repair_record","tab_")->where($map)->select();
        $this->assign("list_data",$data);
        $this->display();
    }
    /**
    *补单记录列表
    */
    public function repairBindRecordList(){
        if(isset($_REQUEST['user_account'])){
            $map['user_account']=array('like','%'.$_REQUEST['user_account'].'%');
            unset($_REQUEST['user_account']);
        }
        if(isset($_REQUEST['time-start'])&&isset($_REQUEST['time-end'])){
            $map['create_time'] =array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])+24*60*60-1));
            unset($_REQUEST['time-start']);unset($_REQUEST['time-end']);
        }
        if(isset($_REQUEST['start'])&&isset($_REQUEST['end'])){
            $map['create_time'] =array('BETWEEN',array(strtotime($_REQUEST['start']),strtotime($_REQUEST['end'])+24*60*60-1));
            unset($_REQUEST['start']);unset($_REQUEST['end']);
        }
        if(isset($_REQUEST['game_name'])){
            if($_REQUEST['game_name']=='全部'){
                unset($_REQUEST['game_name']);
            }else{
                $map['game_name']=$_REQUEST['game_name'];
                unset($_REQUEST['game_name']);
            }
        }
        
        if(isset($_REQUEST['pay_order_number'])){
            $map['pay_order_number']=array('like','%'.$_REQUEST['pay_order_number'].'%');
            unset($_REQUEST['pay_order_number']);
        }
        $map['dis_pf']=1;
        $list = M("repair_record","tab_")->where($map)->select();
        $this->assign("list_data",$list);
        $this->display();
    }
}
