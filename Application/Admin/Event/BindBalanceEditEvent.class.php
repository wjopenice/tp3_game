<?php
namespace Admin\Event;
use Think\Controller;
use Admin\Model;
/**
 * 后台事件控制器
 * @author 王贺 
 */
class BindBalanceEditEvent extends Controller {
    /*
    *自选
    */
    public function add1()
    {
            $type = $_REQUEST['type'];
            $Propay = A('BangPropay','Event');
            $map['account']=$_POST['account'];
            $res=M('promote','tab_')->where($map)->find();
            if (empty($res)) {
                $this->error("此用户不是渠道用户",U('bind_balance_edit'));exit;
            }
            $gid=$_POST['game_id'];
            $account=$_POST['account'];
            $amount= $_POST['amount'];
            if($gid==""){$this->error("游戏不能为空",U('bind_balance_edit'));exit;}
                if(!is_numeric($amount)||$amount<0){
                    $this->error("金额不正确！",U("bind_balance_edit"));
                    return false;  
                }
                $map1['game_id']=$gid;
                $map1['promote_account']=$account;
                $pro=M('promote_game','tab_')->where($map1)->find();
                    if (empty($pro)) {
                        $this->error("此渠道账号没有充值过，请先充值",U("bind_balance_edit"));exit;
                    }else{
                        $map2['promote_id']=get_promote_id($_POST['account']);
                        $map2['game_id']   =$gid;
                        $pro_game=M('promote_game','tab_')->field('bind_balance')->where($map2)->find();
                        $pge=M('promote_game_edit',"tab_");
                        $data=array(
                            'promote_id'       =>get_promote_id($_POST['account']),
                            'promote_account'  =>$_POST['account'],
                            'game_id'          =>$gid,
                            'game_name'        =>get_game_name($gid),
                            'prev_amount'      =>$pro_game['bind_balance'],
                            'amount'           =>$amount,
                            'status'           =>0,
                            'op_id'            =>is_login(),
                            'op_account'       =>get_admin_name(is_login()),
                            'create_time'      =>time()
                            );
                        $pge->add($data);
                        $this->success("操作成功",U("bind_balance_edit"));
                    }       
    }

    /**
     * 内充管理---导入Excel
     * @author 顽皮蛋 <shf_l@163.com>
     */
    public function add2(){
        header("Content-Type:text/html;charset=utf-8");
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     3145728 ;// 设置附件上传大小
        $upload->exts      =     array('xls', 'xlsx');// 设置附件上传类
        $upload->rootPath  =     './Uploads/'; // 设置附件上传目录
        $upload->savePath  =      'excel/'; // 设置附件上传目录
        // 上传文件
        $info   =   $upload->uploadOne($_FILES['excelData']);
        $filename = './Uploads/'.$info['savepath'].$info['savename'];
        $exts = $info['ext'];
        if(!$info) {// 上传错误提示错误信息
            $this->error($upload->getError());
          }else{// 上传成功
            $this->charge_import($filename, $exts);
        }
    }


    public function add3(){
         $account=$_POST['pay_names'];
        $amount=$_POST['amount'];
        $gid=$_POST['game_id'];
      if($gid==""){$this->error("游戏不能为空",U('bang_propay'));}
        if(empty($account)){$this->error("充值人员不能为空");}
        if($amount<0){$this->error("金额不正确！");}            
        $namearr = explode("\n",$account);
        for($i=0;$i<count($namearr);$i++){
            $user=get_promote_one_list(str_replace(array("\r\n", "\r", "\n"), "", $namearr[$i]));
            if(null!=$user){
                $map2['promote_id']=$user['id'];
                $map2['game_id']   =$gid;
                $pro_game=M('promote_game','tab_')->field('bind_balance')->where($map2)->find();
                $data=array(
                            'promote_id'       =>$user['id'],
                            'promote_account'  =>$namearr[$i],
                            'game_id'          =>$gid,
                            'game_name'        =>get_game_name($gid),
                            'prev_amount'      =>$pro_game['bind_balance'],
                            'amount'           =>$amount,
                            'status'           =>0,
                            'op_id'            =>UID,
                            'op_account'       =>get_admin_name(is_login()),
                            'create_time'      =>time()
                            );
                $prov=M("promote_game_edit","tab_")->add($data);
            }
        }
        $this->success("提交成功",U("bind_balance_edit"));
    }



