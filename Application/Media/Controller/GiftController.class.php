<?php
namespace Media\Controller;
/**
 * 礼包控制器
 *@author zdd
 */
class GiftController extends BaseController{
    public $gameController;
    /**
     * @author zdd
     * 礼包详情
     */
    public function index(){
        $this->gameController = new GameController();
        //游戏排行
        $this->gameController->rank();
        //全部礼包
        $this->game_sort();
        //最新礼包
        $this->gift();
        //礼包页广告图
        $this->libao_gift();
        //推荐礼包
        $this->recommend_gift();
        $this->display();
        
    }
    /**
    *全部游戏礼包
    */
    public function game_sort($game_type=0,$p=0,$id=0){
            $p = I('get.p');
            $p = empty($p)?1:$p;
            $row=I('get.pagesize');
            $row = empty($row)?12:$row;
            $map['game_status']=1;
            if($_GET['type']){
                $map['game_type_id']=$_GET['type'];
            }
            $model=M('game','tab_');
            $data=$model
                ->alias('a')
                ->field('a.id,a.game_type_id ,a.icon,a.game_name,b.game_id,b.giftbag_name,b.id as gift_id')
                ->join("tab_giftbag as b on a.id = b.game_id ")
                ->where($map)
                ->order('a.sort asc')
                ->page("$p,$row")
                ->select();
             $count = $model
                ->alias('a')
                ->field('a.id,a.game_type_id ,a.icon,a.game_name,b.game_id,b.giftbag_name,b.id as gift_id')
                ->join("tab_giftbag as b on a.id = b.game_id ")
                ->where($map)
                ->count();
            $this->assign('data',$data);
            $this->assign('count',$count);
    }
	 /**
     *游戏礼包详情
     */
    public function giftdetail(){
        $id = $_GET['id'];
        $model = array(
            'm_name'=>'Giftbag',
            'prefix'=>'tab_',
            'field' =>'tab_giftbag.id as gift_id,giftbag_name,tab_giftbag.game_name,desribe,tab_giftbag.start_time,tab_giftbag.end_time',
            'map'   =>array('game_status'=>1,'game_id' => $id),
            //'limit' =>4
        );
        $giftdetail = parent::join_data($model);
        /*echo "<pre>";
        print_r($giftdetail);exit;
        echo "</pre>";*/
        $this->assign('giftdetail',$giftdetail);
    }



    /**
     *推荐礼包
     */
    public function recommend_gift(){
         $model = array(
            'm_name'=>'Giftbag',
            'prefix'=>'tab_',
            'field' =>'tab_giftbag.id as gift_id,game_id,tab_giftbag.game_name,giftbag_name,desribe,tab_giftbag.gift_icon',
            'join'    =>'tab_game on tab_giftbag.game_id = tab_game.id',
            'map'   =>array('game_status'=>1),
            'order' =>'tab_giftbag.create_time desc',
            'group' =>'game_name',
           
            'limit' =>4 ,
        );
        $recommend_gift = parent::join_data($model);
        //print_r($recommend_gift);exit;
        $this->assign('recommend_gift',$recommend_gift);
        }
    /**
     *最新礼包
     */
    public function gift(){
        $p = I('get.p');
        $model = array(
            'm_name'=>'Giftbag',
            'prefix'=>'tab_',
            'field' =>'tab_giftbag.id as gift_id,tab_giftbag.game_id,tab_giftbag.game_name,tab_giftbag.giftbag_name,tab_giftbag.desribe,tab_game.icon',
            'join'  =>'tab_game on tab_giftbag.game_id = tab_game.id',
            'map'   =>array('game_status'=>1),
            'group' =>'tab_giftbag.game_id',
            'order' =>'tab_giftbag.id desc',
            'limit' =>6    ,
        );
        $row = 16;
        $gift = parent::join_data($model,$p,$row);
        $this->assign('gift',$gift);
    }


     /*
      *  礼包页广告图
      *  @author   whh
      */
   
   public function libao_gift(){
        $adv = M("Adv","tab_");
        $map['status'] = 1;
        $map['pos_id'] = 7; #礼包页广告图id
        $libao_gift = $adv->where($map)->order('sort ASC')->select();
        $this->assign("libao_gift",$libao_gift);
    }

    


}
