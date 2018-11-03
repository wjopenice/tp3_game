<?php

namespace Admin\Controller;
use User\Api\UserApi as UserApi;

/**
 * 后台首页控制器
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
class SpendController extends ThinkController {
	const model_name = 'Spend';
    public function lists(){
        //print_r($_REQUEST['time-start']);
    	if(isset($_REQUEST['user_account'])){
    		$map['user_account']=array('like','%'.$_REQUEST['user_account'].'%');
    		unset($_REQUEST['user_account']);
    	}
    	if(isset($_REQUEST['time-start'])&&isset($_REQUEST['time-end'])){
            $map['pay_time'] =array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])));
            unset($_REQUEST['time-start']);unset($_REQUEST['time-end']);
        }
        if(isset($_REQUEST['start'])&&isset($_REQUEST['end'])){
            $map['pay_time'] =array('BETWEEN',array(strtotime($_REQUEST['start']),strtotime($_REQUEST['end'])));
            unset($_REQUEST['start']);unset($_REQUEST['end']);
        }
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
        
        if(isset($_REQUEST['pay_order_number'])){
        	$map['pay_order_number']=array('like','%'.$_REQUEST['pay_order_number'].'%');
        	unset($_REQUEST['pay_order_number']);
        }
        if(isset($_REQUEST['pay_status'])){
            $map['pay_status']=$_REQUEST['pay_status'];
            unset($_REQUEST['pay_status']);
        }
        if(isset($_REQUEST['pay_game_status'])){
            $map['pay_game_status']=$_REQUEST['pay_game_status'];
            unset($_REQUEST['pay_game_status']);
        }
        if(isset($_REQUEST['pay_way'])){
            $map['pay_way']=$_REQUEST['pay_way'];
            unset($_REQUEST['pay_status']);
        }
        $map1=$map;
        $map1['pay_status']=1;
        $total=D(self::model_name)->where($map1)->sum('pay_amount');
        if(isset($map['pay_status'])&&$map['pay_status']==0){
            $total=sprintf("%.2f",0);
        }else{
            $total=sprintf("%.2f",$total);
        }    
        $this->assign('total',$total);
        $map['order']='pay_time DESC';
    	parent::lists(self::model_name,$_GET["p"],$map);
    }

    
    /**
     * 快发订单查询
     * @author whh
     */
    public function search_kuaifa(){
            $status=$_REQUEST['status'];
            $map['pay_order_number']=$_REQUEST['pay_order_number'];      
             
            if ($status == 0) {
            $model=M('Spend','tab_');
            $data=$model->where($map)->find();
            $this->assign('list_data', $data);
            } else {
            $model=M('bind_spend','tab_');
            $data=$model->where($map)->find();
            $this->assign('list_data', $data);
            } 
            $this->display();
         
    }
}
