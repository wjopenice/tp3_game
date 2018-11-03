<?php

namespace Admin\Controller;
use User\Api\UserApi as UserApi;
use Org\Util\Memcache as Memcache;

/**
 * 后台首页控制器
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
class GiftbagController extends ThinkController {

    const model_name = 'Giftbag';

    public function lists(){
        //$extend_map = array('key'=>'gift_name');
        if(isset($_REQUEST['game_id'])){
            if($_REQUEST['game_id']=='全部'){
                unset($_REQUEST['game_id']);
            }else{
                $extend_map['tab_giftbag.game_id']=$_REQUEST['game_id'];
                unset($_REQUEST['game_id']);
            }
        }
        if(isset($_REQUEST['game_status'])){
            $extend_map['tab_game.game_status']=$_REQUEST['game_status'];
            unset($_REQUEST['game_status']);
        }
        if(isset($_REQUEST['giftbag_name'])){
            $extend_map['tab_giftbag.giftbag_name']=$_REQUEST['giftbag_name'];
            unset($_REQUEST['giftbag_name']);
        }

        //礼包过期状态
        if(isset($_REQUEST['expire_status'])){
            //已过期
            if($_REQUEST['expire_status'] == 0) {
                $extend_map['tab_giftbag.end_time'] = array("lt", time());
            //未过期
            } else if($_REQUEST['expire_status'] == 1) {
                $extend_map['tab_giftbag.end_time'] = array("gt", time());
            }
            unset($_REQUEST['expire_status']);
        }
     
        //parent::lists(self::model_name,$_GET["p"],$map);
        $model=self::model_name;
        $p=$_GET["p"];
        $model || $this->error('模型名标识必须！');
        $page = intval($p);
        $page = $page ? $page : 1; //默认显示第一页数据

        //获取模型信息
        
        $model = M('Model')->getByName($model);
        $model || $this->error('模型不存在！');
        //解析列表规则
        $fields = array();
        $grids  = preg_split('/[;\r\n]+/s', trim($model['list_grid']));
        foreach ($grids as &$value) {
            if(trim($value) === ''){
                continue;
            }
            // 字段:标题:链接
            $val      = explode(':', $value);
            // 支持多个字段显示
            $field   = explode(',', $val[0]);
            $value    = array('field' => $field, 'title' => $val[1]);
            if(isset($val[2])){
                // 链接信息
                $value['href']  =   $val[2];
                // 搜索链接信息中的字段信息
                preg_replace_callback('/\[([a-z_]+)\]/', function($match) use(&$fields){$fields[]=$match[1];}, $value['href']);
            }
            if(strpos($val[1],'|')){
                // 显示格式定义
                list($value['title'],$value['format'])    =   explode('|',$val[1]);
            }
            foreach($field as $val){
                $array  =   explode('|',$val);
                $fields[] = $array[0];
            }
        }
        // 过滤重复字段信息
        $fields =   array_unique($fields);
        // 关键字搜索
        $map    =   $extend_map;
        $key    =   $model['search_key']?$model['search_key']:'title';
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
        $row    = empty($model['list_row']) ? 15 : $model['list_row'];

        //读取模型数据列表
      
            /* 查询记录数 */
            $count = M('Giftbag','tab_')->join("INNER JOIN tab_game ON tab_giftbag.game_id = tab_game.id")->where($map)->count();

            // 查询数据
            $data   = M('Giftbag','tab_')
                ->join("INNER JOIN tab_game ON tab_giftbag.game_id = tab_game.id")
                /* 查询指定字段，不指定则查询所有字段 */
                ->field('tab_giftbag.*,tab_game.game_status')
                // 查询条件
                ->where($map)
                /* 默认通过id逆序排列 */
                ->order("tab_giftbag.id DESC")
                /* 数据分页 */
                ->page($page, $row)
                /* 执行查询 */
                ->select();


        //分页
        if($count > $row){
            $page = new \Think\Page($count, $row);
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $this->assign('_page', $page->show());
        }

        $data   =   $this->parseDocumentList($data,$model['id']);
        $this->assign('model', $model);
        $this->assign('list_grids', $grids);
        $this->assign('list_data', $data);
        //print_r($data);exit;
        $this->meta_title = $model['title'].'列表';
        $this->display($model['template_list']);

        

    }
    public function record(){
        if(isset($_REQUEST['game_name'])){
            if($_REQUEST['game_name']=='全部'){
                unset($_REQUEST['game_name']);
            }else{
                $extend['game_name']=$_REQUEST['game_name'];
                unset($_REQUEST['game_name']);
            }
        }
        if(isset($_REQUEST['user_account'])){
            $extend['user_account']=array('like','%'.$_REQUEST['user_account'].'%');
            unset($_REQUEST['user_account']);
        }
        parent::lists('GiftRecord',$_GET["p"],$extend);
    }

    public function add(){
        if(IS_POST){
            $Model  =   D('Giftbag');
            // 获取模型的字段信息
            $Model  =   $this->checkAttr($Model,$model['id']);
            $data = $Model->create();
            if($data){
                $data['novice'] = str_replace(array("\r\n", "\r", "\n"), ",", $_POST['novice']);  
                $data['server_name']=get_server_name($data['server_id']);
                $Model->add($data);

                //清除礼包数据缓存
                $this->_clear_cache();
                
                $this->success('添加'.$model['title'].'成功！', U('lists?model='.$model['name']));
            } else {
                $this->error($Model->getError());
            }
        } else {
            $this->display('add');
        }
    }

    public function edit($id=0){
		$_REQUEST['id'] || $this->error('请选择要编辑的用户！');
		$model = M('Model')->getByName(self::model_name); /*通过Model名称获取Model完整信息*/
		//获取模型信息
        $model = M('Model')->find($model['id']);
        $model || $this->error('模型不存在！');

        if(IS_POST){
            $Model  =   D(parse_name(get_table_name($model['id']),1));
            // 获取模型的字段信息
            $Model  =   $this->checkAttr($Model,$model['id']);
            $data = $Model->create();
            if($data){
                //修改不更新创建时间
                if(isset($data['create_time'])) {
                    unset($data['create_time']);
                }
                $data['novice'] = str_replace(array("\r\n", "\r", "\n"), ",", $_POST['novice']);
                $Model->save($data);

                //清除礼包数据缓存
                $this->_clear_cache();

                $this->success('保存'.$model['title'].'成功！', U('lists?model='.$model['name']));
            } else {
                $this->error($Model->getError());
            }
        } else {
            $fields     = get_model_attribute($model['id']);
            //获取数据
            $data       = D(get_table_name($model['id']))->find($id);
            $data || $this->error('数据不存在！');

            $this->assign('model', $model);
            $this->assign('fields', $fields);
            $this->assign('data', $data);
            $this->meta_title = '编辑'.$model['title'];
            $this->display($model['template_edit']?$model['template_edit']:'');
        }
    }

    public function del($model = null, $ids=null){
        $model = M('Model')->getByName(self::model_name); /*通过Model名称获取Model完整信息*/
        parent::del($model["id"],$ids);

        //清除礼包数据缓存
        $this->_clear_cache();
    }

    public function get_ajax_area_list(){
    	$area = D('Server');
    	$map['game_id'] = I('post.game_id',1);
    	$list = $area->where($map)->select();
    	$this->ajaxReturn($list);
    }
    /**
     * 激活码列表展示
     * @author  whh 
     */
   public function exchange_list(){ 
        $map=array();
        if(isset($_REQUEST['title'])){
            $map['title'] = $_REQUEST['title'];
        }
        //print_r($_REQUEST['forbid_ip']);exit;
       
        $row=10;
        $page = intval($_GET['p']);
        $page = $page ? $page : 1; //默认显示第一页数据
        $model=M('exchange','tab_');
        $data=$model
        ->where($map)
        ->order('id desc')
        ->page($page, 10)
        ->select();
        //print_r($data);exit;
        $count=$model
        ->where($map)
        ->count();
        if($count > $row){
            $page = new \Think\Page($count, $row);
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $this->assign('_page', $page->show());
        }
        $this->assign('_list', $data);
        $this->meta_title = 'IP信息';
        $this->display();
    }
    /**
     * 激活码添加
     * @author  whh 
     */
    public function exchange_add(){
        
        if(IS_POST){
                $data=$_REQUEST;
                $data['start_time']=strtotime($_REQUEST['start_time']);
                $data['end_time']=strtotime($_REQUEST['end_time']);
                $data['create_time']=time();
                $data['novice'] = str_replace(array("\r\n", "\r", "\n"), ",", $_POST['novice']); 
                //print_r($data);exit; 
                $model=M('exchange','tab_');
                $return=$model->add($data);
                if ($return) {
                    $this->success('激活码添加成功！',U('exchange_list'));
                } else {
                    $this->error('激活码添加失败！',U('exchange_list'));
                }
                
           
        } else {
            $this->display('exchange_add');
        }
    }
    
    /**
     * 激活码编辑
     * @author  whh 
     */
    public function exchange_edit($id=0){
        $_REQUEST['id'] || $this->error('请选择要编辑的用户！');
        if(IS_POST){
           
                $model=M('exchange','tab_');
                $data=$_REQUEST;
                $data['start_time']=strtotime($_REQUEST['start_time']);
                $data['end_time']=strtotime($_REQUEST['end_time']);
                $data['novice'] = str_replace(array("\r\n", "\r", "\n"), ",", $_POST['novice']);
                $return=$model->save($data);
                if ($return) {
                    $this->success('激活码编辑成功！',U('exchange_list'));
                } else {
                    $this->error('激活码编辑失败！',U('exchange_list'));
                }
           
        } else {

            $data = M('exchange','tab_')->find($id);
            $this->assign('data', $data);
            $this->display();
        }
    }

     /**
     * 激活码删除
     * @author  whh 
     */
     public function exchange_del($id) {
        if (empty($id)) {
             $this->error('请选择要删除的激活码！');
        } else {
            $where['id']=$id;
            $return=M('exchange','tab_')->where($where)->delete();
            if ($return) {
                $this->success('激活码删除成功！',U('exchange_list'));
            } else {
                $this->error('激活码删除失败！',U('exchange_list'));
            }
            
        }      
          
     }

     /**
     * 激活码领取记录
     * @author  whh 
     */
     public function exchange_record(){
         if(isset($_REQUEST['user_account'])){
            $map['user_account']=array('like','%'.$_REQUEST['user_account'].'%');
            unset($_REQUEST['user_account']);
        }
        if(isset($_REQUEST['prefix'])){
            $map['cdkey']=array('like','%'.$_REQUEST['prefix'].'%');
            unset($_REQUEST['prefix']);
        }

        $row=10;
        $page = intval($_GET['p']);
        $page = $page ? $page : 1; //默认显示第一页数据
        $model=M('exchange_record','tab_');
        $data=$model
        ->where($map)
        ->order('id desc')
        ->page($page, 10)
        ->select();
        //print_r($data);exit;
        $count=$model
        ->where($map)
        ->count();
        if($count > $row){
            $page = new \Think\Page($count, $row);
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $this->assign('_page', $page->show());
        }
        $this->assign('_list', $data);
        $this->meta_title = 'IP信息';
        $this->display();

     }

    //清除礼包数据缓存
    private function _clear_cache() {
        $cache = Memcache::instance();

        //清除首页游戏礼包
        $key = "media_index_game_gift";
        $cache->rm($key);
    }
}
