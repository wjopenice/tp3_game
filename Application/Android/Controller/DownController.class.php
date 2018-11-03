<?php
namespace Android\Controller;


class DownController extends BaseController
{
	/**
	 * @author zdd
	 * 游戏官包下载
     */
	public function down_file()
	{
	    $game_id = I('post.game_id');
	    $type = I('post.type');
	    $user_account=I('post.account');
	    //添加app下载记录
	    $sdata['game_id']=$game_id;
	    $wheregame['id']=$game_id;
	    $sdata['game_name']=M('game','tab_')->where($wheregame)->getField('game_name');
	    $sdata['user_account']=$user_account;
	    $whereuser['account']=$user_account;
	    $udata=M('user','tab_')->where($whereuser)->field('id,promote_id,promote_account')->find();
	    $sdata['user_id']=$udata['id'];
	    $sdata['promote_id']=$udata['promote_id'];
	    $sdata['promote_account']=$udata['promote_account'];
	    $sdata['down_way']=$type;//app2
	    $sdata['create_time']= time();
	    $downmodel=M('down_record','tab_')->add($sdata);
	    $model = M('Game','tab_');
	    $where['id'] = $game_id;
	    $model->where($where)->setInc('app_dow_num');
	    $map['tab_game.id'] = $game_id;
	    $data = $model
		        ->field('tab_game_source.*,tab_game.game_name,tab_game.game_address')
		        ->join("left join tab_game_source on tab_game.id = tab_game_source.game_id")->where($map)->find();
	        if(!varify_url($data['game_address']))
	        {
	            $this->output($data);
	        }else
	        {
	            $this->output(-200); 
	        }
	}
	/**
	 * 游戏是否需要更新
	 * 根据官包和渠道包返回对应地址
	 * @author zdd
	 */
	/*public function is_update(){
	    $ids = I('post.ids');
	    if(!$ids)
	    {
	    	$this->output(-901);
	    }
	    $game_arr = json_decode(I('post.ids'),true);
	    if(is_array($game_arr))
	    {
	    	$this->output(-902);
	    }
	    $game = M('Game','tab_');
	    $apply = M('Apply','tab_');
	    $result =array();
	    foreach ($game_arr as $k => $v)
	    {
			$game_id = $v['game_id'];
		    $where['id'] = $game_id;
		    $version = $game->where($where)->field('version,and_dow_address')->find();
		    if($version['version'] != $v['versionName'])
		    {
		    	//判断是否为官包
		        if($v['promote_id'] == 0)
		        {
		        	//返回官包下载地址
		        	$result[$game_id] = $version['and_dow_address'];
		        }else
		        {
		        	//返回渠道包下载地址
			        $pwhere['promte_id'] = $v['promote_id'];
			        $pwhere['game_id'] = $v['game_id'];
			        $down_url = $apply->where($pwhere)->getField('pack_url');
			        $result[$game_id] = $down_url;
		        }
		    }
	    }
	    if(empty($result))
	    {
	    	$this->output(-201);
	    }else
	    {
	    	$this->output($result);
	    }
	}*/
	/**
	 * 根据游戏包名返回需要更新游戏包信息
	 * @param 	$update_info 需要更新的游戏信息
	 * @author zdd
	 */
	public function is_update()
	{
		$apk_pck_name_arr = json_decode(I('post.packageName'),ture);
		$p = (int)I('post.p');
		$size = (int)I('post.size');
		$update_info = array();
		foreach ($apk_pck_name_arr as $key => $value) 
		{

			$where_game_set['apk_pck_name'] = $key;
			$game_id = M('game_set','tab_')->where($where_game_set)->getField('game_id');
			$where_game['id'] = $game_id;
			$game_info = M('game','tab_')->field('version_num,and_dow_address')->where($where_game)->find();
			if($game_info['version_num'] != $value)
			{
				//所有需要更新的游戏id
				$update_game_ids[]= $game_id;
			}
		}
		if(!empty($update_game_ids))
		{
			$where_update['id'] = array('in',implode(',',$update_game_ids));
			$update_info = D('game')->select_game($where_update,$size,$p);
		}
			$this->output($update_info);
	}

}