    //导入数据方法
    protected function charge_import($filename, $exts='xls'){
        //导入PHPExcel类库，因为PHPExcel没有用命名空间，只能inport导入
        //import("Org.Util.PHPExcel");
        vendor("PHPExcel.PHPExcel");
        //创建PHPExcel对象，注意，不能少了\
        $PHPExcel=new \PHPExcel();
        //如果excel文件后缀名为.xls，导入这个类
        if($exts == 'xls'){
            //import("Org.Util.PHPExcel.Reader.Excel5");
            $PHPReader=new \PHPExcel_Reader_Excel5();
        }else if($exts == 'xlsx'){
            //import("Org.Util.PHPExcel.Reader.Excel2007");
            $PHPReader=new \PHPExcel_Reader_Excel2007();
        }
        //载入文件
        $PHPExcel=$PHPReader->load($filename);
        //获取表中的第一个工作表，如果要获取第二个，把0改为1，依次类推
        $currentSheet=$PHPExcel->getSheet(0);
        //获取总列数
        $allColumn=$currentSheet->getHighestColumn();
        //获取总行数
        $allRow=$currentSheet->getHighestRow();
        //循环获取表中的数据，$currentRow表示当前行，从哪行开始读取数据，索引值从0开始
        for($currentRow=1;$currentRow<=$allRow;$currentRow++){
            //从哪列开始，A表示第一列
            for($currentColumn='A';$currentColumn<=$allColumn;$currentColumn++){
                //数据坐标
                $address=$currentColumn.$currentRow;
                //读取到的数据，保存到数组$arr中
                $data[$currentRow][$currentColumn]=$currentSheet->getCell($address)->getValue();
            }

        }
        $this->save_import($data);
    }

    //保存导入数据并返回错误信息
    public function save_import($data){ 
        unset($data[1]);
        $errorNum = 0;
        $succNum = 0;
        $errorList = array();//存储错误数据;
        foreach ($data as $k=>$v){
          $errorList[$errorNum]['A'] = $v['A'];
          $errorList[$errorNum]['B'] = $v['B'];
          $errorList[$errorNum]['C'] = $v['C'];
          $map['id']=array('in',get_promote_id($v['A']));
          $u = M('promote',"tab_")
          ->where($map)->find();
          if(empty($u)){//用户名不存在
            $errorList[$errorNum]['D'] = '用户名不存在';
            $errorNum++;
            continue;
          }
          $g = D('Game')->where(array('id'=>get_game_id($v['B'])))->find();
          if(empty($g)){//游戏不存在
            $errorList[$errorNum]['D'] = '游戏不存在';
            $errorNum++;
            continue;
          }
          if($v['C']<0){//金额有问题
            $errorList[$errorNum]['D'] = '金额有问题';
            $errorNum++;
            continue;
          }
          $succNum++;
          $map2['promote_id']=get_promote_id($v['A']);
          $map2['game_id']   =get_game_id($v['B']);
          $pro_game=M('promote_game','tab_')->field('bind_balance')->where($map2)->find();
          $data=array(
                'promote_id'       =>get_promote_id($v['A']),
                'promote_account'  =>$v['A'],            
                'game_id'          =>get_game_id($v['B']),
                'game_name'        =>$v['B'],
                'prev_amount'      =>$pro_game['bind_balance'],
                'amount'           =>(double)$v['C'],
                'status'           =>0,
                'op_id'            =>UID,
                'op_account'       =>get_admin_name(is_login()),
                'create_time'      =>time()            
          );                 
          D('promote_game_edit')->add($data);
        }
        $a = json_encode($errorList);
        $json = urlencode(json_encode($errorList));          
        $this->assign ( 'errorNum', $errorNum );
        $this->assign ( 'succNum', $succNum );
        $this->assign ( 'status', 1 );
        $this->assign ( 'json', $json);
        $this->success('成功：'.$succNum.';失败：'.$errorNum,U('bind_balance_edit'));
    }
}