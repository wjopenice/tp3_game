<?php
namespace Media\Controller;
use Admin\Model\GameModel;
use Org\Util\Memcache as Memcache;

class CategoryController extends BaseController {
    private $_cache;
    private $_timeout;
    public function __construct() {
        parent::__construct();
        //初始化并传memcache前缀
        $this->_cache = Memcache::instance('media_');
        //超时时间
        $this->_timeout = 1800;
    }


    public function zixun(){
        $this->notice();
        $this->activity();
        $this->announcement();
        $this->zixun_adv();
        $this->display();
    }


    /**
     *游戏资讯
     */
    public function notice(){
        $model = array(
            'm_name'=>'document',
            'prefix'=>'sys_',
            'field' =>'sys_document.id as notice_id,sys_document.cover_id,sys_document.title,sys_document.create_time,sys_document_article.id,sys_document_article.content',
            'join'	=>'sys_document_article on sys_document.id = sys_document_article.id',
            'map'   =>array('display'=>1,'status'=>1,'category_id' =>43),
            'order' =>'sys_document.create_time desc',
            'limit' =>3  ,
        );
        if ($_GET['p']) {
               $p = $_GET['p'];
            }else{
                $p=0;
            }
        if ($_GET['pagesize']) {
               $row = $_GET['pagesize'];
            }else{
               $row = 10; 
            }
        $notice = parent::join_data_page($model,$p,$row);
        //print_r($notice);exit;
        if(I('get.type') == 43){
            $this->assign('count',$notice['count']);
        }
        unset($notice['count']);
        $this->assign('notice',$notice);
    }

    /**
     *游戏活动
     */
    public function activity(){
        $model = array(
            'm_name'=>'document',
            'prefix'=>'sys_',
            'field' =>'sys_document.id as notice_id,title,cover_id,create_time,sys_document_article.id,content',
            'join'	=>'sys_document_article on sys_document.id = sys_document_article.id',
            'map'   =>array('display'=>1,'status'=>1,'category_id' =>44),
            'order' =>'create_time desc',
            'limit' =>3  ,
        );
        if ($_GET['p']) {
               $p = $_GET['p'];
            }else{
                $p=0;
            }
        if ($_GET['pagesize']) {
               $row = $_GET['pagesize'];
            }else{
               $row = 10; 
            }
      
        $activity = parent::join_data_page($model,$p,$row);
        //修改判断，原以post方式接收，现改为get方式  updateby:sunhao20170707
         if(I('get.type') == 44){
            $this->assign('count',$activity['count']);
        }
        unset($activity['count']);
        $this->assign('activity',$activity);
    }


    /**
     *游戏公告
     */
    public function announcement(){
        $model = array(
            'm_name'=>'document',
            'prefix'=>'sys_',
            'field' =>'sys_document.id as notice_id,title,cover_id,create_time,sys_document_article.id,content',
            'join'	=>'sys_document_article on sys_document.id = sys_document_article.id',
            'map'   =>array('display'=>1,'status'=>1,'category_id' =>42),
            'order' =>'create_time desc',
            'limit' =>3  ,
        );
        if ($_GET['p']) {
               $p = $_GET['p'];
            }else{
                $p=0;
            }
        if ($_GET['pagesize']) {
               $row = $_GET['pagesize'];
            }else{
               $row = 10; 
            }
        $announcement = parent::join_data_page($model,$p,$row);
        //修改判断，原以post方式接收，现改为get方式  updateby:sunhao20170707
         if(I('get.type') == 42 || I('get.type') ==''){
            $this->assign('count',$announcement['count']);
        }
        unset($announcement['count']);
        $this->assign('announcement',$announcement);
    }


