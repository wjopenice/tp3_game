<?php
namespace Android\Controller;

class GameController extends BaseController
{
	public function classify()
	{
		$type = I('post.type');
		$p = (int)I('post.p');
		$size = (int)I('post.size');
		$data = D('Game')->type_to_select($type,'',$size,$p);
		$this->output($data);
	}

	public function detail()
	{
		$game_id = I('post.game_id');
		if(empty($game_id))
		{
			$this->output(-901);
		}

		//游戏详情信息
		$game_field = 'id,game_name,icon,game_size,introduction,version_num,version,apk_pck_name,dow_mynum,screenshot,game_type_id,and_dow_address';
		$game_info = D('Game')->type_to_select('id', $game_id, '', '', $game_field);
		if(empty($game_info) || !is_array($game_info))
		{
			$this->output(-903);
		}
		$game_detail = $game_info[0];
		//游戏类型名称
		$game_detail['game_type'] = get_game_type($game_detail['game_type_id']);

		//游戏图片
		$game_images = array();
		if(!empty($game_detail['screenshot']))
		{
			$array_screenshot = explode(',', $game_detail['screenshot']);
			foreach($array_screenshot as $k1=> &$v1)
			{
				$game_images[$k1] = get_cover($v1,'path');
			}
		}

		$return_data = array();
		$return_data['game_detail'] = $game_detail;
		$return_data['game_images'] = $game_images;

		$this->output($return_data);
	}
}
