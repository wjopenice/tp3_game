<?php
namespace Android\Controller;

class GiftController extends BaseController
{
	public function search_list()
	{
		$game_id = I('post.game_id');
		$user_id = I('post.user_id');
		
		if(empty($game_id))
		{
			$this->output(-901);
		}

		$page = I('post.page');
		$page_size = I('post.page_size');

		$page = $page ? $page : 1;
		$page_size = $page_size ? $page_size : 10;

		//查询用户已领取的礼包
		$gift_record_data = array();
		if(!empty($user_id))
		{
			$gift_record_where = array();
			$gift_record_where['user_id'] = $user_id;
			$gift_record_where['game_id'] = $game_id;

			$gift_record_data = D('GiftRecord')->search_gift_record_list($gift_record_where);
		}

		//查询礼包条件
		$gift_where = array();
		$gift_where['game_id'] = $game_id;

		$gift_field = 'id as gift_id,game_name,game_id,giftbag_name,digest,desribe,novice';
		$gift_data = D('Giftbag')->search_gift_list($gift_where, $gift_field, $page, $page_size);
		if($gift_data['list']) 
		{
			foreach($gift_data['list'] as $key => $value)
			{
				//礼包剩余
				$gift_code_num = count(explode(",", $value['novice']));
				$value['gift_code_num'] = $gift_code_num;
				unset($value['novice']);

				//用户是否领取
				$gift_receive_status = 0;
				if(!empty($gift_record_data[$value['gift_id']]))
				{
					$gift_receive_status = 1;
				}
				$value['gift_receive_status'] = $gift_receive_status;

				$gift_data['list'][$key] = $value;
			}
		}

		$this->output($gift_data);
	}

	public function detail()
	{
		$gift_id = I('post.gift_id');
		$user_id = I('post.user_id');

		if(empty($gift_id))
		{
			$this->output(-901);
		}

		$gift_field = 'id,game_name,game_id,giftbag_name,start_time,end_time,digest,desribe,novice';
		$gift_info = D('Giftbag')->get_gift_info($gift_id, $gift_field);
		if(empty($gift_info))
		{
			$this->output(-903);
		}

		//查询用户已领取的礼包
		$gift_receive_code = '';
		$gift_receive_status = 0;
		if(!empty($user_id))
		{
			$gift_record_where = array();
			$gift_record_where['user_id'] = $user_id;
			$gift_record_where['gift_id'] = $gift_id;

			$gift_record_data = D('GiftRecord')->search_gift_record_list($gift_record_where);
			if(!empty($gift_record_data[$gift_id])) {
				$gift_receive_code = $gift_record_data[$gift_id]['novice'];
				$gift_receive_status = 1;
			}
		}
		$gift_info['gift_receive_code'] = $gift_receive_code;
		$gift_info['gift_receive_status'] = $gift_receive_status;

		//剩余礼包码数量
		$gift_code_num = 0;
		if(!empty($gift_info['novice']))
		{
			$gift_code_num = count(explode(",", $gift_info['novice']));
			unset($gift_info['novice']);
		}
		$gift_info['gift_code_num'] = $gift_code_num;

		//游戏详情信息
		$game_field = 'icon,game_size';
		$game_info = D('Game')->type_to_select('id', $gift_info['game_id'], '', '', $game_field);
		$game_detail = $game_info[0];

		//游戏图标
		$game_icon = '';
		$game_size = '';
		if(!empty($game_detail['icon']))
		{
			$game_icon = $game_detail['icon'];
			$game_size = $game_detail['game_size'];
		}
		$gift_info['game_icon'] = $game_icon;
		$gift_info['game_size'] = $game_size;

		$this->output($gift_info);
	}
}
