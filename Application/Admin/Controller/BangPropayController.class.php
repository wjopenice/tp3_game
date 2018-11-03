<?php

namespace Admin\Controller;
use User\Api\UserApi as UserApi;
/**
 * 后台事件控制器
 * @author  
 */
class BangPropayController extends ThinkController{
	const model_name = 'bang_propay';

    public function promote_game_list(){
        if(isset($_REQUEST['promote_account'])){
            $map['promote_account']=array('like','%'.$_REQUEST['promote_account'].'%');
            unset($_REQUEST['promote_account']);
        }
        if(isset($_REQUEST['time-start'])&&isset($_REQUEST['time-end'])){
            $map['create_time'] =array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])+24*60*60-1));
            unset($_REQUEST['time-start']);unset($_REQUEST['time-end']);
        }
    
        if(isset($_REQUEST['start']) && isset($_REQUEST['end'])){
            $map['create_time'] = array('BETWEEN',array(strtotime($_REQUEST['start']),strtotime($_REQUEST['end'])+24*60*60-1));
            #unset($_REQUEST['start']);unset($_REQUEST['end']);
        }

         if(isset($_REQUEST['game_name'])){
            if($_REQUEST['game_name']=='全部游戏'){
                unset($_REQUEST['game_name']);
            }else{
                $map['game_id']=$_REQUEST['game_name'];
            }
            unset($_REQUEST['game_name']);
        }
        //$promote=M('promote','tab_')->field('id')->select();
        //$pro=array_column($promote, 'id');
        //$map['user_id']=array('in',implode(",",$pro));
        parent::lists("promote_game",$_GET["p"],$map);
    }

    public function movebanglist(){
        if(isset($_REQUEST['promote_account'])){
            $map['promote_account']=array('like','%'.$_REQUEST['promote_account'].'%');
            unset($_REQUEST['promote_account']);
        }
        if(isset($_REQUEST['time-start'])&&isset($_REQUEST['time-end'])){
            $map['create_time'] =array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])+24*60*60-1));
            unset($_REQUEST['time-start']);unset($_REQUEST['time-end']);
        }
    
        if(isset($_REQUEST['start']) && isset($_REQUEST['end'])){
            $map['create_time'] = array('BETWEEN',array(strtotime($_REQUEST['start']),strtotime($_REQUEST['end'])+24*60*60-1));
            #unset($_REQUEST['start']);unset($_REQUEST['end']);
        }

        if(isset($_REQUEST['game_name'])){
            if($_REQUEST['game_name']=='全部'){
                unset($_REQUEST['game_name']);
            }else{
                $map['game_name']=$_REQUEST['game_name'];
                unset($_REQUEST['game_name']);
            }
        }
        if(isset($_REQUEST['agents_name'])){
            $map['agents_name']=array('like','%'.$_REQUEST['agents_name'].'%');
            unset($_REQUEST['agents_name']);
        }
        $total=M("Movebang",'tab_')->where($map)->sum('amount');
        $total=sprintf("%.2f",$total);
        $this->assign('total',$total);
        parent::lists("Movebang",$_GET["p"],$map);
    }

    public function bang_propay(){
        if(IS_POST){
            $type = $_REQUEST['type'];
            $Propay = A('BangPropay','Event');
            switch ($type) {
                case 1:
                    $Propay->add1();
                    break;
                case 2:
                    $Propay->add2();
                    break;
                case 3:
                    $Propay->add3();
                    break;
            }
        }   
        else{
            $this->display();
        }
    }
    public function bang_propaylist(){
        if(isset($_REQUEST['game_name'])){
            if($_REQUEST['game_name']=='全部'){
                unset($_REQUEST['game_name']);
            }else{
                $map['game_name']=$_REQUEST['game_name'];
                unset($_REQUEST['game_name']);
            }
        }
       if(isset($_REQUEST['promote_account'])){
            $map['user_account']=array('like','%'.$_REQUEST['promote_account'].'%');
            unset($_REQUEST['promote_account']);
        }
        if(isset($_REQUEST['time-start'])&&isset($_REQUEST['time-end'])){
            $map['create_time'] =array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])+24*60*60-1));
            unset($_REQUEST['time-start']);unset($_REQUEST['time-end']);
        }
    
        if(isset($_REQUEST['start']) && isset($_REQUEST['end'])){
            $map['create_time'] = array('BETWEEN',array(strtotime($_REQUEST['start']),strtotime($_REQUEST['end'])+24*60*60-1));
            #unset($_REQUEST['start']);unset($_REQUEST['end']);
        }
        $total=M(self::model_name,'tab_')->where($map)->sum('amount');
        $total=sprintf("%.2f",$total);
        $this->assign('total',$total);
        parent::lists(self::model_name,$_GET["p"],$map);

    }

    public function batch($ids){
        $list=M("bang_propay","tab_");
        $map['id']=array("in",$ids);
        $map['status']=0;
        $pro=$list->where($map)->select();  
        for ($i=0; $i <count($pro) ; $i++) {
            $maps['promote_id']=$pro[$i]['user_id'];
            $maps['game_id']=$pro[$i]['game_id'];
            $promote_game=M('promote_game','tab_')->where($maps)->find();
            if (empty($promote_game)) {
                $data=array(
                    'promote_id'       =>  $pro[$i]['user_id'],
                    'promote_account'  =>  $pro[$i]['user_account'],
                    'promote_nickname' =>  $pro[$i]['user_nickname'],
                    'game_id'          =>  $pro[$i]['game_id'],
                    'game_name'        =>  $pro[$i]['game_name'],  
                    'bind_balance'     =>  $pro[$i]['amount']
                     );
                M('promote_game','tab_')->add($data);
            }else{
                $user=M("promote_game","tab_")->where($maps)->setInc("bind_balance",$pro[$i]['amount']);
            }
          $list->where($map)->setField("status",1);
        }
        $this->success("充值成功",U("bang_propaylist"));
    }

    public function delprovide($ids){
      $list=M("bang_propay","tab_");
      $map['id']=array("in",$ids);
      $map['status']=0;
      $delete=$list->where($map)->delete();
       if($delete){
            $this->success("批量删除成功！",U("bang_propaylist"));
       }else{
             $this->error("批量删除失败！",U("bang_propaylist"));
        }
    }

    public function bind_balance_edit(){
        if(IS_POST){
            $type = $_REQUEST['type'];
            $Propay = A('BindBalanceEdit','Event');
            switch ($type) {
                case 1:
                    $Propay->add1();
                    break;
                case 2:
                    $Propay->add2();
                    break;
                case 3:
                    $Propay->add3();
                    break;
            }
        }   
        else{
            $this->display();
        }  
    }   


    public function bind_balance_edit_list(){
        if(isset($_REQUEST['promote_account'])){
            $map['promote_account']=array('like','%'.$_REQUEST['promote_account'].'%');
            unset($_REQUEST['promote_account']);
        }
        if(isset($_REQUEST['time-start'])&&isset($_REQUEST['time-end'])){
            $map['create_time'] =array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])+24*60*60-1));
            unset($_REQUEST['time-start']);unset($_REQUEST['time-end']);
        }
    
        if(isset($_REQUEST['start']) && isset($_REQUEST['end'])){
            $map['create_time'] = array('BETWEEN',array(strtotime($_REQUEST['start']),strtotime($_REQUEST['end'])+24*60*60-1));
            unset($_REQUEST['start']);unset($_REQUEST['end']);
        }
        parent::lists("promote_game_edit",$_GET["p"],$map);
    }

    public function batch_edit($ids){
        $list=M("promote_game_edit","tab_");
        $map['id']=array("in",$ids);
        $map['status']=0;
        $pro=$list->where($map)->select();
        for ($i=0; $i <count($pro) ; $i++) {
            $maps['promote_id']=$pro[$i]['promote_id'];
            $maps['game_id']=$pro[$i]['game_id'];
            M("promote_game","tab_")->where($maps)->setField("bind_balance",$pro[$i]['amount']);
            $list->where($map)->setField("status",1);
        }
        $this->success("修改成功",U("bind_balance_edit_list"));
    }
    public function delprovide_edit($ids){
      $list=M("promote_game_edit","tab_");
      $map['id']=array("in",$ids);
      $map['status']=0;
      $delete=$list->where($map)->delete();
       if($delete){
            $this->success("批量删除成功！",U("bind_balance_edit_list"));
       }else{
             $this->error("批量删除失败！",U("bind_balance_edit_list"));
        }
    }
}
?>