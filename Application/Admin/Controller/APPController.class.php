<?php

namespace Admin\Controller;
use User\Api\UserApi as UserApi;
/**
 * 后台首页控制器
 * @author whh <zuojiazi@vip.qq.com>
 */
class APPController extends ThinkController {
        //app图片管理页面展示
        public function index(){
            
            $row=10;
            $page = intval($_GET['p']);
            $page = $page ? $page : 1; //默认显示第一页数据
            $map['del']=0;
            $model=M('appimage','tab_');
            $data=$model
            ->field('id,title,game_id,sort,status,location,create_time')
            ->where($map)
            ->page($page, 10)
            ->select();
            $count=$model
            ->field('id,title,game_id,sort,status,location,create_time')
            ->where($map)
            ->count();
            if($count > $row){
                $page = new \Think\Page($count, $row);
                $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
                $this->assign('_page', $page->show());
            }
            $this->assign('list_data', $data);
            $this->display();    
            }
        //app图片新增
        public function img_add()
        {
            if (IS_POST)
            {
                $data=$_REQUEST;
                switch ($data['adv_type'])
                {
                    case 2:
                        $data['adv_url'] = 'http://'.$_SERVER['HTTP_HOST'].'/android.php?s=Index/adv_detail/id/'.$data['adv_jump_id'];
                        break;
                    
                    default:
                        # code...
                        break;
                }
                $data['create_time']=time();
                $appimage=M('appimage','tab_');
                $res=$appimage->add($data);
                if (!$res)
                {
                    $this->error($appimage->getError());
                }
                else
                {
                    $this->success('新增成功',U('index'));
                }
                
            }
            else
            {
                $this->display();
            }
            
        }
        //APP图片编辑
        public function img_edit()
        {
            $appimage=M('appimage','tab_');
            if (IS_POST)
            {
                $data=$_REQUEST;
                switch ($data['adv_type'])
                {
                    case 2:
                        $data['adv_url'] = 'http://'.$_SERVER['HTTP_HOST'].'/android.php?s=Index/adv_detail/id/'.$data['adv_jump_id'];
                        break;
                    
                    default:
                        # code...
                        break;
                }
                $data['create_time']=time();
                $appimage=M('appimage','tab_');
                $res=$appimage->where(array('id'=>$_REQUEST['id']))->save($data);
                if ($res===false)
                {
                    $this->error($appimage->getError());
                } 
                else 
                {
                    $this->success('编辑成功',U('index'));
                }
                
            }
            else
            {
                $edit_data=$appimage
                ->where(array('id'=>$_REQUEST['ids']))
                ->find();
                $this->assign('edit_data',$edit_data);
                $this->display();
            }
            
        }
        //APP图片删除
        public function img_del(){
            $appimage=M('appimage','tab_');
            if(empty($_REQUEST['ids'])){
            $this->error('请选择要操作的数据');
            }
            if(isset($_REQUEST['ids'])){
                $id=$_REQUEST['ids'];
            }
             $appimage
             ->where(array("id"=>$id))
             ->setField('del','1');
             $this->success("删除成功！",U('index'));

        }
        /**
         * @author 采蘑菇的巳寸
         * APP游戏类型管理
         */
        public function game_type_list(){
            $row=10;
            $page = intval($_GET['p']);
            $page = $page ? $page : 1; //默认显示第一页数据
            $map['status']=1;
            $model=M('Game_type','tab_');
            $data=$model
            ->field('id,type_name,status,app_sort,create_time')
            ->where($map)
            ->page($page, 10)
            ->select();
            $count=$model
            ->field('id,type_name,status_show,sort,create_time')
            ->where($map)
            ->count();
            if($count > $row){
                $page = new \Think\Page($count, $row);
                $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
                $this->assign('_page', $page->show());
            }
            $this->assign('list_data', $data);
            $this->display();    
        }
        /**
         * @author 采蘑菇的巳寸
         * APP游戏类型添加
         */
        public function game_type_add(){
            if(IS_POST){
                $game_type = M('Game_type','tab_');
                $game_type->create();
                $user_info = session('user_auth');
                $game_type->op_id=$user_info['uid'];
                $game_type->op_nickanme=$user_info['user_name'];
                $game_type->create_time=time();
                $res = $game_type->add();
                if($res){
                     $this->success("添加成功！",U('game_type_list'));
                }else{
                     $this->error("添加失败！",U('game_type_list'));   
                }
            }else{
                
              $this->display();  
            }
               
        }
        /**
        * @author 采蘑菇的巳寸
        * APP游戏类型编辑
        */
        public function game_type_edit(){
            if(IS_POST){
                $game_type = M('Game_type','tab_');
                $game_type->create();
                $user_info = session('user_auth');
                $game_type->op_id=$user_info['uid'];
                $game_type->op_nickanme=$user_info['user_name'];
                $game_type->create_time=time();
                $res = $game_type->save();
                 if($res){
                     $this->success("编辑成功！",U('game_type_list'));
                }else{
                     $this->error("编辑失败！",U('game_type_list'));   
                } 
            }else{
                $game_type['id'] =I('get.ids'); 
                $data = M('game_type','tab_')->where($game_type)->find();
                if(!$data){
                    $this->error("获取模型数据出错!");   
                }
                $this->assign('data',$data);
                $this->display();    
            }
             
        }
        /**
        * @author 采蘑菇的巳寸
        * APP游戏类型删除
        */
        public function game_type_del(){
              $game_type['id'] =I('get.ids'); 
              $res = M('Game_type','tab_')->where($game_type)->delete();
              if($res){
                $this->success('APP游戏类型删除成功');
              }else{
                $this->error('APP游戏类型删除失败');
              }
        }
        /**
        * @author 采蘑菇的巳寸
        * APP游戏状态列表
        */
       public function game_status_lists(){
            if(isset($_REQUEST['app_recommend_status'])){
            $map['app_recommend_status']=$_REQUEST['app_recommend_status'];
            unset($_REQUEST['app_recommend_status']);
            }
            if(isset($_REQUEST['game_id'])){
               if($_REQUEST['game_id']=='全部'){
                  }else{
                $map['id'] = $_REQUEST['game_id'];
               }
               unset($_REQUEST['game_id']);
            }
            if(isset($_REQUEST['game_type_name'])){
            if($_REQUEST['game_type_name']=='全部'){
            }else{
                $map['game_type_name'] = $_REQUEST['game_type_name'];
            }
             unset($_REQUEST['game_type_name']);
            }
            $row=10;
            $page = intval($_GET['p']);
            $page = $page ? $page : 1; //默认显示第一页数据
            $map['game_status']  = 1;
            $model=M('Game','tab_');
            $data=$model
            ->field('id,game_name,game_type_name,app_recommend_status,app_sort,create_time')
            ->where($map)
            ->page($page, 10)
            ->order('id desc')
            ->select();
            $count=$model
            ->field('id')
            ->where($map)
            ->count();
            if($count > $row){
                $page = new \Think\Page($count, $row);
                $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
                $this->assign('_page', $page->show());
            }
            $this->assign('list_data', $data);
            $this->display();   
       }
       /**
        * @author 采蘑菇的巳寸
        * 游戏显示状态编辑
        */
       public function game_status_edit(){
            $where['id'] = I('post.id');
            $data['app_recommend_status'] = I('post.status');
            $data['app_sort'] = I('post.sort');
            $result = M('Game','tab_')->where($where)->save($data);
            if($result){
                echo json_encode(array('status'=>1,'msg'=>'ok'));
            }else{
                echo json_encode(array('status'=>0,'msg'=>'编辑失败'));
            }
       }
       /**
        * @author 采蘑菇的巳寸
        * 资讯列表
        */
       public function app_information_lists(){
            session('back_url',$_SERVER['HTTP_REFERER']);
            $row=15;
            $page = intval($_GET['p']);
            $page = $page ? $page : 1; //默认显示第一页数据
            $where['status'] = 1;
            $where['category_id'] = I('get.category_id');
            $model=M('Document');
            $data=$model
            ->field('id,title,type,update_time,status,admin,view,category_id')
            ->where($where)
            ->page($page, 15)
            ->order('id desc')
            ->select();
            $count=$model
            ->field('id')
            ->where($where)
            ->count();
            if($count > $row){
                $page = new \Think\Page($count, $row);
                $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
                $this->assign('_page', $page->show());
            }
            $this->assign('list_data', $data);

            $this->display();
       }
        /**
        * @author 采蘑菇的巳寸
        * 资讯列表
        */
       public function app_information_add(){
           

            if(IS_POST){ //提交表单
                 $document   =   D('Document');
                $res = $document->update();
                 if(!$res){
                    $this->error($document->getError());
                }else{
                    $this->success($res['id']?'更新成功':'新增成功',session('back_url'));
                }
            }else{
                $this->display();
            }
            
       }
        /**
        * @author 采蘑菇的巳寸
        * 资讯编辑
        */
       public function app_information_edit(){
            session('back_url',$_SERVER['HTTP_REFERER']);  
            $where['id'] = I('get.ids');  
            $info = M('Document')->where($where)->find();
            $content = M('Document_article')->where($where)->find();
            $this->assign('info',$info);
            $this->assign('content',$content);
            $this->display();
       }
       /**
        * @author 采蘑菇的巳寸
        * 资讯删除
        */
       public function app_information_del(){
            session('back_url',$_SERVER['HTTP_REFERER']);  
            $where['id'] = I('get.ids');  
            $data['status'] = -1;  
            $result = M('Document')->where($where)->save($data);
            if($result !== false){
                 $this->success('删除成功',session('back_url'));
            }else{
                 $this->error('删除失败',session('back_url'));
            }
       }
        /**
        * @author 采蘑菇的巳寸
        * 版本列表
        */
       public function version_list(){
            $list = M('app_version','tab_')->order('serverversion desc')->select();
            //dump($list);exit;
            $this->assign('list_data',$list);
            $this->display();
       }
       /**
        * @author 采蘑菇的巳寸
        * 新增版本
        */
       public function version_add(){
            if(IS_POST){
                $app_version = M('app_version','tab_');
                $app_version->create();
                $app_version->create_time = time();
                $result = $app_version->add();
                if($result){
                    $this->success('版本添加成功',U('version_list'));
                }else{
                    $this->error('版本添加失败');
                }
            }else{
                $this->display();
            }
            
       }
        /**
        * @author 采蘑菇的巳寸
        * 修改版本
        */
       public function version_edit(){
           if(IS_POST){
                $app_version = M('app_version','tab_');
                $app_version->create();
                $result = $app_version->save();
                if($result !== false){
                    $this->success('版本修改成功',U('version_list'));
                }else{
                   
                    $this->error('版本修改失败');
                }
            }else{
                $id = I('get.id');
                $data = M('app_version','tab_')->find($id);
                $this->assign('data',$data);
                $this->display();
            }
       }
        /**
        * @author 采蘑菇的巳寸
        * 删除版本
        */
       public function version_del(){
            $id = I('get.id');
            $result = M('app_version','tab_')->delete($id);
            if($result){
                    $this->success('版本删除成功',U('version_list'));
                }else{
                   
                    $this->error('版本删除失败');
            }
       }

}
