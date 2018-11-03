<?php

namespace Media\Controller;
use OT\DataDictionary;
use User\Api\MemberApi;

/**
 * 前台首页控制器
 * 主要获取首页聚合数据
 */
class AppdwController extends BaseController {
	  //APP
      public function requ(){
      	$this->display();
      }
      //官网
      public function gamedown($game_id){
        $game_id=I('get.game_id');
        $this->assign('game_id',$game_id);
        $this->display();
      }
      //渠道
       public function promote_gamedown($promote_id,$game_id){
      	$game_id=I('get.game_id');
        $this->assign('game_id',$game_id);
        $promote_id=I('get.promote_id');
        $this->assign('promote_id',$promote_id);
        /*$url="test.898sm.com/index.php?s=/Home/Down/down_file/game_id/".$game_id."/promote_id/".$promote_id;*/
        //$this->assign('url',$url);
        $this->display();
      }

}