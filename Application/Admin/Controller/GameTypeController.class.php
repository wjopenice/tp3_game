<?php

namespace Admin\Controller;
use User\Api\UserApi as UserApi;
/**
 * 后台首页控制器
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
class GameTypeController extends ThinkController {
    const model_name = 'GameType';

    public function lists(){
        parent::lists(self::model_name,$_GET["p"],$extend);
    }

    public function add($model='')
    {
        parent::add($model);
    }
    
    public function edit($model='',$id=0)
    {
        parent::edit($model,$id);
    }

    public function del($model = null, $ids=null)
    {
        $model = M('Model')->getByName(self::model_name); /*通过Model名称获取Model完整信息*/
        parent::del($model["id"],$ids);
    }

    public function set_status()
    {
        parent::set_status(self::model_name);
    }
     /**
     * 游戏类型排序
     * @author 采蘑菇的巳寸
     */
    public function sort(){
        //获取左边菜单$this->getMenus()
        if(IS_GET){
            if(I('get.type')){
                $where['status'] = 1;
                $sort = 'app_sort';
            }else{
                $where['status_show'] = 1;
                 $sort = 'sort';
            }
            $list = D('Game_type')->where($where)->field('id,type_name')->order($sort. ' DESC, id DESC')->select();
            //var_dump($list);exit;
            $this->assign('list', $list);
            $this->meta_title = '游戏排序';
            $this->display();
        }elseif (IS_POST){
            $ids = I('post.ids');
            $ids = explode(',', $ids);
            if(I('post.type')){
                $field = 'app_sort';
            }else{
                $field = 'sort';
            }
            foreach ($ids as $key=>$value){
                $res = D('GameType')->where(array('id'=>$value))->setField($field, $key+1);
            }
            if($res !== false){
                $this->success('排序成功！');
            }else{
                $this->error('排序失败！');
            }
        }else{
            $this->error('非法请求！');
        }
    }

}
