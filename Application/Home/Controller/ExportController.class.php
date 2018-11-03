<?php
namespace Home\Controller;
use Think\Controller;
class ExportController extends Controller
{
	public function exportExcel($expTitle,$expCellName,$expTableData){
        $xlsTitle = iconv('utf-8', 'gb2312', $expTitle);//文件名称  
        $fileName = session('user_auth.username').date('_YmdHis');//or $xlsTitle 文件名称可根据自己情况设定
        $cellNum = count($expCellName);
        $dataNum = count($expTableData);
        Vendor("PHPExcel.PHPExcel");
        $objPHPExcel = new \PHPExcel();
        $cellName = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');
        $objPHPExcel->getActiveSheet(0)->mergeCells('A1:'.$cellName[$cellNum-1].'1');//合并单元格
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $expTitle);  
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('A1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        for($i=0;$i<$cellNum;$i++){
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i].'2', $expCellName[$i][1]); 
        } 
        for($i=0;$i<$dataNum;$i++){
          for($j=0;$j<$cellNum;$j++){
            $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j].($i+3), $expTableData[$i][$expCellName[$j][0]]);
          }             
        }  
        header('pragma:public');
        header('Content-type:application/vnd.ms-excel;charset=utf-8;name="'.$xlsTitle.'.xls"');
        header("Content-Disposition:attachment;filename=$fileName.xls");//attachment新窗口打印inline本窗口打印
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');  
        $objWriter->save('php://output'); 
        exit;   
    }

	//导出Excel
     function expUser($id){
     	switch ($id) {
          case 1:
            $xlsName  = "转移绑定平台币记录";
            $xlsCell  = array(
                    array('id','编号'),
                    array('agents_name','充值账号'),
                    array('game_id','游戏ID'),
                    array('game_name','游戏名称'), 
                    array('amount','充值金额'),
                    array('type','用户类型'),
                    array('create_time','充值时间')  
            ); 
           if($_REQUEST['game_id']>0){
                $map['game_id']=$_REQUEST['game_id'];
            }        
            if(!empty($_REQUEST['time-start']) && !empty($_REQUEST['time-end'])){
                $map['create_time']=array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])+24*60*60-1));
            }
            if(isset($_REQUEST['start'])&&isset($_REQUEST['end'])){
                $map['create_time']=array('BETWEEN',array(strtotime($_REQUEST['start']),strtotime($_REQUEST['end'])+24*60*60-1));
            } 
            if(isset($_REQUEST['type'])){
            if($_REQUEST['type']==''){
                unset($_REQUEST['type']);
            }else{
                $map['type'] = $_REQUEST['type'];
                unset($_REQUEST['type']);
             }
            }
            $map['promote_id']=get_pid();
           $xlsData=M('movebang','tab_')
           ->field("id,agents_name,game_id,game_name,type,amount,FROM_UNIXTIME(create_time,'%Y-%m-%d %H:%i:%s') as create_time")
             ->where($map) 
             ->order("create_time")
             ->select(); 
             foreach ($xlsData as &$value) {
                    $value['game_name']=get_game_name($value['game_id']);
                    $value['type'] = get_type_move($value['type']);
                }
        break; 
             case 2:
                $xlsName  = "游戏绑定平台币记录";
                $xlsCell  = array(
                    array('id','编号'),
                    array('agents_name','充值账号'),
                    array('type','账号类型(0:渠道;1:用户)'),
                    array('amount','充值金额'),  
                    array('create_time','充值时间')    
                );
                if(isset($_REQUEST['time-start'])&&isset($_REQUEST['time-end'])){
                $map['create_time']=array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])+24*60*60-1));
                }
                if(isset($_REQUEST['start'])&&isset($_REQUEST['end'])){
                    $map['create_time']=array('BETWEEN',array(strtotime($_REQUEST['start']),strtotime($_REQUEST['end'])+24*60*60-1));
                }
                 if(isset($_REQUEST['agents_name'])&&trim($_REQUEST['agents_name'])){
                    $map['agents_name']=$_REQUEST['agents_name'];
                    unset($_REQUEST['agents_name']);
                }
                if(isset($_REQUEST['type'])){
                    if($_REQUEST['type']==''){
                        unset($_REQUEST['type']);
                    }else{
                        $map['type'] = $_REQUEST['type'];
                        unset($_REQUEST['type']);
                    }
                }
            $map['promote_id']=get_pid();
                $xlsData=M('PayAgents','tab_')
                ->field("id,agents_name,type,FROM_UNIXTIME(create_time,'%Y-%m-%d %H:%i:%s') as create_time,amount")
                ->where($map) 
                ->order("id")
                ->select(); 
                //echo M('PayAgents','tab_')->getlastsql();exit;
                foreach ($xlsData as &$value) {
                    $value['type'] =get_typo($value['type']);
                }
            break; 
         case 3:
            $xlsName  = "游戏绑定平台币余额记录";
            $xlsCell  = array(
                    array('id','编号'),
                    array('promote_account','用户账号'),
                    array('promote_nickname','用户昵称'), 
                    array('game_name','游戏名称'),
                    array('bind_balance','游戏余额'), 
            ); 
            if($_REQUEST['game_id']>0){
            $map['game_id']=$_REQUEST['game_id'];
        }
        $map['promote_account']=session("promote_auth.account");   
           $xlsData=M('promote_game','tab_')
           ->field("id,promote_account,promote_nickname,game_name,bind_balance")
             ->where($map) 
             ->select(); 
        break; 

        case 4:
                $xlsName  = "充值明细";
                $xlsCell  = array(
                    array('id','编号'),
                    array('user_account','用户账号'),
                    array('pay_order_number','订单号'),
                    array('game_name','游戏名称'), 
                    array('pay_amount','充值金额'), 
                    array('pay_way','支付方式(0平台币;1支付宝;2微信)'), 
                    array('pay_time','充值时间'),
                    array('promote_id','渠道id'),
                    array('promote_account','所属渠道'),    
                );
                        $pro_id=get_prmoote_chlid_user(session('promote_auth.pid'));
                foreach ($pro_id as $key => $value) {
                    $pro_id1[]=$value['id'];
                }
                if(!empty($pro_id1)){
                    $pro_id2=array_merge($pro_id1,array(get_pid()));
                }else{
                    $pro_id2=array(get_pid());
                }
                $map['promote_id'] = array('in',$pro_id2);
                //$_REQUEST['user_account']=$_REQUEST['account'];
                if(isset($_REQUEST['user_account'])&&trim($_REQUEST['user_account'])){
                    $map['user_account']=array('like','%'.$_REQUEST['user_account'].'%');
                    unset($_REQUEST['user_account']);
                }
                  if(isset($_REQUEST['game_name'])){
                    if($_REQUEST['game_name']=='全部游戏'){
                        unset($_REQUEST['game_name']);
                    }else{
                        $map['game_name'] = array('like','%'.$_REQUEST['game_name'].'%');
                        unset($_REQUEST['game_name']);
                    }
                }
                if($_REQUEST['promote_id']>0){
                    $map['promote_id']=$_REQUEST['promote_id'];
                }
                if(!empty($_REQUEST['time-start'])&&!empty($_REQUEST['time-end'])){
                    $map['pay_time']  =  array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])+24*60*60-1));
                    unset($_REQUEST['time-start']);unset($_REQUEST['time-end']);
                }
                if(!empty($_REQUEST['start'])&&!empty($_REQUEST['end'])){

                    $map['pay_time']  =  array('BETWEEN',array(strtotime($_REQUEST['start']),strtotime($_REQUEST['end'])+24*60*60-1));

                    unset($_REQUEST['start']);unset($_REQUEST['end']);

                }
                $map['pay_status'] = 1;
                $map['pay_way']=0;
                $xlsData=M('Spend','tab_')
                ->field("id,user_account,pay_order_number,game_name,pay_amount,pay_way,FROM_UNIXTIME(pay_time,'%Y-%m-%d %H:%i:%s') as pay_time,promote_id,promote_account")
                ->where($map) 
                ->order("id")
                ->select(); 
                
            break; 
            case 5:
                $xlsName  = "注册明细";
                $xlsCell  = array(
                    array('id','编号'),
                    array('account','用户账号'),
                    array('register_ip','注册IP'), 
                    array('register_time','注册日期'),
                    array('promote_id','推广员id'),
                    array('promote_account','推广人员'),   
                );
                $pro_id=get_prmoote_chlid_account(session('promote_auth.pid'));
                foreach ($pro_id as $key => $value) {
                    $pro_id1[]=$value['id'];
                }

                if(!empty($pro_id1)){
                    $pro_id2=array_merge($pro_id1,array(get_pid()));
                }else{
                    $pro_id2=array(get_pid());
                }

                $map['promote_id'] = array('in',$pro_id2);

                if(isset($_REQUEST['account'])&&trim($_REQUEST['account'])){
                    $map['account']=array('like','%'.$_REQUEST['account'].'%');
                    unset($_REQUEST['account']);
                }

                if(isset($_REQUEST['game_appid'])&&$_REQUEST['game_appid']!=0){
                    $map['game_appid']=$_REQUEST['game_appid'];
                }

                if($_REQUEST['promote_id']>0){
                    $map['promote_id']=$_REQUEST['promote_id'];
                }

                if(!empty($_REQUEST['time-start'])&&!empty($_REQUEST['time-end'])){
                    $map['register_time']  =  array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])+24*60*60-1));

                    // unset($_REQUEST['time-start']);unset($_REQUEST['time-end']);

                }

                if(!empty($_REQUEST['start'])&&!empty($_REQUEST['end'])){

                    $map['register_time']  =  array('BETWEEN',array(strtotime($_REQUEST['start']),strtotime($_REQUEST['end'])+24*60*60-1));

                    // unset($_REQUEST['time-start']);unset($_REQUEST['time-end']);

                }
                // var_dump($map);exit;
                $map['is_check']=array('neq',2);     
      
                $xlsData=M('User','tab_')
                ->field("id,account,register_ip,FROM_UNIXTIME(register_time,'%Y-%m-%d %H:%i:%s') as register_time,promote_id,promote_account")
                ->where($map) 
                ->order("id")
                ->select(); 
                
            break;

            case 6:
                $xlsName  = "绑币充值明细";
                $xlsCell  = array(
                    array('id','编号'),
                    array('user_account','用户账号'),
                    array('pay_order_number','订单号'),
                    array('game_name','游戏名称'), 
                    array('pay_amount','充值金额'), 
                    array('pay_time','充值时间'),
                    array('promote_id','渠道id'),
                    array('promote_account','所属渠道'),    
                );
              $pro_id=get_prmoote_chlid_account(session('promote_auth.pid'));
                //print_r($pro_id);exit;
                foreach ($pro_id as $key => $value) {
                    $pro_id1[]=$value['id'];
                }
                if(!empty($pro_id1)){
                    $pro_id2=array_merge($pro_id1,array(get_pid()));
                }else{
                    $pro_id2=array(get_pid());
                }
                //print_r($pro_id2);exit;
                $map['promote_id'] = array('in',$pro_id2);
                //$map['promote_id'] =session('promote_auth.pid');
                //print_r($map);exit;
                //$_REQUEST['user_account']=$_REQUEST['account'];
                if(isset($_REQUEST['user_account'])&&trim($_REQUEST['user_account'])){
                    $map['user_account']=array('like','%'.$_REQUEST['user_account'].'%');
                    unset($_REQUEST['user_account']);
                }
                if(isset($_REQUEST['game_name'])){
                    if($_REQUEST['game_name']==''){
                        unset($_REQUEST['game_name']);
                    }else{
                        $map['game_name'] =array('like','%'.$_REQUEST['game_name'].'%');
                        unset($_REQUEST['game_name']);
                    }
                }
                if($_REQUEST['promote_id']>0){
                    $map['promote_id']=$_REQUEST['promote_id'];
                }
                if(!empty($_REQUEST['time-start'])&&!empty($_REQUEST['time-end'])){
                    $map['pay_time']  =  array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])+24*60*60-1));
                    unset($_REQUEST['time-start']);unset($_REQUEST['time-end']);
                }
                if(!empty($_REQUEST['start'])&&!empty($_REQUEST['end'])){

                    $map['pay_time']  =  array('BETWEEN',array(strtotime($_REQUEST['start']),strtotime($_REQUEST['end'])+24*60*60-1));

                    unset($_REQUEST['start']);unset($_REQUEST['end']);

                }
                $map['pay_status'] = 1;
                $xlsData=M('BindSpend','tab_')
                ->field("id,user_account,pay_order_number,game_name,pay_amount,FROM_UNIXTIME(pay_time,'%Y-%m-%d %H:%i:%s') as pay_time,promote_id,promote_account")
                ->where($map) 
                ->order("id desc")
                ->select(); 
                
            break; 
     	}
     	   $this->exportExcel($xlsName,$xlsCell,$xlsData);

     }
	
}