    /**
     *资讯详情
     */
    public function zxchildren(){

        $gift = $this->gift();
        $hot = $this->hot();

        $id = $_GET['id'];
        $key = 'zxchildren_'.$id;
        $cache = $this->_cache->get($key);
        if(!$cache){
          $Document = D('Document');
          $info = $Document->detail($id);

          //print_r($id);exit;
          $model =  array(
              'm_name' =>'document',
              'prefix' =>'sys_',
              'field'  =>'sys_document.id as uid,title,admin,create_time,sys_document_article.content',
              'join'	 =>'sys_document_article on sys_document.id = sys_document_article.id',
              'map'    =>array('sys_document.id' => $id),
          );
          $zxchildren = parent::join1_data($model);
          //print_r($zxchildren);exit;
          
          //轮播图
          $adv = M("Adv","tab_");
          $map['status'] = 1;
          $map['pos_id'] = 14; #充值页广告图id
          $slider_zxchild = $adv->where($map)->order('sort ASC')->select();

          $cache['info'] = $info ;
          $cache['zxchildren'] = $zxchildren ;
          $cache['slider_zxchild'] = $slider_zxchild ;
          $this->_cache->set($key,$cache,$this->_timeout);
        }

        $this->assign("slider_zxchild",$cache['slider_zxchild']);
        $this->assign('vo',$cache['zxchildren']);
        $this->assign('info',$cache['info']);
        $this->display();
    }
    public function cache_data(){
      
      $host = '127.0.0.1';
      $port = '11211';
       $mem=new \Memcache();

       $mem->connect($host,$port);
       /*echo 'mem<br />';
       print_r($mem);*/
       $mem->set('key','value',1800);
       
       $items = $mem->getExtendedStats('items');
       /*echo '<br />items<br />';
       print_r($items);*/
       $items=$items["$host:$port"]['items'];
        /*echo '<br />items2<br />';
       print_r($items);*/
       foreach($items as $k=>$v){
          $str=$mem->getExtendedStats("cachedump",$k,0);
          /*echo '<br />str<br />';
          print_r($str);*/
          $line = $str[$host.':'.$port];
          /*echo '<br />line<br />';
          print_r($line);*/
          foreach ($line as $key => $value) {
            echo $key.'=>';
            print_r($mem->get($key));
            echo "<br />";
          }

         
       }
       /*for($i=0,$len=count($items);$i<$len;$i++){
            $number=$items[$i]['number'];
          $str=$mem->getExtendedStats("cachedump",$number,0);
        $line=$str["$host:$port"];
        if( is_array($line) && count($line)>0){
             foreach($line as $key=>$value){
                echo $key.'=>';
                 print_r($mem->get($key));
                 echo "\r\n";
            }
         }
      }*/


    }
     public function get_all_data(){
      $this->_cache->get_all_data();
     }


        /**
     *游戏礼包
     */
    public function gift(){
        $model = array(
            'm_name'=>'Giftbag',
            'prefix'=>'tab_',
            'field' =>'tab_giftbag.id as gift_id,game_id,tab_giftbag.game_name,giftbag_name,giftbag_type,tab_game.icon,tab_giftbag.create_time',
            'join'	=>'tab_game on tab_giftbag.game_id = tab_game.id',
            'map'   =>array('game_status'=>1),
            'group' =>'tab_giftbag.game_id',
            'order' =>'tab_giftbag.id desc',
            'limit' =>6    ,
        );
        $gift = parent::join_data($model);
       /* echo "<pre>";
        print_r($gift);exit;
        echo "</pre>";*/
        $this->assign('gift',$gift);
        return $gift;
    }


    /***
     *热门游戏
     */
    public function hot(){
        $model = array(
            'm_name'=>'Game',
            'prefix'=>'tab_',
            'map'   =>array('game_status'=>1,'recommend_status'=>2),
            'field' =>true,
            'order' =>'sort desc',
            'limit' =>6,
        );
        $hot = parent::list_data($model);
        $this->assign('hot',$hot);
        return $hot;
    }

    public function news($type='') {
        if (empty($type)) {return;}
        $name = 'media_'.$type;
        $news = M("Document")->field("d.id")->table("__DOCUMENT__ as d")
            ->join("__CATEGORY__ as c on(c.id=d.category_id and c.name='$name')",'right')
            ->where("d.status>0 and d.display=1")->find();
        $this->zxchildren($news['id']);
    }

    /*
      *  资讯页广告图
      *  @author   whh
      */
   
   public function zixun_adv(){
        $adv = M("Adv","tab_");
        $map['status'] = 1;
        $map['pos_id'] = 8; #资讯页广告图id
        $left_top_zixun= $adv->where($map)->order('sort ASC')->select();

        $map['pos_id'] = 9; #资讯页广告图id
        $mid_top_zixun= $adv->where($map)->order('sort ASC')->select();

        $map['pos_id'] = 10; #资讯页广告图id
        $rig_top_zixun= $adv->where($map)->order('sort ASC')->select();

        $map['pos_id'] = 11; #资讯页广告图id
        $adv_zixun= $adv->where($map)->order('sort ASC')->select();
        //file_put_contents('E:/aaa.html',json_encode($left_top_zixun.'-----'.$mid_top_zixun.'-----'.$rig_top_zixun.'-----'.$adv_zixun));
        $this->assign("left_top",$left_top_zixun);
        $this->assign("mid_top",$mid_top_zixun);
        $this->assign("rig_top",$rig_top_zixun);
        $this->assign("adv_zixun",$adv_zixun);
    }
    /**
   * APP资讯详情页接口H5页面
   * @author zdd
   */
  public function consultdetail_h5(){
    $id = $_REQUEST['id'];
      if(empty($id)){
                $msg = json_encode(array('status'=>-3,'msg'=>'参数不能为空'));
                            echo $msg;exit;
              }
      $obj = M('document','sys_');
      $result = $obj
          ->field('a.id,a.title,a.create_time,a.admin,b.content,b.id')
          ->alias('a')
          ->join('join sys_document_article as b on a.id=b.id ')
          ->where("a.id = $id ")
          ->find();
      $this->assign('vo',$result);
      $this->display();
  }
}
