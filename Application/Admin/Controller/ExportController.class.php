<?php
namespace admin\Controller;
use Think\Controller;
class ExportController extends Controller
{
	public function exportExcel($expTitle,$expCellName,$expTableData){
        
        $xlsTitle = iconv('utf-8', 'gb2312', $expTitle);//文件名称  
         
        $fileName = session('user_auth.username').date('_YmdHis');//or $xlsTitle 文件名称可根据自己情况设定
        $cellNum = count($expCellName);
        $dataNum = count($expTableData);
        Vendor("PHPExcel.PHPExcel");

        $cacheMethod = \PHPExcel_CachedObjectStorageFactory::cache_in_memory;
        \PHPExcel_Settings::setCacheStorageMethod($cacheMethod);

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
       /* $out1 = ob_get_contents();
        file_put_contents(__DIR__.'/ob.html', json_encode($out1));
        //清除缓冲区
        ob_end_clean() ;*/
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
            $xlsName  = "代充记录";
            $xlsCell  = array(
                    array('id','编号'),
                    array('user_account','账号'),
                    array('game_name','游戏名称'), 
                    array('game_id','游戏ID'), 
                    array('amount','充值金额'),
                    array('real_amount','实扣金额'),
                    array('promote_id','渠道ID'),
                    array('promote_account','所属渠道'),
                    array('cp_name','所属cp'),
                    array('zhekou','折扣比例'),
                    array('pay_status','支付状态(0:失败,1:成功)'),
                    array('create_time','充值时间'),  
                    array('promote_account','推广员账号'),    
            );
                //print_r($xlsCell);exit;
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
                //print_r($map);exit;
                $map1=$map;
                $map1['pay_status']=1;      
               $xlsData=M('agent','tab_')
               ->field("id,user_account,game_name,game_id,amount,promote_id,promote_account,real_amount,zhekou,pay_status,FROM_UNIXTIME(create_time,'%Y-%m-%d %H:%i:%s') as create_time,promote_account")
                 ->where($map1) 
                 ->order("create_time")
                 ->select(); 
               foreach ($xlsData as $key => &$value) {
                    $value['cp_name'] = get_cp_name($value['game_id']);
                }
            break; 
             case 2:
                $xlsName  = "渠道充值";
                $xlsCell  = array(
                    array('id','编号'),
                    array('user_account','账号'),
                    array('game_id','游戏id'),
                    array('game_name','游戏名称'),
                    array('server_name','区服名称'),  
                    array('pay_amount','充值金额'),
                    array('pay_way','充值方式(0平台币;1支付宝;2微信)'),
                    array('pay_time','充值时间'),  
                    array('promote_id','推广员id'), 
                    array('promote_account','推广员名称'),    
                );
            if(isset($_REQUEST['pay_way'])){
                $map['pay_way']=$_REQUEST['pay_way'];
            }
             if(isset($_REQUEST['game_id'])){
               if($_REQUEST['game_id']=='全部'){
                unset($_REQUEST['game_id']);
               }else{
                $map['game_id']=$_REQUEST['game_id'];
                unset($_REQUEST['game_id']);
              }
           }
            if(isset($_REQUEST['promote_id'])){
               if($_REQUEST['promote_id']=='全部'){
                unset($_REQUEST['promote_id']);
               }else{
                $map['promote_id']=$_REQUEST['promote_id'];
                unset($_REQUEST['promote_id']);
               }
             }
            if(isset($_REQUEST['user_account'])){
                $map['user_account']=array('like','%'.$_REQUEST['user_account'].'%');
            }
            if(isset($_REQUEST['time-start'])&&isset($_REQUEST['time-end'])){
                $map['pay_time']=array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])));
            }
            if(isset($_REQUEST['start'])&&isset($_REQUEST['end'])){
                $map['pay_time']=array('BETWEEN',array(strtotime($_REQUEST['start']),strtotime($_REQUEST['end'])));
            }
            $map['tab_spend.pay_status'] = 1;
                $xlsData=M('Spend','tab_')
                ->field("id,user_account,game_name,game_id,promote_id,server_name,pay_amount,FROM_UNIXTIME(pay_time,'%Y-%m-%d %H:%i:%s') as pay_time,pay_way,promote_account")
                ->where($map) 
                ->order("id")
                ->select(); 
            break;

           case 3:
                $xlsName  = "渠道注册";
                $xlsCell  = array(
                    array('account','账号'),
                    array('lock_status','状态(1正常，0锁定)'),
                    array('register_time','注册时间'),
                    array('game_name','注册游戏'),
                    array('register_ip','注册IP'),
                    array('promote_account','所属渠道'),
                    array('nickname','所属专员'),
                );

                if(isset($_REQUEST['account'])){
                    $map['tab_user.account']=$_REQUEST['account'];
                }

                if(isset($_REQUEST['time_start'])&&isset($_REQUEST['time_end'])){
                    $map['register_time']=array('BETWEEN',array(strtotime($_REQUEST['time_start']),strtotime($_REQUEST['time_end'])+24*60*60-1));
                }

                if(!empty($_REQUEST['promote_name'])){
                    if($_REQUEST['promote_name']=='全部'){
                        unset($_REQUEST['promote_name']);
                    }else if($_REQUEST['promote_name']=='自然注册'){
                        $map['u.promote_account']=$_REQUEST['promote_name'];
                        unset($_REQUEST['promote_name']);
                    }else{
                        $map['u.promote_id']=get_promote_id($_REQUEST['promote_name']);
                    }
                }

                if(isset($_REQUEST['game_id'])){
                    $map['game_id'] = $_REQUEST['game_id'];
                    unset($_REQUEST['game_id']);
                }

                if(!empty($_REQUEST['time_start'])&& !empty($_REQUEST['time_end'])){
                    $map['register_time']=array('BETWEEN',array(strtotime($_REQUEST['time_start']),strtotime($_REQUEST['time_end'])+24*60*60-1));
                    unset($_REQUEST['time_start']);unset($_REQUEST['time_end']);
                }

                //取所有游戏列表
                $game_list = format_get_game_list();
                //取所有推广员后台信息
                $promote_admin_list = get_promote_admin_info();

                $xlsData = M('user_play','tab_')->alias('p')
                    ->field('u.id,u.account,u.lock_status,FROM_UNIXTIME(u.register_time,\'%Y-%m-%d %H:%i:%s\') as register_time,u.register_ip,p.game_id,u.promote_id')
                    ->join('left join tab_user as u on p.user_id = u.id')
                    ->where($map)
                    ->order("p.user_id")
                    ->select();

                if($xlsData) {
                    foreach($xlsData as $key => $value) {
                        $game_name = $game_list[$value['game_id']];
                        $nickname = $promote_admin_list[$value['promote_id']]['nickname'];
                        $promote_account = $promote_admin_list[$value['promote_id']]['promote_account'];

                        $xlsData[$key]['game_name'] = $game_name;
                        $xlsData[$key]['nickname'] = $nickname;
                        $xlsData[$key]['promote_account'] = $promote_account;
                    }
                }
                
                //echo M()->_sql();die;
                /*echo "<pre>";
                print_r($xlsData);exit;
                echo "</pre>";*/
                break;

            case 4:
                $xlsName  = "渠道对账";
                $xlsCell  = array(
                    array('id','编号'),
                    array('game_name','充值游戏'),
                    array('pay_amount','充值金额'),
                    array('promote_account','推广员账号'),
                    // array('pay_time','充值时间'),  
                );
            if(isset($_REQUEST['game_name'])){
                if($_REQUEST['game_name']=='全部'){
                    unset($_REQUEST['game_name']);
                }else{
                    $map['game_name']=$_REQUEST['game_name'];
                    unset($_REQUEST['game_name']);
                }
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
            if(isset($_REQUEST['time-start'])&&isset($_REQUEST['time-end'])){
                $map['pay_time']=array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])+24*60*60-1));
            }
            if(isset($_REQUEST['start'])&&isset($_REQUEST['end'])){
                $map['pay_time']=array('BETWEEN',array(strtotime($_REQUEST['start']),strtotime($_REQUEST['end'])+24*60*60-1));
            }
                $xlsData=M('Spend','tab_')
                ->field('tab_spend.*,case parent_id  when 0 then promote_id else parent_id end AS parent_id,sum(pay_amount) AS total_amount,DATE_FORMAT( FROM_UNIXTIME(pay_time),"%Y-%m-%d %H:%i:%s") AS period')
                ->join('left join tab_promote ON tab_spend.promote_id = tab_promote.id') 
                // 查询条件
                ->where($map)
                ->order('pay_time')
                //根据字段分组
                ->group('case parent_id  when 0 then promote_id else parent_id end ,DATE_FORMAT( FROM_UNIXTIME(pay_time),"%Y-%m-%d %H:%i:%s"),game_id')
                ->where($map) 
                ->select();
                // var_dump(M('Spend','tab_')->getlastsql()); 
                // exit;
            break;
            case 5:
                $xlsName  = "渠道结算";
                $xlsCell  = array(
                    array('id','编号'),
                    array('game_name','充值游戏'),
                    array('money','充值金额'),
                    array('account','推广员账号'),
                    array('spend_time','充值时间'),   
                );
                if(isset($_REQUEST['game_name'])){
                $map['game_id']=get_game_id($_REQUEST['game_name']);
                }
                if(isset($_REQUEST['time-start'])&&isset($_REQUEST['time-end'])){
                $map['spend_time']=array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])+24*60*60-1));
                }
                if(isset($_REQUEST['start'])&&isset($_REQUEST['end'])){
                    $map['spend_time']=array('BETWEEN',array(strtotime($_REQUEST['start']),strtotime($_REQUEST['end'])+24*60*60-1));
                }
                $xlsData=M('Settlement as s','tab_')
                ->field("s.id,g.game_name,s.money,p.account,FROM_UNIXTIME(s.spend_time,'%Y-%m-%d %H:%i:%s') as spend_time")
                ->join('left join tab_game as g on s.game_id=g.id')
                ->join('left join tab_promote as p on s.promote_id=p.id')
                ->where($map) 
                ->order("spend_time")
                ->select(); 
            break;
            case 6:
                $xlsName  = "渠道提现";
                $xlsCell  = array(
                    array('id','编号'),
                    array('account','推广员账号'),
                    array('amount','提现金额'),
                    array('username','操作人'),
                    array('create_time','提现时间'),   
                );
                if(isset($_REQUEST['op_account'])){
                    $map['op_account']=array('like','%'.$_REQUEST['op_account'].'%');
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
                if(isset($_REQUEST['time-start'])&&isset($_REQUEST['time-end'])){
                    $map['s.create_time']=array(
                        'BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])+24*60*60-1)
                    );
                }
                if(isset($_REQUEST['start'])&&isset($_REQUEST['end'])){
                    $map['s.create_time']=array(
                        'BETWEEN',array(strtotime($_REQUEST['start']),strtotime($_REQUEST['end'])+24*60*60-1)
                    );
                }
                $xlsData=M('Withdraw as s','tab_')
                ->field("s.id,p.account,s.amount,m.username,FROM_UNIXTIME(s.create_time,'%Y-%m-%d %H:%i:%s') as create_time")
                ->join('left join tab_promote as p on s.promote_id=p.id')
                ->join('left join sys_ucenter_member as m on s.op_id=m.id')
                ->where($map) 
                ->order("create_time")
                ->select(); 
            break;
            case 7:
                $xlsName  = "游戏消费记录";
                $xlsCell  = array(
                    array('id','编号'),
                    array('pay_order_number','订单号'),
                    array('user_account','用户帐号'),
                    array('game_name','游戏名称'),
                    array('game_id','游戏id'),
                    array('cp_name','所属cp'),
                    array('pay_amount','充值金额'),
                    array('pay_time','充值时间'),    
                    array('pay_way','充值方式(0平台币;1支付宝;2微信)'),
                    array('pay_status','充值状态(0未支付;1成功)'),
                    array('pay_game_status','回调状态(0失败;1成功)'),
                );
                if(isset($_REQUEST['user_account'])){
                $map['user_account']=array('like','%'.$_REQUEST['user_account'].'%');
                }
                if(isset($_REQUEST['pay_way'])){
                    $map['pay_way']=$_REQUEST['pay_way'];
                }
                if(isset($_REQUEST['pay_status'])){
                    $map['pay_status']=$_REQUEST['pay_status'];
                }
                if(isset($_REQUEST['pay_game_status'])){
                    $map['pay_game_status']=$_REQUEST['pay_game_status'];
                    unset($_REQUEST['pay_game_status']);
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
                
                if(isset($_REQUEST['time-start']) && isset($_REQUEST['time-end'])){
                    $map['pay_time'] =array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])));
                }
                if(isset($_REQUEST['start']) && isset($_REQUEST['end'])){
                    $map['pay_time'] = array('BETWEEN',array(strtotime($_REQUEST['start']),strtotime($_REQUEST['end'])));;
                }
                $xlsData=M('Spend','tab_')
                ->field("id,pay_order_number,user_account,game_name,game_id,pay_amount,pay_game_status,FROM_UNIXTIME(pay_time,'%Y-%m-%d %H:%i:%s') as pay_time,pay_way,pay_status")
                ->where($map) 
                ->order("pay_time")
                ->select(); 
                foreach ($xlsData as $key => &$value) {
                    $value['cp_name'] = get_cp_name($value['game_id']);
                }
            break;
            case 8:
                $xlsName  = "平台币充值记录";
                $xlsCell  = array(
                    array('id','编号'),
                    array('pay_order_number','订单号'),
                    array('user_nickname','用户昵称'),
                    array('pay_amount','支付金额'),
                    array('promote_account','所属渠道'),
                    array('create_time','充值时间'),    
                    array('pay_way','充值方式(0支付宝;1微信)'),
                    array('pay_status','充值状态(0失败;1成功)'),
                    array('pay_source','支付来源(1:PC;2:SDK;3APP)'),
                );
                if(isset($_REQUEST['user_nickname'])){
                $map['user_nickname']=array('like','%'.$_REQUEST['user_nickname'].'%');
                }
                if(isset($_REQUEST['pay_way'])){
                    $map['pay_way']=$_REQUEST['pay_way'];
                }
                if(isset($_REQUEST['pay_status'])){
                    $map['pay_status']=$_REQUEST['pay_status'];
                }
                if(!isset($_REQUEST['promote_id'])){

                }else if(isset($_REQUEST['promote_id']) && $_REQUEST['promote_id']==0){
                    $map['promote_id']=array('elt',0);
                }elseif(isset($_REQUEST['promote_name'])&&$_REQUEST['promote_id']==-1){
                    $map['promote_id']=get_promote_id($_REQUEST['promote_name']);
                }else{
                    $map['promote_id']=$_REQUEST['promote_id'];
                }
                if(isset($_REQUEST['time-start']) && isset($_REQUEST['time-end'])){
                    $map['create_time'] =array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])+24*60*60-1));
                }
                if(isset($_REQUEST['start']) && isset($_REQUEST['end'])){
                    $map['create_time'] = array('BETWEEN',array(strtotime($_REQUEST['start']),strtotime($_REQUEST['end'])+24*60*60-1));;
                }
                $xlsData=M('Deposit','tab_')
                ->field("id,pay_order_number,user_nickname,pay_amount,promote_account,FROM_UNIXTIME(create_time,'%Y-%m-%d %H:%i:%s') as create_time,pay_way,pay_status,pay_source")
                ->where($map) 
                ->order("create_time")
                ->select(); 
            break;
                
            case 9:
				$xlsName  = "渠道平台币发放";
                $xlsCell  = array(
                    array('id','编号'),
                    array('order_number','订单号'),
                    //array('user_nickname','用户昵称'),
                    //array('game_name','游戏名称'),
                    array('amount','金额'),
                    array('create_time','充值时间'),    
                    array('status','状态(0未充值;1已充值)'),
                    //array('op_account','操作人'),
					array('promote_account','所属渠道'),
                );
                if(isset($_REQUEST['user_nickname'])){
                $map['user_nickname']=array('like','%'.$_REQUEST['user_nickname'].'%');
                }
                if(isset($_REQUEST['time-start']) && isset($_REQUEST['time-end'])){
                    $map['create_time'] =array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])+24*60*60-1));
                }
                if(isset($_REQUEST['start']) && isset($_REQUEST['end'])){
                    $map['create_time'] = array('BETWEEN',array(strtotime($_REQUEST['start']),strtotime($_REQUEST['end'])+24*60*60-1));;
                }
                $xlsData=M('Propay','tab_')
                ->field("id,order_number,amount,FROM_UNIXTIME(create_time,'%Y-%m-%d %H:%i:%s') as create_time,status,promote_account")
                ->where($map) 
                ->order("id desc")
                ->select(); 
            break;
            case 10:
                $xlsName  = "绑定平台币使用记录";
                $xlsCell  = array(
                    array('id','编号'),
                    array('pay_order_number','订单号'),
                    array('user_account','用户名称'),
                    array('promote_id','渠道id'),
                    array('promote_account','渠道名称'),
                    array('game_id','游戏ID'),
                    array('game_name','游戏名称'),
                    array('cp_name','所属CP'),
                    array('pay_amount','金额'),
                    array('props_name','游戏道具'),
                    array('pay_time','充值时间'),    
                    array('pay_status','充值状态(0下单未支付;1成功)'),
                    array('pay_game_status','回调状态(0失败;1成功)'),
                );
                if(isset($_REQUEST['user_account'])){
                    $map['user_account']=$_REQUEST['user_account'];
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
                if(isset($_REQUEST['pay_game_status'])){
                    $map['pay_game_status']=$_REQUEST['pay_game_status'];
                    unset($_REQUEST['pay_game_status']);
                }
                if(isset($_REQUEST['pay_order_number'])){
                    $map['pay_order_number']=array('like','%'.$_REQUEST['pay_order_number'].'%');
                    unset($_REQUEST['pay_order_number']);
                }
                $xlsData=M('Bind_spend','tab_')
                ->field("id,pay_order_number,user_account,game_id,game_name,promote_id,promote_account,pay_amount,props_name,pay_game_status,FROM_UNIXTIME(pay_time,'%Y-%m-%d %H:%i:%s') as pay_time,pay_status")
                ->where($map) 
                ->order("pay_time")
                ->select(); 
                foreach ($xlsData as $key => &$value) {
                    $value['cp_name'] = get_cp_name($value['game_id']);
                }
            break;
            case 11:
                $xlsName  = "礼包领取记录";
                $xlsCell  = array(
                    array('id','编号'),
                    array('game_name','游戏名称'),
                    array('gift_name','礼包名称'),
                    array('user_account','领取用户'),
                    array('novice','激活码'),    
                    array('create_time','领取时间'),
                );
                if(isset($_REQUEST['game_name'])){
                $map['game_name']=array('like','%'.$_REQUEST['game_name'].'%');
                }
                $xlsData=M('gift_record','tab_')
                ->field("id,game_name,gift_name,user_account,novice,FROM_UNIXTIME(create_time,'%Y-%m-%d %H:%i:%s') as create_time")
                ->where($map) 
                ->order("create_time")
                ->select(); 
            break;
          case 12:
                $xlsName  = "平台用户";
                $xlsCell  = array(
                    array('id','用户id'),
                    array('account','用户账号'),
                    array('balance','平台币余额'),
                    array('register_way','注册方式(0:WEB;1:SDK;2:APP)'),
                    array('register_time','注册时间'),
					array('promote_account','所属渠道'),
                );
                if(isset($_REQUEST['account'])){
                    $map['tab_user.account'] = array('like','%'.$_REQUEST['account'].'%');
                }
                if(isset($_REQUEST['game_id'])){
                    $map['tab_game.id'] = $_REQUEST['game_id'];
                }
                if(isset($_REQUEST['register_way'])){
                    $map['register_way'] = $_REQUEST['register_way'];
                }
                if(isset($_REQUEST['time-start']) && isset($_REQUEST['time-end'])){
                    $map['register_time'] =array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])+24*60*60-1));
                }
                if(isset($_REQUEST['start']) && isset($_REQUEST['end'])){
                    $map['register_time'] = array('BETWEEN',array(strtotime($_REQUEST['start']),strtotime($_REQUEST['end'])+24*60*60-1));;
                }
                $xlsData=M('User','tab_')
                ->field("id,account,balance,register_time,register_way,FROM_UNIXTIME(register_time,'%Y-%m-%d %H:%i:%s') as register_time,promote_account")
                ->where($map) 
                ->order("register_time")
                ->select(); 
            break;
            case 13:
                $xlsName  = "代充额度";
                $xlsCell  = array(
                    array('id','编号'),
                    array('account','渠道账号'),
                    array('pay_limit','代充上限'),
                    array('set_pay_time','更新时间'),
                );
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
            $map['pay_limit']=array('gt','0');
                $xlsData=M('Promote','tab_')
                ->field("id,account,pay_limit,FROM_UNIXTIME(set_pay_time,'%Y-%m-%d %H:%i:%s') as set_pay_time")
                ->where($map) 
                ->order("set_pay_time")
                ->select(); 
            break;
             case 14:
                $xlsName  = "游戏返利";
                $xlsCell  = array(
                    array('id','编号'),
                    array('pay_order_number','订单号'),
                    array('user_id','用户名'),
                    array('game_name','游戏名称'),
                    array('pay_amount','充值金额'),
                    array('ratio','返利比例'),
                    array('ratio_amount','返利金额'),
                    array('promote_name','所属推广员'),
                    array('create_time','添加时间'),
                );
            if(isset($_REQUEST['game_name'])){
                if($_REQUEST['game_name']=='全部'){
                    unset($_REQUEST['game_name']);
                }else if($_REQUEST['game_name']=='自然注册'){
                    $map['id']=array("elt",0);
                    unset($_REQUEST['game_name']);
                }else{
                    $map['id']=get_game_id($_REQUEST['game_name']);
                    unset($_REQUEST['game_name']);
                }
            }
            
                $xlsData=M('RebateList','tab_')
                ->field("id,pay_order_number,user_id,user_name,game_name,pay_amount,ratio,ratio_amount,promote_name,FROM_UNIXTIME(create_time,'%Y-%m-%d %H:%i:%s') as create_time")
                ->where($map) 
                ->order("create_time")
                ->select(); 
            break;
			case 15:
                $xlsName  = "用户平台币发放";
                $xlsCell  = array(
                    array('id','编号'),
                    array('order_number','订单号'),
                    array('user_account','用户昵称'),
                    array('game_id','游戏ID'),
                    array('game_name','游戏名称'),
                    array('amount','金额'),
                    array('create_time','充值时间'),    
                    array('status','状态(0未充值;1已充值)'),
                    array('op_account','操作人'),
					//array('promote_account','所属渠道'),
                );
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

                if(isset($_REQUEST['pay_order_number'])){
                    $map['pay_order_number']=array('like','%'.$_REQUEST['pay_order_number'].'%');
                    unset($_REQUEST['pay_order_number']);
                }
                if(isset($_REQUEST['status'])){
                    $map['status']=$_REQUEST['status'];
                    unset($_REQUEST['status']);
                }

                if(isset($_REQUEST['game_id'])){
                    if($_REQUEST['game_id']=='全部'){
                        unset($_REQUEST['game_id']);
                    }else{
                        $map['game_id'] = $_REQUEST['game_id'];
                        unset($_REQUEST['game_id']);
                    }
                }

                $xlsData=M('Provide','tab_')
                ->field("id,order_number,user_account,game_name,game_id,amount,FROM_UNIXTIME(create_time,'%Y-%m-%d %H:%i:%s') as create_time,status,op_account")
                ->where($map) 
                ->order("id desc")
                ->select(); 
            break;
            case 16:
                $xlsName  = "渠道绑定平台币发放";
                $xlsCell  = array(
                    array('id','编号'),
                    array('user_account','用户账号'),
                    array('user_nickname','用户昵称'),
                    array('game_name','游戏名称'),
                    array('amount','金额'),
                    array('create_time','充值时间'),    
                    array('status','状态(0未充值;1已充值)'),
                    array('op_account','操作人'),
                );
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
                $xlsData=M('Bang_propay','tab_')
                ->field("id,user_account,user_nickname,game_name,amount,FROM_UNIXTIME(create_time,'%Y-%m-%d %H:%i:%s') as create_time,status,op_account,promote_account")
                ->where($map) 
                ->order("id desc")
                ->select(); 
                foreach ($xlsData as &$value) {
                    $value['status'] = get_info_status($value['status'],9);
                }
            break;
            case 17:
                $xlsName  = "渠道-游戏余额记录";
                $xlsCell  = array(
                    array('id','编号'),
                    array('promote_account','渠道用户账号'),
                    array('promote_nickname','渠道用户昵称'),
                    array('game_name','游戏名称'),
                    array('bind_balance','游戏绑定平台币余额'),
                );
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
                        }
                        unset($_REQUEST['game_name']);
                }
                $promote=M('promote','tab_')->field('id')->select();
                $pro=array_column($promote, 'id');
                $map['user_id']=array('in',implode(",",$pro));
                $xlsData=M('PromoteGame','tab_')
                ->field("id,game_name,promote_account,promote_nickname,bind_balance")
                ->where($map) 
                ->order("id desc")
                ->select(); 
            break;
            case 18:
                $xlsName  = "绑定平台币转移记录";
                $xlsCell  = array(
                    array('id','编号'),
                    array('agents_name','转入账户'),
                    array('game_name','游戏名称'),
                    array('amount','转入金额'),
                    array('promote_account','所属渠道'),
                    array('type','类型'),
                    array('create_time','发放时间'),
                );
                if(isset($_REQUEST['agents_name'])){
                $map['agents_name']=array('like','%'.$_REQUEST['agents_name'].'%');
                }
                if(isset($_REQUEST['promote_account'])){
                $map['promote_account']=array('like','%'.$_REQUEST['promote_account'].'%');
                }
                if(isset($_REQUEST['time-start']) && isset($_REQUEST['time-end'])){
                    $map['create_time'] =array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])+24*60*60-1));
                }
                if(isset($_REQUEST['start']) && isset($_REQUEST['end'])){
                    $map['create_time'] = array('BETWEEN',array(strtotime($_REQUEST['start']),strtotime($_REQUEST['end'])+24*60*60-1));;
                }
                if(isset($_REQUEST['game_name'])){
                    if($_REQUEST['game_name']=='全部'){
                    unset($_REQUEST['game_name']);
                    }else{
                        $map['game_name']=$_REQUEST['game_name'];
                        unset($_REQUEST['game_name']);
                    }
                }
                $xlsData=M('Movebang','tab_')
                ->field("id,agents_name,game_name,amount,promote_account,type,FROM_UNIXTIME(create_time,'%Y-%m-%d %H:%i:%s') as create_time,game_id")
                ->where($map) 
                ->order("id desc")
                ->select(); 
                foreach ($xlsData as &$value) {
                    $value['game_name']=get_game_name($value['game_id']);
                    $value['type'] = get_type_move($value['type']);
                }
            break;
            case 19:
                $xlsName  = "修改绑定平台币记录";
                $xlsCell  = array(
                    array('id','编号'),
                    array('game_name','游戏名称'),
                    array('promote_account','渠道账号'),
                    array('prev_amount','修改前金额'),
                    array('amount','修改后金额'),
                    array('status','状态'),
                    array('op_account','操作人'),
                    array('create_time','发放时间'),
                );
                if(isset($_REQUEST['user_nickname'])){
                $map['user_nickname']=array('like','%'.$_REQUEST['user_nickname'].'%');
                }
                if(isset($_REQUEST['time-start']) && isset($_REQUEST['time-end'])){
                    $map['create_time'] =array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])+24*60*60-1));
                }
                if(isset($_REQUEST['start']) && isset($_REQUEST['end'])){
                    $map['create_time'] = array('BETWEEN',array(strtotime($_REQUEST['start']),strtotime($_REQUEST['end'])+24*60*60-1));;
                }
                $xlsData=M('promote_game_edit','tab_')
                ->field("id,game_name,amount,promote_account,prev_amount,status,op_account,FROM_UNIXTIME(create_time,'%Y-%m-%d %H:%i:%s') as create_time")
                ->where($map) 
                ->order("id desc")
                ->select(); 
                foreach ($xlsData as &$value) {
                    $value['status'] = get_info_status($value['status'],13);
                }
            break;
            case 20:
                $xlsName  = "渠道支付宝充值记录";
                $xlsCell  = array(
                    array('id','编号'),
                    array('pay_order_number','支付订单号'),
                    array('promote_account','渠道账号'),
                    array('pay_amount','充值金额'),
                    array('pay_status','充值状态(0失败;1成功)'),
                    array('pay_way','充值方式(1支付宝;2微信;3平台币)'),
                    array('create_time','时间'),
                );
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

                $xlsData=M('promote_deposit','tab_')
                ->field("id,pay_order_number,promote_account,pay_amount,pay_status,pay_way,FROM_UNIXTIME(create_time,'%Y-%m-%d %H:%i:%s') as create_time")
                ->where($map) 
                ->order("id desc")
                ->select(); 
                /*foreach ($xlsData as &$value) {
                    $value['status'] = get_info_status($value['status'],13);
                }*/
            break;
            case 21:
                $xlsName  = "用户绑定平台币修改记录";
                $xlsCell  = array(
                    array('id','编号'),
                    array('user_name','用户账号'),
                    array('game_name','游戏名称'),
                    array('admin_name','操作人'),
                    array('money','修改后金额'),
                    array('create_time','时间'),
                );
                if(isset($_REQUEST['game_name'])){
                    if($_REQUEST['game_name']=="全部"){
                         unset($_REQUEST['game_name']);
                        }else{
                        $map['game_id']=get_game_id($_REQUEST['game_name']);
                        }
                        unset($_REQUEST['game_name']);
                    }
                    if(isset($_REQUEST['user_name'])){
                        $map['user_name']=array('like',"%".$_REQUEST['user_name']."%");
                        unset($_REQUEST['user_name']);
                    }
                       
                    if(isset($_REQUEST['time-start']) && isset($_REQUEST['time-end'])){
                        $map['create_time'] =array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])+24*60*60-1));
                        unset($_REQUEST['time-start']);unset($_REQUEST['time-end']);
                    }
                $xlsData=M('user_cancel','tab_')
                ->field("id,user_name,game_name,money,admin_name,FROM_UNIXTIME(create_time,'%Y-%m-%d %H:%i:%s') as create_time")
                ->where($map) 
                ->order("id desc")
                ->select(); 
                /*foreach ($xlsData as &$value) {
                    $value['status'] = get_info_status($value['status'],13);
                }*/
            break;

            case 22:
                $xlsName  = "用户平台币修改记录";
                $xlsCell  = array(
                    array('id','编号'),
                    array('cancel_name','用户账号'),
                    array('admin_name','操作人'),
                    array('money','修改后金额'),
                    array('create_time','时间'),
                );
               
                    if(isset($_REQUEST['cancel_name'])){
                        $map['cancel_name']=array('like',"%".$_REQUEST['cancel_name']."%");
                        unset($_REQUEST['cancel_name']);
                    }
                       
                    if(isset($_REQUEST['time-start']) && isset($_REQUEST['time-end'])){
                        $map['create_time'] =array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])+24*60*60-1));
                        unset($_REQUEST['time-start']);unset($_REQUEST['time-end']);
                    }
                $map['type']=0;
                $xlsData=M('cancel','tab_')
                ->field("id,cancel_name,money,admin_name,FROM_UNIXTIME(create_time,'%Y-%m-%d %H:%i:%s') as create_time")
                ->where($map) 
                ->order("id desc")
                ->select(); 
                /*foreach ($xlsData as &$value) {
                    $value['status'] = get_info_status($value['status'],13);
                }*/
            break;
             case 23:
                $xlsName  = "渠道列表";
                $xlsCell  = array(
                    array('id','编号'),
                    array('account','渠道账号'),
                    array('real_name','真实姓名'),
                    array('mobile_phone','手机号'),
                    array('email','邮箱'),
                    array('admin_id','管理员ID'),
                    array('status','渠道状态：0未审核，1正常，2拉黑'),
                    array('create_time','发放时间'),
                );
                if(isset($_REQUEST['account'])){
                    $map['account']=array('like','%'.$_REQUEST['account'].'%');
                    unset($_REQUEST['account']);
                }
                if(isset($_REQUEST['status'])){
                    $map['status']=$_REQUEST['status'];
                    unset($_REQUEST['status']);
                }
                if(isset($_REQUEST['admin_id'])){
                    if($_REQUEST['admin_id']=='全部'){
                        unset($_REQUEST['admin_id']);
                    }else{
                        $map['admin_id'] = $_REQUEST['admin_id'];
                        unset($_REQUEST['admin_id']);
                    }
                }
                $xlsData=M('promote','tab_')
                ->field("id,account,real_name,mobile_phone,email,status,admin_id,FROM_UNIXTIME(create_time,'%Y-%m-%d %H:%i:%s') as create_time")
                ->where($map) 
                ->order("id desc")
                ->select(); 
            break;
            
            case 24:
                $xlsName  = "游戏列表";
                $xlsCell  = array(
                    array('id','游戏ID'),
                    array('game_name','游戏名称'),
                    array('game_type_name','游戏类型'),
                    array('dow_num','真实下载量'),
                    array('dow_mynum','虚拟下载量'),
                    array('game_status','显示状态:0:关闭，1:开启,'),
                    array('recommend_status','推荐状态:0:不推荐，1:推荐，2:热门，3:最新'),
                    array('cp_name','所属CP'),
                    array('create_user','创建人'),
                   
                );
               if(isset($_REQUEST['game_id'])){
                    if($_REQUEST['game_id']=='全部'){
                    }else{
                        $extend['id'] = $_REQUEST['game_id'];
                    }
                     unset($_REQUEST['game_id']);
                }
                if(isset($_REQUEST['cp_name'])){
                    if($_REQUEST['cp_name']=='全部'){
                    }else{
                        $extend['cp_name'] = $_REQUEST['cp_name'];
                    }
                     unset($_REQUEST['cp_name']);
                }
                if(isset($_REQUEST['create_user'])){
                    if($_REQUEST['create_user']=='全部'){
                    }else{
                        $extend['create_user'] = $_REQUEST['create_user'];
                    }
                     unset($_REQUEST['create_user']);
                }
                if(isset($_REQUEST['game_appid'])){
                    $extend['game_appid'] = array('like','%'.$_REQUEST['game_appid'].'%');
                    unset($_REQUEST['game_appid']);
                }
                if(isset($_REQUEST['game_type_name'])){
                    if($_REQUEST['game_type_name']=='全部'){
                    }else{
                        $extend['game_type_name'] = $_REQUEST['game_type_name'];
                    }
                     unset($_REQUEST['game_type_name']);
                }
                if(isset($_REQUEST['game_status'])){
                    $extend['game_status']=$_REQUEST['game_status'];
                    unset($_REQUEST['game_status']);
                }
                if(isset($_REQUEST['recommend_status'])){
                    $extend['recommend_status']=$_REQUEST['recommend_status'];
                    unset($_REQUEST['recommend_status']);
                }
                $xlsData=M('game','tab_')
                ->field("id,game_name,game_type_name,dow_num,dow_mynum,game_status,recommend_status,cp_name,create_user")
                ->where($extend) 
                ->order("id desc")
                ->select(); 
            break;
            case 25:
                $xlsName  = "注册统计";
                $xlsCell  = array(
                    array('game_name','游戏名称'),
                    array('promote_account','渠道名称'),
                    array('num','注册数'),
                   
                );
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
                      
                        $list_data = M('UserPlay','tab_')->alias('p')->join('tab_user as u on p.user_id = u.id')->field($fields)->where($map)->group($group)->select();
                       
                    }
            $xlsData = $list_data;
            break;
            case 26 :
                $xlsName  = "激活码兑换记录";
                $xlsCell  = array(
                    array('id','编号'),
                    array('user_account','用户账号'),
                    array('user_id','用户id'),
                    array('describe','物品描述'),
                    array('goods_num','物品数量'),
                    array('status','显示状态'),
                    array('cdkey','激活码'),
                    array('create_time','领取时间'),
                   
                );
                if(isset($_REQUEST['user_account'])){
                    $map['user_account']=array('like','%'.$_REQUEST['user_account'].'%');
                    unset($_REQUEST['user_account']);
                }
                if(isset($_REQUEST['prefix'])){
                    $map['cdkey']=array('like','%'.$_REQUEST['prefix'].'%');
                    unset($_REQUEST['prefix']);
                }

                $model=M('exchange_record','tab_');
                $list_data=$model
                ->where($map)
                ->order('id asc')
                ->select();
                foreach ($list_data as $k => &$v) {
                    $v['create_time'] = date('Y-m-d H:i:s',$v['create_time']);
                    $v['status'] = $v['status']?'已成功':'处理中';
                }                
                $xlsData = $list_data;
            break;
            case 27:
                $xlsName  = "登陆统计";
                $xlsCell  = array(
                    array('game_name','游戏名称'),
                    array('promote_account','渠道名称'),
                    array('num','登陆数'),
                   
                );
                $p = (int)I('get.p')?(int)I('get.p'):1;
                $size =  (int)I('get.size')?(int)I('get.size'):15;
                if(isset($_REQUEST['time_start'])&&isset($_REQUEST['time_end']))
                {
                    $map['l.login_time']=array('BETWEEN',array(strtotime($_REQUEST['time_start']),strtotime($_REQUEST['time_end'])));
                    
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
                $xlsData = $list_data;
            break;
            case 28:
                $xlsName  = "消费统计";
                $xlsCell  = array(
                    array('id','编号'),
                    array('game_id','游戏ID'),
                    array('game_name','游戏名称'),
                    array('promote_id','渠道id'),
                    array('promote_account','渠道名称'),
                    array('count','充值金额'),
                    array('ucount','充值人数'),
                    array('pay_time','时间'),
                );
                $where_str = "pay_status=1";
                if(isset($_REQUEST['promote_id'])){
                        if($_REQUEST['promote_id']=='全部'){
                            unset($_REQUEST['promote_id']);
                        }else if($_REQUEST['promote_id']=='自然注册'){
                            $promote_id=array("elt",0);
                            $where_str .= " and promote_id >= 0";
                        }else{
                            $promote_id=$_REQUEST['promote_id'];
                            $where_str .= " and promote_id = '{$promote_id}'";
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
                    $where_str .= " and pay_time between '".strtotime($_REQUEST['time-start'])."' and '".strtotime($_REQUEST['time-end'])."'";
                }
                if(isset($_REQUEST['start'])&&isset($_REQUEST['end'])){
                    $where_str .= " and pay_time between '".strtotime($_REQUEST['start'])."' and '".strtotime($_REQUEST['end'])."'";
                }
                $Model = D();
                $sql = "select sum(f1) as count, sum(f2) as ucount,game_id,game_name, promote_id,promote_account from (select sum(s.pay_amount) as f1, count(DISTINCT s.user_id) as f2,s.game_id,s.game_name, s.promote_id,s.promote_account from tab_spend as s where {$where_str} group by s.game_id, s.promote_id union all select sum(b.pay_amount) as f1, count(DISTINCT b.user_id) as f2,b.game_id,b.game_name, b.promote_id,b.promote_account from tab_bind_spend as b where {$where_str} group by b.game_id, b.promote_id) A GROUP BY game_id, promote_id order by game_id desc";
                $data = $Model->query($sql);
                $xlsData=$data;
            break;
            case 29:
                $xlsName  = "消费总额统计(平台币和支付宝,充值方式0为平台币，1为支付宝)";
                $xlsCell  = array(
                    array('id','编号'),
                    array('game_name','游戏名称'),
                    array('game_id','游戏id'),
                    array('cp_name','所属cp'),  
                    array('pay_way','充值方式(0平台币;1支付宝;2微信)'),
                    array('total_money','总额'),
                    array('start_time','开始时间'),  
                    array('end_time','结束时间'), 
                );
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
                }
                if(isset($_REQUEST['time-start'])&&isset($_REQUEST['time-end'])){
                    $map['pay_time'] =array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])));
                    unset($_REQUEST['time-start']);unset($_REQUEST['time-end']);
                }
                if(isset($_REQUEST['start'])&&isset($_REQUEST['end'])){
                    $map['pay_time'] =array('BETWEEN',array(strtotime($_REQUEST['start']),strtotime($_REQUEST['end'])));
                    //unset($_REQUEST['start']);unset($_REQUEST['end']);
                }
                $map['pay_status']=1;
                $xlsData=M('Spend','tab_')
                ->field('game_id,sum(pay_amount) as total_money,pay_way')
                ->group('game_id')
                ->where($map) 
                ->select(); 
                foreach ($xlsData as $key => &$value) {
                    $value['cp_name'] = get_cp_name($value['game_id']);
                    $value['game_name'] = get_game_name($value['game_id']);
                    $value['start_time'] = $_REQUEST['start'];
                    $value['end_time'] = $_REQUEST['end'];
                    if (!isset($map['pay_way'])) {
                        $value['pay_way'] = '平台币和支付宝总和';
                    }
                    
                }
            break;
            case 30:
                $xlsName  = "绑币总额统计";
                $xlsCell  = array(
                    array('id','编号'),
                    array('game_name','游戏名称'),
                    array('game_id','游戏id'),
                    array('cp_name','所属cp'),  
                    array('total_money','总额'),
                    array('start_time','开始时间'),  
                    array('end_time','结束时间'), 
                );
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
                    $map['pay_time'] =array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])));
                    unset($_REQUEST['time-start']);unset($_REQUEST['time-end']);
                }
                if(isset($_REQUEST['start'])&&isset($_REQUEST['end'])){
                    $map['pay_time'] =array('BETWEEN',array(strtotime($_REQUEST['start']),strtotime($_REQUEST['end'])));
                    //unset($_REQUEST['start']);unset($_REQUEST['end']);
                }
                $map['pay_status']=1;
                $xlsData=M('bindSpend','tab_')
                ->field('game_id,sum(pay_amount) as total_money')
                ->group('game_id')
                ->where($map) 
                ->select(); 
                foreach ($xlsData as $key => &$value) {
                    $value['cp_name'] = get_cp_name($value['game_id']);
                    $value['game_name'] = get_game_name($value['game_id']);
                    $value['start_time'] = $_REQUEST['start'];
                    $value['end_time'] = $_REQUEST['end'];
                    
                }
            break;
            case 31:
                $xlsName  = "绑币总额统计";
                $xlsCell  = array(
                    array('id','编号'),
                    array('game_id','游戏id'),
                    array('game_name','游戏名称'),
                    array('discount','折扣'), 
                    array('promote_id','渠道ID'),
                    array('promote_name','渠道名称'),
                    array('admin_name','管理员'),  
                    array('create_time','创建时间'), 
                );
                if(isset($_REQUEST['promote_name'])){
                    $map['promote_name']=$_REQUEST['promote_name'];
                    unset($_REQUEST['promote_name']);
                }
                if(isset($_REQUEST['time-start'])&&isset($_REQUEST['time-end'])){
                    $map['create_time'] =array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])+24*60*60-1));
                    unset($_REQUEST['time-start']);unset($_REQUEST['time-end']);
                }
            
                if(isset($_REQUEST['start']) && isset($_REQUEST['end'])){
                    $map['create_time'] = array('BETWEEN',array(strtotime($_REQUEST['start']),strtotime($_REQUEST['end'])+24*60*60-1));
                    #unset($_REQUEST['start']);unset($_REQUEST['end']);
                }

                if(isset($_REQUEST['game_id'])){
                    if($_REQUEST['game_id']=='全部'){
                        unset($_REQUEST['game_id']);
                    }else{
                        $map['game_id']=$_REQUEST['game_id'];
                    }
                    unset($_REQUEST['game_id']);
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
                $xlsData=M('Charge','tab_')
                ->field("id,promote_id,promote_name,game_id,game_name,discount,FROM_UNIXTIME(create_time,'%Y-%m-%d %H:%i:%s') as create_time")
                ->where($map) 
                ->order("create_time desc")
                ->select(); 
                foreach ($xlsData as $key => &$value) {
                    $where['account'] = $value['promote_name'];
                    $admin_id = M('promote','tab_')->where($where)->getField('admin_id');
                    if(!empty($admin_id)){
                        $value['admin_name'] = get_admin_nickname($admin_id);
                    }
                                       
                }
            break;

     	}
     	   $this->exportExcel($xlsName,$xlsCell,$xlsData);

     }
	
}