<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Home\Widget;
use Think\Controller;

/**
 * 分类widget
 * 用于动态调用分类信息
 */

class CategoryWidget extends Controller{
	
	/* 显示指定分类的同级分类或子分类列表 */
	public function lists($cate, $child = false){
		$field = 'id,name,pid,title,link_id';
		if($child){
			$category = D('Category')->getTree($cate, $field);
			$category = $category['_'];
		} else {
			$category = D('Category')->getSameLevel($cate, $field);
		}
		$this->assign('category', $category);
		$this->assign('current', $cate);
		$this->display('Category/lists');
	}

	public function apply_game_list($promote_id=0){
		$game = M('Apply',"tab_");
		$map['tab_apply.promote_id'] = $promote_id;
		$map['tab_apply.status'] = 1;
		$map['tab_game.game_status']=1;
		// $map['tab_charge.status'] = 1;
		$data = $game
				->field("tab_apply.*,tab_game.icon,tab_game.game_appid,tab_game.game_name,tab_charge.discount")
				->join("left join tab_game on tab_apply.game_id = tab_game.id")
				->join("LEFT JOIN tab_charge on tab_charge.promote_id=tab_apply.promote_id and tab_apply.game_id = tab_charge.game_id ")
				->where($map)
				->group('game_id,promote_id')
				->order("id DESC")
				->select();
		foreach ($data as $key => $value) {
			if(null==$value['discount']){
				$data[$key]['discount']=0;
			}
		}
		/*echo $game->getlastsql();
		echo "<pre>";
		print_r($data);exit;*/
		$this->assign("list_data",$data);
		$this->display("Category/apply_game_list");
	}
	
}
