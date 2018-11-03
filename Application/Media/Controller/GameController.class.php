<?php
namespace Media\Controller;
use Think\Controller;
use Admin\Model\GameModel;

class GameController extends BaseController {
     

    public function youxi(){
        $map['game_status'] = 1;
        $p = I('get.p');
        $p = empty($p)?1:$p;
        $row=I('get.pagesize');
        $row = empty($row)?18:$row;
        $model = array(
            'm_name' => 'Game',
            'prefix' => 'tab_',
            'map' => $map,
            'order' => 'sort DESC',
            'template_list' => 'Game/youxi'
        );
        $this->slider_game();

        $this->rank();
        parent::lists($model,$p,$row);
    }

    public function yx_fenlei($game_type=0,$p=0,$id=0){ 
        if (IS_POST) {
            $map['game_status'] = 1;
            if ($_POST['game_type']) {
               $map['game_type_id'] = $_POST['game_type'];
            }
            $model=M('game','tab_');
            $data=$model->where($map)->order('sort DESC')->limit(20)->select();
            $count=$model->where($map)->count('id');
            foreach ($data as $k => &$v) {
                $data[$k]['game_image']=__ROOT__.get_cover($v['icon'],'path');
            }
            $data1=json_encode((object)$data);
            //file_put_contents('E:/bbbbb.html',$data1);
            echo $data1;
        }else {
           
        $map['game_status'] = 1;
         if ($_GET['type']) {
               $map['game_type_id'] = $_GET['type'];
            }
        if ($_GET['p']) {
               $p = $_GET['p'];
            }else{
                $p=1;
            }
        if ($_GET['pagesize']) {
               $row = $_GET['pagesize'];
            }else{
               $row = 20; 
            }
            $keyword = I('get.keyword');
        if($keyword){

                $map['game_name'] = array('like',"%".$keyword."%");
            }
        $model = array(
            'm_name' => 'Game',
            'prefix' => 'tab_',
            'map' => $map,
            'order' => 'sort desc',
            'template_list' => 'Game/yx_fenlei'
        );
        
        
        parent::lists($model,$p,$row);
        
    }
}    
    


    public function yxchildlb(){                           
        $this->display();
    }
   
   /**
  * 游戏详情页面
  * use Admin\Model\GameModel;
  */
   public function yxchildren($id = 0, $p = 1){
        /* 获取详细信息 */
        $game = new GameModel();
        $game->detail();
        $info = $game->detail($id);
        if(!$info){
            $this->error($game->getError());
        }
        $tmpl = 'yxchildren';
        $this->assign('vo', $info);
        $this->assign('page', $p);
        $this->rank();
        $GiftController = new GiftController();
        $GiftController->giftdetail();
        $this->display($tmpl);
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
            'limit'=>4,
        );
        $giftdetail = parent::join_data($model);
        
        $this->assign('giftdetail',$giftdetail);
    }



   
   

        /**
     *最新礼包
     */
    public function gift(){
        $p = I('get.p');
        $model = array(
            'm_name'=>'Giftbag',
            'prefix'=>'tab_',
            'field' =>'tab_giftbag.id as gift_id,game_id,tab_giftbag.game_name,giftbag_name,tab_game.icon,desribe',
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

    public function dow_url_generate($game_id=null){
        $url = "http://".$_SERVER['SERVER_NAME']."/media.php?s=/Appdw/gamedown/game_id/".$game_id;
        $qrcode = $this->qrcode(base64_encode($url));
        return $qrcode;
    }

    public function qrcode($url='pc.vlcms.com',$level=3,$size=4){
        Vendor('phpqrcode.phpqrcode');
        $errorCorrectionLevel =intval($level) ;//容错级别 
        $matrixPointSize = intval($size);//生成图片大小 
        $url = base64_decode($url);
        //生成二维码图片 
        //echo $_SERVER['REQUEST_URI'];
        $object = new \QRcode();
        echo $object->png($url, false, $errorCorrectionLevel, $matrixPointSize, 2);   
    }





    /**
     *游戏排行
     */
    public function rank(){
        //echo 1111;
        $model = array(
            'm_name'=>'Game',
            'prefix'=>'tab_',
            'map'   =>array('game_status'=>1),
            'field' =>true,
            'order' =>'dow_mynum desc',
            'limit' =>10,
        );
        $rank = parent::list_data($model);
        $this->assign('rank',$rank);
        //print_r($rank);exit;
        $this->assign('num',1);
    }


     /*
      *  游戏页轮播图
      *  @author   whh
      */

   public function slider_game(){
        $adv = M("Adv","tab_");
        $map['status'] = 1;
        $map['pos_id'] = 5; #游戏页轮播小图id
        $slidersmall= $adv->where($map)->order('sort ASC')->select();
        $map1['status'] = 1;
        $map1['pos_id'] = 6;#游戏页轮播大图id
        $sliderbig= $adv->where($map1)->order('sort ASC')->select();
        foreach ($sliderbig as $key => &$value) {
            $value['small_title'] = $slidersmall[$key]['title'];
            $value['small_data'] = $slidersmall[$key]['data'];
            $value['small_url'] = $slidersmall[$key]['url'];
        }
        //print_r($sliderbig);exit;
        $this->assign("sliderbig",$sliderbig);
        //$this->assign("slider_data",$slider_data);
    }

   

}