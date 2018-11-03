<?php

namespace Admin\Controller;
use User\Api\UserApi as UserApi;

/**
 * 后台首页控制器
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
class PropayController extends ThinkController {
	const model_name = 'propay';

    public function propaylist(){
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
        $total=M(self::model_name,'tab_')->where($map)->sum('amount');
        $total=sprintf("%.2f",$total);
        $this->assign('total',$total);
    	parent::lists(self::model_name,$_GET["p"],$map);

    }

         public function apply() {
            $page = intval($p);
            $page = $page ? $page : 1; //默认显示第一页数据
            $row    = 10;
          if(isset($_REQUEST['com_name'])){
                $map['com_name']=array("like","%".$_REQUEST['com_name']."%");
            }
         if(isset($_REQUEST['time-start'])&&isset($_REQUEST['time-end'])){
            $map['create_time'] =array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])+24*60*60-1));
            unset($_REQUEST['time-start']);unset($_REQUEST['time-end']);
        }
            $data = M("limit","tab_")
                // 查询条件
                ->where($map)
                /* 默认通过id逆序排列 */
                /* 数据分页 */
                ->page($page, $row)
                /* 执行查询 */
                ->select();
            /* 查询记录总数 */
            $count =M("limit","tab_")->where($map)->count();
             //分页
            if($count > $row){
                $page = new \Think\Page($count, $row);
                $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
                $this->assign('_page', $page->show());
            }
            $this->assign('list_data', $data);
            $this->display();
        }

        public function apply_review($ids){
            $limit=M("limit","tab_");                        
            $map['status']=0;
            $map['id']=array("in",$ids);        
            $py=$limit->where($map)->select();
            for ($i=0; $i <count($py) ; $i++) { 
                $maps['id']=$py[$i]['id'];
                $limit->where($maps)->setField(array("status"=>1,"create_time"=>time()));
                $com_map['com_id']=$py[$i]['com_id'];
                M("comlimits","tab_")->where($com_map)->setInc("limits",$py[$i]['s_limit']);
            }
            $this->success("批量审核成功！",U("apply"));

        }
      public function pro_spend(){
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
      if(isset($_REQUEST['time-start'])&&isset($_REQUEST['time-end'])){
            $map['create_time'] =array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])+24*60*60-1));
            unset($_REQUEST['time-start']);unset($_REQUEST['time-end']);
        }
        $maps['pay_status']=1;
       $total=M("pro_spend",'tab_')->where($maps)->sum('amount');
        $total=sprintf("%.2f",$total);
        $this->assign('total',$total);
        parent::lists("ProSpend",$_GET["p"],$map);
        }
      public function propay(){    
        if(IS_POST){
            $type = $_REQUEST['type'];
            $Propay = A('Propay','Event');
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
    
    public function batch($ids){
        $list=M("Propay","tab_");
        $map['id']=array("in",$ids);
        $map['status']=0;
        $pro=$list->where($map)->select();  
        for ($i=0; $i <count($pro) ; $i++) {
          $maps['id']=$pro[$i]['promote_id'];
          $user=M("Promote","tab_")->where($maps)->setInc("balance_coin",$pro[$i]['amount']);
          if ($user===false){
              $this->error('数据异常，请从新充值',U('propaylist'));
          }else{
            $list->where($map)->setField("status",1);
          $this->success("充值成功",U("propaylist"));
          }
          
        }
        
    }

    public function delprovide($ids){
      $list=M("Propay","tab_");
      $map['id']=array("in",$ids);
      $map['status']=0;
      $delete=$list->where($map)->delete();
       if($delete){
            $this->success("批量删除成功！",U("propaylist"));
       }else{
       		 $this->error("批量删除失败！",U("propaylist"));
        }
    }
    public function dellimit($ids){
      $list=M("limit","tab_");
      $map['id']=array("in",$ids);
      $map['status']=0;
      $delete=$list->where($map)->delete();
       if($delete){
            $this->success("批量删除成功！",U("apply"));
       }else{
             $this->error("批量删除失败！",U("apply"));
        }
    }

}
