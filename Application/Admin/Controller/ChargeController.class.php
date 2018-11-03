<?php

namespace Admin\Controller;
use User\Api\UserApi as UserApi;

/**
 * 后台首页控制器
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
class ChargeController extends ThinkController {
    
    /**
	*纠错列表
    */
    public function lists($p=0){
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
        //print_r($map);exit;
        parent::lists("charge",$p,$map);
    }

    
    //新增
    public function add(){
    	if(IS_POST){
    		$charge=M("Charge","tab_");
    		$add=$charge->create();
    		$map['promote_id']=$add['promote_id'];
    		$map['game_id']=$add['game_id'];
    		$add['game_name']=get_game_name($add['game_id']);
    		$add['promote_name']=get_promote_name($add['promote_id']);    		
    		$find=$charge->where($map)->find();
    	if(null!==$find){
    		$this->error("该推广已经设置过此游戏的代充比例");
    		}
    	 if(preg_match("/^[1-9](\.\d+)?$/", $add['discount'])){
    	 	$charge->add($add);
    	 	$this->success("添加成功",U("lists"));
    	 }else{
    	 	$this->error("代充比例设置错误");
    	 }
    	}else{
    	   $this->display();
    	}
    }



      public function edit($id) {
		$charge=M("Charge","tab_");
        $add=$charge->create();
        if(IS_POST){
           if(preg_match("/^[1-9](\.\d+)?$/", $add['discount'])){
            $charge->save($add);
            $this->success("修改成功",session('url_back'));
         }else{
            $this->error("代充比例设置错误");
         }
            }else{
            session('url_back',$_SERVER['HTTP_REFERER']);
            $map['id']=$id;
            $lists=$charge->where($map)->find();
            $this->assign("data",$lists);
            $this->meta_title = '编辑游戏返利';
            $this->display();
            }
        }
        


    public function del($model = null, $ids=null){
        $model = M('Model')->getByName("charge"); /*通过Model名称获取Model完整信息*/
        parent::remove($model["id"],'Set',$ids);
    }


    //渠道和渠道间，渠道和用户间转移平台币记录

    public function agency_list($p=0)
    {      
        if(isset($_REQUEST['promote_account'])&&$_REQUEST['promote_account']!==""){
            $map['promote_account']=array("like","%".$_REQUEST['promote_account']."%");
            unset($_REQUEST['promote_account']);
        }
        if(isset($_REQUEST['agents_name'])&&$_REQUEST['agents_name']!==""){
            $map['agents_name']=array("like","%".$_REQUEST['agents_name']."%");
            unset($_REQUEST['agents_name']);
        }

      $total=M("PayAgents",'tab_')->where($map)->sum('amount');
      $total=sprintf("%.2f",$total);
      $this->assign('total',$total); 
      //$map['promote_id']=get_pid(); 
      $this->lists_move('PayAgents',$p,$map);
   
    }
    

     /**
     * 显示指定模型列表数据
     * @param  String $model 模型标识
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function lists_move($model = null, $p = 0,$extend_map = array()){
        
        $model || $this->error('模型名标识必须！');
        $page = intval($p);
        $page = $page ? $page : 1; //默认显示第一页数据

        //获取模型信息
        $model = M('Model')->getByName($model);
        $model || $this->error('模型不存在！');

        //解析列表规则
        $fields = array();


        // 关键字搜索
        $map    =   $extend_map;
        $key    =   $model['search_key']?$model['search_key']:'title';
        $key1    =   $model['search_key1']?$model['search_key1']:'title';
        if(isset($_REQUEST[$key])){
            $map[$key]  =   array('like','%'.$_GET[$key].'%');
            unset($_REQUEST[$key]);
        }
        if(isset($_REQUEST[$key1])){
            $map[$key1]  =   array('like','%'.$_GET[$key1].'%');
            unset($_REQUEST[$key1]);
        }
        if(isset($_REQUEST['agents_name'])){
            $map['agents_name']=array('like','%'.$_REQUEST['agents_name'].'%');
            unset($_REQUEST['agents_name']);
        }

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

        // 条件搜索
        foreach($_REQUEST as $name=>$val){
            if(in_array($name,$fields)){
                $map[$name] =   $val;
            }
        }
        $row    = empty($model['list_row']) ? 15 : $model['list_row'];
        $name = parse_name(get_table_name($model['id']), true);
       /* print_r($map);
        exit;*/
        $data = M($name,"tab_")
            /* 查询指定字段，不指定则查询所有字段 */
            ->field(empty($fields) ? true : $fields)
            // 查询条件
            ->where($map)
            /* 默认通过id逆序排列 */
            ->order($model['need_pk']?'id DESC':'')
            /* 数据分页 */
            ->page($page, $row)
            /* 执行查询 */
            ->select();

        /* 查询记录总数 */
        $count = M($name,"tab_")->where($map)->count();
        //分页
        if($count > $row){
            $page = new \Think\Page($count, $row);
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $this->assign('_page', $page->show());
        }
        //$data   =   $this->parseDocumentList($data,$model['id']);
        $this->assign("count",$count);
        $this->assign('model', $model);
        $this->assign('list_grids', $grids);
        $this->assign('list_data', $data);
        $this->meta_title = $model['title'].'列表';
        $this->display($model['template_list']);
    }
}
