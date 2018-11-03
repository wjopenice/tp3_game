<?php

namespace Admin\Controller;
use User\Api\UserApi as UserApi;
use Org\Util\Memcache as Memcache;

/**
 * 后台首页控制器
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
class GameController extends ThinkController {
    //private $table_name="Game";
    const model_name = 'game';

    /**
    *游戏信息列表
    */
    public function lists(){

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
        parent::lists(self::model_name,$_GET["p"],$extend);
    }

    /**
    *游戏原包列表
    */
    public function source(){
        $extend = array('field_time'=>'create_time');
        parent::lists('Source',$_GET["p"],$extend);
    }

    /**
    *游戏更新列表
    */
    public function update(){
        parent::lists('Update',$_GET["p"]);
    }

    /**
    *添加游戏原包
    */
    public function add_source(){
        if(IS_POST){
            if(empty($_POST['game_id']) || empty($_POST['file_type'])){
                $this->error('游戏名称或类型不能为空');
            }
            $map['game_id']=$_POST['game_id'];
            $map['file_type'] = $_POST['file_type'];
            $d = D('Source')->where($map)->find();
            $source = A('Source','Event');
            if(empty($d)){
                $source->add_source();
            }
            else{
                $source->update_source($d['id']);
            }
        }
        else{

            $this->display();
        }
    }

    /**
    *删除原包
    */
    public function del_game_source($model = null, $ids=null){
        $source = D("Source");
        $id = array_unique((array)$ids);
        $map = array('id' => array('in', $id) );
        $list = $source->where($map)->select();
        foreach ($list as $key => $value) {
            $file_url = APP_ROOT.$value['file_url'];
            unlink($file_url);
        }
        $model = M('Model')->getByName("source"); /*通过Model名称获取Model完整信息*/
        parent::del($model["id"],$ids,"tab_game_");
    }

    public function add(){

    	if(IS_POST){

    		$game   =   D(self::model_name);//M('$this->$model_name','tab_');
	        $res = $game->update();  
	        if(!$res){
	            $this->error($game->getError());
	        }else{

                //清除游戏数据缓存
                $this->game_cache_clear();

	            $this->success($res['id']?'更新成功':'新增成功',session('back_url'));
	        }
    	}
    	else{
            session('back_url',$_SERVER['HTTP_REFERER']);
    		$this->display();
    	}
    	
    }

    public function edit($id=null){
        if(IS_POST){
            $game   =   D(self::model_name);//M('$this->$model_name','tab_');
            //获取编辑数据
            $game_data = I('post.');
            //file_put_contents('/home/wwwroot/test.898sm.com/Data/bbbbb.log',$game_data['bind_discount'].'-----'.$game_data['id']);
            $map['id'] = $game_data['id'];
            $bind_discount = M('game','tab_')->where($map)->find();

            if(($game_data['bind_discount'] != $bind_discount['bind_discount']) && $game_data['bind_discount']>=3)
            {   
                //file_put_contents('/home/wwwroot/test.898sm.com/Data/bbbbb.log',$bind_discount['bind_discount']);
                $save['discount'] = $game_data['bind_discount'];
                $mapchr['game_id'] = $game_data['id'];
                $result = M('charge','tab_')->where($mapchr)->save($save);
            }

            $res = $game->update();  
            if(!$res){
                $this->error($game->getError());
            }else{

                //清除游戏数据缓存
                $this->game_cache_clear();

                $this->success($res['id']?'更新成功':'新增成功',session('back_url'));
            }

        }
        else{
            session('back_url',$_SERVER['HTTP_REFERER']);
            $id || $this->error('id不能为空');
            $data = D(self::model_name)->detail($id);
            $data || $this->error('数据不存在！');
            $this->assign('data', $data);
            $this->meta_title = '编辑游戏';
            $this->display();
        }
    }

    public function set_status($model='Game'){
        parent::set_status($model);

        //清除游戏数据缓存
        $this->game_cache_clear();
    }

    public function del($model = null, $ids=null){
        $model = M('Model')->getByName(self::model_name); /*通过Model名称获取Model完整信息*/
        parent::remove($model["id"],'Set',$ids);

        //清除游戏数据缓存
        $this->game_cache_clear();
    }

    //开放类型
    public function openlist(){
        $extend = array(
        );
        parent::lists("opentype",$_GET["p"],$extend);
    }
    //新增开放类型
    public function addopen(){
        if(IS_POST){
            $game=D("opentype");
        if($game->create()&&$game->add()){
            $this->success("添加成功",U('openlist'));
        }else{
            $this->error("添加失败",U('openlist'));
        }
        }else{
            $this->display();
        }
        
    }
    //编辑开放类型
    public function editopen($ids=null){
          $game=D("opentype");
        if(IS_POST){
        if($game->create()&&$game->save()){
             $this->success("修改成功",U('openlist'));
        }else{
           $this->error("修改失败",U('openlist'));
        }
        }else{  
         $map['id']=$ids;
            $date=$game->where($map)->find();
            $this->assign("data",$date);
            $this->display();
        }
    }
    //删除开放类型
    public function delopen($model = null, $ids=null){
       $model = M('Model')->getByName("opentype"); /*通过Model名称获取Model完整信息*/
        parent::del($model["id"],$ids);
    }
    /**
     * 文档排序
     * @author huajie <banhuajie@163.com>
     */
    public function sort(){
        //获取左边菜单$this->getMenus()
       
        if(IS_GET){
            $map['game_status'] = 1;
            $map['recommend_status']=array('neq','0');
            $list = D('Game')->where($map)->field('id,game_name,game_type_name,recommend_status,sort')->order('sort DESC, id DESC')->select();
            //var_dump($list);exit;
            $this->assign('list', $list);
            $this->meta_title = '游戏排序';
            $this->display();
        }elseif (IS_POST){
            $ids = I('post.ids');
            //print_r($ids);exit;
            $ids = array_reverse(explode(',', $ids));
            foreach ($ids as $key=>$value){
                $res = D('Game')->where(array('id'=>$value))->setField('sort', $key+1);
            }
            if($res !== false){
                //清除游戏数据缓存
                $this->game_cache_clear();

               $this->success('排序成功！');
            }else{
                $this->error('排序失败！');
            }
        }else{
            $this->error('非法请求！');
        }
    }

    //清除游戏数据缓存
    private function game_cache_clear() {
        $cache = Memcache::instance();

        //清除首页取所有游戏列表
        $key = "media_all_game_list";
        $cache->rm($key);
    }

}
