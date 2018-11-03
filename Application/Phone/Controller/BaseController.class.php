<?php
namespace Phone\Controller;
use Media\Controller\HomeController;
use User\Api\MemberApi;
use Org\XiguSDK\Xigu;
use Media\Controller\DownController;



class BaseController{
  public $memberClass;
  public $key="mgwmd5keyapp";
  public $aes_key="2at7s9lumkgsq6u3";
  public $userClass;
  public $userModel;
  public $down_con;
  public function __construct(){

       $this->memberClass = new HomeController();
       $this->userClass = new MemberApi();
       $this->down_con = new DownController();
       $this->userModel = M('user','tab_');
    }
	protected function join_data($model){
		$game  = M($model['m_name'],$model['prefix']);
		$map = $model['map'];
		$data  = $game
		->field($model['field'])
		->join($model['join'])
		->limit($model['limit'])
    ->group($model['group'])
		->where($map)->order($model['order'])->select();
		return $data;
	}


	protected function list_data($model){
		$game  = M($model['m_name'],$model['prefix']);
		$map = $model['map'];
		$data  = $game
		->field($model['field'])
		->limit($model['limit'])
		->where($map)->order($model['order'])->select();
		return $data;
	}

	/**
	 * 首页广告游戏列表
	 */
	public function recommend(){
        $adv = M("appimage","tab_");
        $map1['status'] = 1;
        $map1['pos_id'] = 1; #首页轮播图广告id
        $map1['del']=0;
        $advdata1 = $adv->field('image_url as game_images,adv_url')->where($map1)->order('sort ASC')->limit(0,2)->select();
        $map2['status'] = 1;
        $map2['pos_id'] = 3; #首页轮播图广告id
        $map2['del']=0;
        $advdata2 = $adv->field('image_url as game_images,adv_url')->where($map2)->order('sort ASC')->limit(3)->select();
        $advdata=array_merge($advdata1,$advdata2);
        foreach($advdata as $key=>&$v){
            
            $advdata[$key]['game_images']=get_cover($v['game_images'],'path');
            
        }

        $recommend_data['advdata'] = $advdata;
		/***
		 *热门游戏
     */
   
    	$model = array(
    		'm_name'=>'Game',
    		'prefix'=>'tab_',
    		'map'   =>array('game_status'=>1,'app_recommend_status'=>2),
    		'field' =>'id as game_id,icon as game_images,game_name,dow_mynum as game_count,introduction game_text,and_dow_address as game_down,game_type_name as game_type',
    		'order' =>'app_sort ASC',
    		'limit' =>9,
    	);
    	$hot = $this->list_data($model);
    	foreach($hot as $key=>&$v){
        	
        	$v['game_images']=get_cover($v['game_images'],'path');
        	$v['game_down']=substr($v['game_down'],1);
        }    
   		
    	$recommend_data['hot'] = $hot;
    	/***
   		 *推荐新游
   		 */
   
        $model = array(
            'm_name'=>'Game',
            'prefix'=>'tab_',
            'map'   =>array('game_status'=>1,'app_recommend_status'=>3),
            'field' =>'id as game_id,icon as game_images,game_name as game_name,dow_mynum as game_count,introduction game_text,and_dow_address as game_down,game_type_name as game_type',
            'order' =>'app_sort ASC',
            'limit' =>4,
        );
        $xin = $this->list_data($model);
        foreach($xin as $key=>&$v){
        	
        	$v['game_images']=get_cover($v['game_images'],'path');
        	$v['game_down']=substr($v['game_down'],1);
        }   
        $recommend_data['xin'] = $xin;
   		/**
   		 *活动资讯
   		 */
   		$where['category_id'] = 44;
   		$where['status'] = 1;
   		$information = M('document')->where($where)->field('id as game_id,cover_id as game_images,title as game_text')->limit('6')->order('update_time desc')->select();  
   		foreach($information as $key=>&$v){
        	
        	$v['game_images']=get_cover($v['game_images'],'path');
        	
        }    
   		$recommend_data['information'] = $information;  

   		/***
		    *游戏礼包
    	  */
    
    	$model = array(
    		'm_name'=>'Giftbag',
    		'prefix'=>'tab_',
    		'field' =>'tab_giftbag.id as gift_id,game_id,tab_giftbag.game_name,giftbag_name,giftbag_type,tab_game.icon as game_images,tab_giftbag.create_time',
    		'join'	=>'tab_game on tab_giftbag.game_id = tab_game.id',
    		'map'   =>array('status'=>1),
    		'order' =>'create_time desc',
        'group' =>'game_id',
        'limit' =>9,
    	);
    	$gift = $this->join_data($model);
    	foreach($gift as $key=>&$v){
        	
        	$v['game_images']=get_cover($v['game_images'],'path');
        	
        }  
        $recommend_data['gift'] = $gift;   
    	/**
    	 * 所有游戏数据
    	 */
    	$game_model=M("game","tab_"); 
               $dow_map['game_status']=1;
               //游戏按下载量排
               $down_data1=$game_model
                          ->field('id as game_id,icon as game_image,game_name,dow_mynum as game_count,introduction as game_text,and_dow_address as game_down,game_type_id,game_type_name game_type')
                          ->where($dow_map)->order('dow_mynum desc')->select();
               foreach ($down_data1 as $k => &$v) {
                        $down_data1[$k]['game_image']=get_cover($v['game_image'],'path');
                        $v['game_down']=substr($v['game_down'],1);
                       
               }
               
              $recommend_data['down_data1'] = $down_data1;
    		      $this->out($recommend_data);exit;
	}
	/**
         * APP游戏页面接口
         * @author whh
         */

        public function game(){
               $game_model=M("game","tab_"); 
               $dow_map['game_status']=1;
               //游戏按下载量排
               $down_data1=$game_model
                          ->field('id,icon as game_image,game_name,game_type_name as game_type,dow_mynum as game_count,introduction as game_text,and_dow_address as game_down')
                          ->where($dow_map)->order('dow_mynum desc')->limit(25)->select();
               foreach ($down_data1 as $k => &$v) {
                        $down_data1[$k]['game_image']=get_cover($v['game_image'],'path');
                        $down_data1[$k]['game_down']=substr($v['game_down'],1);
               }
                $game_data['down']=$down_data1;
                $adv = M("appimage","tab_");
                $map1['status'] = 1;
                $map1['pos_id'] = 1; #首页轮播图广告id
                $map1['del']=0;
                $advdata1 = $adv->field('image_url as game_images,adv_url')->where($map1)->order('sort ASC')->limit(2,2)->select();
                $map2['status'] = 1;
                $map2['pos_id'] = 3; #首页轮播图广告id
                $map2['del']=0;
                $advdata2 = $adv->field('image_url as game_images,adv_url')->where($map2)->order('sort ASC')->limit(3,1)->select();
                $advdata=array_merge($advdata1,$advdata2); 
                foreach($advdata as $key=>&$v){
            
                    $advdata[$key]['game_images']=get_cover($v['game_images'],'path');
                    
                }
                $game_data['advdata'] = $advdata;

                //游戏页面圣域游戏循环  all
                $all_map['game_status']=1;
                //游戏按下载量排
                $count=$game_model->count('id');
                $all_data1=$game_model
                          ->field('id,icon as game_image,game_name,game_type_name as game_type,dow_mynum as game_count,introduction as game_text,and_dow_address as game_down')
                          ->where($all_map)->order('dow_mynum desc')->limit(26,$count-26)->select();
                foreach ($all_data1 as $k2 => &$v2) {
                        $all_data1[$k2]['game_image']=get_cover($v2['game_image'],'path');
                        $all_data1[$k2]['game_down']=substr($v2['game_down'],1);
                }
                $game_data['all']=$all_data1;
                $this->out($game_data);exit;
        }
       /**
         * APP礼包页面接口
         * @author whh
         */
        public function gift(){
                //礼包页面
                $model = array(
                        'm_name'=>'Giftbag',
                        'prefix'=>'tab_',
                        'field' =>'tab_giftbag.id as gift_id,game_id,tab_giftbag.game_name,giftbag_name,desribe,end_time,tab_game.icon as game_images,tab_giftbag.create_time',
                        'join'  =>'tab_game on tab_giftbag.game_id = tab_game.id',
                        'map'   =>array('game_status'=>1,'novice'=>array('neq',''),"end_time" =>array("GT",time())),
                        'order' =>'create_time desc',
                        'group' =>'game_id',
                        'limit' =>6,
                );
                $gift = $this->join_data($model);
                foreach($gift as $k1=>&$v1){                        
                        $v1['game_images']=get_cover($v1['game_images'],'path'); 
                        $v1['days']=ceil(($v1['end_time']-time())/86400);
                        if($v1['days']<0){
                               $v1['days']="已过期";
                        }                      
                }  
                $gift_data['gift'] = $gift;
                //合并游戏礼包
                $gift_model = M('giftbag','tab_');
                $sql = "select l.game_name,l.game_id,count(l.id) as num,group_concat(l.giftbag_name) as giftbag_name,y.icon as game_images from tab_giftbag l join tab_game as y on l.game_id = y.id and y.game_status = 1 GROUP BY l.game_id order by l.game_id desc;";
                $game_gift = $gift_model->query($sql);
                foreach($game_gift as $k1=>&$v1){                        
                        $v1['game_images']=get_cover($v1['game_images'],'path'); 
                                        
                }  
                $gift_data['game_gift'] = $game_gift;
                //广告位
               $adv = M("appimage","tab_");
                $map2['status'] = 1;
                $map2['pos_id'] = 3; #首页轮播图广告id
                $map2['del']=0;
                $advdata = $adv->field('image_url as game_images,adv_url')->where($map2)->order('sort ASC')->limit(4,4)->select();
                foreach($advdata as $key=>&$v){
                    
                    $advdata[$key]['game_images']=get_cover($v['game_images'],'path');
                    
                }   
                $gift_data['advdata'] = $advdata;
                echo  json_encode($gift_data);
        }

        
        /**
         * 分类
         */
        public function classify(){
          if (IS_POST) {
              if (empty(I('post.type'))) {
          
                $this->out(array('status'=>0,'msg'=>'参数有误'));exit;
              }
              $where['game_type_name'] = I('post.type');
              $where['game_status'] = 1;
             
              $classify = M('game','tab_')->where($where)->select();
              
              foreach ($classify as $k2 => &$v2) {
                            $classify[$k2]['icon']=get_cover($v2['icon'],'path');
                            $classify[$k2]['and_dow_address']=substr($v2['and_dow_address'],1);
                    }
              $classify_data['classify']=$classify;
              //print_r($classify_data);exit;
              $this->out($classify_data);exit;
          }else{
              $where['status_show'] = 1;
              $where['status'] = 1;
              $data = M('Game_type','tab_')->field('id,type_name,img_size,type_img,sort')->where($where)->order('sort asc')->select();
               foreach($data as $key=>&$v){
                    
                    $data[$key]['type_images']=get_cover($v['type_img'],'path');
                    
                }  
              $this->out($data);exit;
          }
        
        }
         /**
         * APP资讯页面接口
         * @author whh
         */
        public function information(){
            $where['category_id'] = array('in','42,43,44,49,50');
            $where['status'] = 1;
            $information = M('document')->where($where)->field('id,category_id,cover_id as game_images,title as game_text,update_time')->order('update_time desc')->select();  
            foreach($information as $key=>&$v){
                
                $v['game_images']=get_cover($v['game_images'],'path');
                
            }    
            $information_data['information'] = $information; 
           $adv = M("appimage","tab_");
            $map2['status'] = 1;
            $map2['pos_id'] = 3; #首页轮播图广告id
            $map2['del']=0;
            $advdata = $adv->field('image_url as game_images,title,adv_url')->where($map2)->order('sort ASC')->limit(8,5)->select();
            foreach($advdata as $key=>&$v){
            
                 $advdata[$key]['game_images']=get_cover($v['game_images'],'path');
            
            } 
            $information_data['advdata'] = $advdata;
            echo  json_encode($information_data);
        }

        /**
         * 游戏页导航部分
         */
        public function game_nav(){
          /**
         * 新游
         */
          $model = array(
            'm_name'=>'Game',
            'prefix'=>'tab_',
            'map'   =>array('game_status'=>1,'app_recommend_status'=>3),
            'field' =>'id as game_id,icon as game_images,game_name as game_name,dow_mynum as game_count,introduction game_text,and_dow_address as game_down,game_type_name as game_type',
            'order' =>'app_sort ASC',
            'limit' =>8,
        );
          $xin = $this->list_data($model);
          foreach($xin as $key=>&$v){
            
            $v['game_images']=get_cover($v['game_images'],'path');
            $v['game_down']=substr($v['game_down'],1);
          }   
          $game_nav['xin'] = $xin;
          /***
          *热门游戏
          */
   
          $model = array(
            'm_name'=>'Game',
            'prefix'=>'tab_',
            'map'   =>array('game_status'=>1,'app_recommend_status'=>2),
            'field' =>'id as game_id,icon as game_images,game_name,dow_mynum as game_count,introduction game_text,and_dow_address as game_down,game_type_name as game_type',
            'order' =>'app_sort ASC',
            
          );
          $hot = $this->list_data($model);
          foreach($hot as $key=>&$v){
              
              $v['game_images']=get_cover($v['game_images'],'path');
              $v['game_down']=substr($v['game_down'],1);
            }  
            
            $adv = M("appimage","tab_");
            $map2['status'] = 1;
            $map2['pos_id'] = 3; #首页轮播图广告id
            $map2['del']=0;
            $advdata = $adv->field('image_url as images,title,adv_url')->where($map2)->order('sort ASC')->limit(11,1)->select();
            foreach($advdata as $key=>&$v){
                
                $advdata[$key]['images']=get_cover($v['images'],'path');
                
            }           
            $game_nav['hot_advdata'] =$advdata; 
            
            $game_nav['hot'] = $hot;
            //游戏按下载量排
            $game_model=M('game','tab_');
            $dow_map['game_status'] = 1;
           $down_data1=$game_model
                      ->field('id,icon as game_image,game_name,game_type_name as game_type,dow_mynum as game_count,introduction as game_text,and_dow_address as game_down')
                      ->where($dow_map)->order('dow_mynum desc')->select();
           foreach ($down_data1 as $k => &$v) {
                    $down_data1[$k]['game_image']=get_cover($v['game_image'],'path');
                    $down_data1[$k]['game_down']=substr($v['game_down'],1);
           }
           $game_nav['down']=$down_data1;
       /***
       *网络游戏
         */
   
          $model = array(
            'm_name'=>'Game',
            'prefix'=>'tab_',
            'map'   =>array('game_status'=>1,'features'=>array('like','%网络游戏%')),
            'field' =>'id as game_id,icon as game_images,game_name,dow_mynum as game_count,introduction game_text,and_dow_address as game_down,game_type_name as game_type',
            'order' =>'app_sort ASC',
            
          );
          $net = $this->list_data($model);
          foreach($net as $key=>&$v){
              
              $v['game_images']=get_cover($v['game_images'],'path');
              $v['game_down']=substr($v['game_down'],1);
            }    
       /**
     * 游戏页面网友列表图片
       */
        $adv = M("appimage","tab_");
        $map1['status'] = 1;
        $map1['pos_id'] = 2; #首页轮播图广告id
        $map1['del']=0;
        $advdata1 = $adv->field('image_url as game_images,adv_url')->where($map1)->order('sort ASC')->limit(1)->select();
        $map2['status'] = 1;
        $map2['pos_id'] = 3; #首页轮播图广告id
        $map2['del']=0;
        $advdata2 = $adv->field('image_url as game_images,adv_url')->where($map2)->order('sort ASC')->limit(12,2)->select();
        $advdata=array_merge($advdata1,$advdata2); 
        foreach($advdata as $key=>&$v){
            
            $advdata[$key]['game_images']=get_cover($v['game_images'],'path');
            
        }
        $game_nav['net_advdata'] = $advdata;
        $game_nav['net'] = $net;
        $this->out($game_nav);exit;
      
        }
  /**
   * APP资讯详情页接口
   * @author Zky
   */
  public function consultdetail(){
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
          ->select();
      $consultdetail['results']=$result;
      $this->out($consultdetail);exit;
  }
 
    /**
     * APP游戏详情页接口
     * @author whh
     */
     public function game_details(){
              $game_id=$_REQUEST['id'];
              if(empty($game_id)){
                $msg = json_encode(array('status'=>-3,'msg'=>'参数不能为空'));
                            echo $msg;exit;
              }
              //print_r($game_id['game_id']);exit;
              $game_model=M('game','tab_');
             
              //游戏详情
              //print_r($game_details);exit;
              $game_data1=$game_model->field('id as game_id,icon as game_image,game_name,game_type_name as game_type,game_type_id,dow_mynum as game_count,introduction as game_text,and_dow_address as game_down,screenshot as game_screenshot,game_size as size')
                  ->where("id=".$game_id)->find();
              //print_r($game_data1);exit;
              
              $game_data1['game_image']=get_cover($game_data1['game_image'],'path');
              $game_data1['game_down']=substr($game_data1['game_down'],1);
              $game_detail=$game_data1;
              $at=explode(',',$game_data1['game_screenshot']);
              //print_r($at);exit;
              foreach($at as $k1=> &$v1){        
               $image_details[$k1]=get_cover($v1,'path');
               } 
               //print_r($image_details);exit; 
               //print_r($game_details);exit; 
               
               //礼包详情
               $gift_model=M('giftbag','tab_');
               $where=array("game_id"=>$game_id,"end_time" =>array("GT",time()));
               $gift_data=$gift_model->field('id as gift_id,giftbag_name,game_name,end_time,desribe,novice')
                  ->where($where)->select();
               //print_r($gift_data);exit;
               foreach ($gift_data as $k2 => &$v2) {
                    //$v2['days']=floor(($v2['end_time']-time())/86400);  
                   if(empty($v2['novice'])){
                    $v2['days'] = 0 ;    
                   }else{
                    $v2['days'] = count(explode(',',$v2['novice'])) ;     
                   }
                   unset($v2['novice']);
                      

               }

               $game_details['gift_details']=$gift_data;
               $game_details['game_details'][]=$game_detail;
               $game_details['image_details'][]=$image_details;
               
               $this->out($game_details);exit;
               
        }
        /**
         * APP搜索页面热门游戏接口
         * @author whh
         */
        
        public function regame(){
              $game_model=M('game','tab_');
              $where['game_status']=1;
              $where['app_recommend_status']=2;
              $game_data=$game_model->field('id as game_id,icon as game_images,game_name,game_type_name as game_type,game_type_id,dow_mynum as game_count,introduction as game_text,and_dow_address as game_down,screenshot as game_screenshot,game_size as size')->where($where)->order('app_sort asc')->limit(13)->select();
              foreach($game_data as $k1=>&$v1){        
                       $game_data[$k1]['game_images']=get_cover($v1['game_images'],'path');
                }
              $regame_data['regame_data']=$game_data;
              //print_r($regame_data);exit;
              echo  json_encode($regame_data);
        }

        /**
         * APP搜索功能接口
         * @author whh
         */
        public function searchgame(){

            $name=trim(I('post.game_name'));
            if(empty($name)){
              $msg = json_encode(array('status'=>-1,'msg'=>'游戏名称不能为空'));
              echo $msg;exit;
            }
            $game_model=M('game','tab_');
            $where=array('game_status'=>1,'game_name'=>array('like',"%$name%"));
            $game_data=$game_model->field('id as game_id,icon as game_images,game_name,game_type_name as game_type,game_type_id,dow_mynum as game_count,introduction as game_text,and_dow_address as game_down,screenshot as game_screenshot,game_size as size')->where($where)->order('app_sort ASC')->select();
            foreach($game_data as $k1=>&$v1){        
                       $game_data[$k1]['game_images']=get_cover($v1['game_images'],'path');
                       $game_data[$k1]['game_down']=substr($v1['game_down'],1);
                }
            $searchgame['searchgame']=$game_data;
            //print_r($searchgame);exit;
            echo  json_encode($searchgame);

        }
        
       
   
              /**
           * APP登录接口
           * @author  whh <[email address]>
           */
        public function login(){
            
            $user_account=I('post.account');
            $user_password=I('post.password');
            $vcode=I('post.vcode');
            
            
            if (empty($user_account) || empty($user_password) || empty($vcode)) {
                $msg = json_encode(array('status'=>-3,'msg'=>'账号或密码不能为空'));
                            echo $msg;exit;
            } else {
                $vcode_now=getVcode($user_account,$user_password,$this->key);
                $msg  = vcodeVerify($vcode_now);
                if(!$msg){
                  $msg = json_encode(array('status'=>-4,'msg'=>'签名验证失败'));
                  echo $msg;exit;
                }
                $user_password = aes_decode(base64_decode($user_password));
                    $res = $this->userClass->login($user_account,$user_password);
                    if ($res=='-2') {
                           $msg = json_encode(array('status'=>-2,'msg'=>'密码错误'));
                            echo $msg;exit;
                    } elseif($res=='-1'){
                         $msg = json_encode(array('status'=>-1,'msg'=>'用户名不存在或被禁用'));
                            echo $msg;exit;
                    } else{

                          $where['user_id'] = $res;
                          $where['user_account'] = $user_account;
                          $flag = M('login_sign','tab_')->where($where)->find();

                          $login_sign =md5($vcode.sp_random_string(7));
                          $data['login_sign'] = $login_sign;
                          $data['create_time']  = time();
                          if($flag){
                              $result = M('login_sign','tab_')->where($where)->save($data);
                          }else{
                            $data['user_id'] = $where['user_id'];
                            $data['user_account']  = $where['user_account'] ;
                            $data['crete_time']  = time();
                              $result = M('login_sign','tab_')->add($data);
                          }
                          
                          if($result){
                            $msg = json_encode(array('status'=>1,'msg'=>$login_sign));
                            echo $msg;exit;
                          }else{
                            $msg = json_encode(array('status'=>-4,'msg'=>'服务器故障，请重试'));
                            echo $msg;exit;
                          }
                    }

                        
                
            }
            
            
            
        }




    /**
     * 用户名注册
     */
    protected function register($data){     
        if(C("USER_ALLOW_REGISTER")==1){
           $user = M('user','tab_');
        /* $where['email'] = I('post.email');
             $count=$user->where($where)->count('id');
            if($count>0){
                $this->out(array('status'=>-1,'msg'=>'该邮箱已被其他账号绑定')); exit;
            }*/
              $data['password'] = think_ucenter_md5($data['password'],UC_AUTH_KEY);
              
              $data['nickname']=$data['account'];
              $data['register_time']=time();
              $data['register_ip']=get_client_ip();
              $data['register_way'] = I('post.register_way');
              $res = $user->add($data);
              if($res){
                 $msg = json_encode(array('status'=>1,'msg'=>'ok'));
                 echo $msg;exit;
              }else{
                $msg = json_encode(array('status'=>5,'msg'=>'注册失败,服务器故障'));
                echo $msg;exit;               
              }
        }else{
                $msg = json_encode(array('status'=>0,'msg'=>'未开放注册！！！'));
                 echo $msg;exit;           
        }
  }
   /*
    *检查账号是否存在
    * $msg=0代表已被注册
    */
    public function check_account(){

        $account = empty(I('post.account'))?I('post.phone'):I('post.account');
        $map['account'] = $account;
        $data = $this->userModel->where($map)->find();
        if(!empty($data)){
         $msg = json_encode(array('status'=>0,'msg'=>'用户名已被注册'));
                 echo $msg;exit;
          }else{
         $msg = json_encode(array('status'=>1,'msg'=>'ok'));
                 echo $msg;exit;
          }

        
    }
   /**
    *检查手机号是否存在
    */
    public function check_phone(){

        $phone=I('post.phone');
        if(empty($phone)){
          $msg = json_encode(array('status'=>-1,'msg'=>'手机号码不能为空'));
                 echo $msg;exit;
        }else{
          $user = M('user','tab_');
          $data = $user->where("account = $phone or phone = $phone")->find();
          if(empty($data)){
            $msg = json_encode(array('status'=>1,'msg'=>'ok'));
                 echo $msg;exit;
          }else{
            $msg = json_encode(array('status'=>0,'msg'=>'手机号已被占用'));
                 echo $msg;exit;
          }
        }
        

        
    }
  /**
    *验证手机安全码
    */
    public function check_phonecode($type=''){

        $phone = I('post.phone');
        $phonecode = I('post.phonecode');
        if(empty($phone) || empty($phonecode)){
          $this->out(array('status'=>-2,'msg'=>'手机号或验证码不能为空'));exit;
        }
        $where['phone'] = $phone;
        
       $telsvcode = M('phonecode','tab_')->where($where)->find();
      if(empty($telsvcode)){
        $this->out(array('status'=>-3,'msg'=>'请检查输入的手机号码是否正确'));exit;
      }
        $time = (time() - $telsvcode['time'])/60;
        if ($time>$telsvcode['delay']) {
            $res = M('phonecode','tab_')->where($where)->delete();
          if(!$res){
            M('phonecode','tab_')->where($where)->delete();
          }
            $this->out(array('status'=>0,'msg'=>'时间超时,请重新获取验证码'));exit;
        }
        if (!($telsvcode['code'] == $phonecode) || !($telsvcode['phone'] == $phone)) {
            $this->out(array('status'=>-1,'msg'=>'安全码输入有误'));exit;
        }else{
          $res = M('phonecode','tab_')->where($where)->delete();
          if(!$res){
            M('phonecode','tab_')->where($where)->delete();
          }
          if($type=='synchronous'){
            return ture;
          }else{
            $this->out(array('status'=>1,'msg'=>'ok'));
          }
          
        }  
    }
    /**
    *发送手机验证码
    */
    public function telsvcode($phone=null,$delay=10,$flag=true) {
      $phone = I('post.phone');
        if (empty($phone)) {
            $this->out(array('status'=>0,'msg'=>'手机号码不能为空'));exit; 
        }
        
        /// 产生手机安全码并发送到手机且存到session
        $rand = rand(100000,999999);
        $xigu = new Xigu(C('sms_set.smtp'));
        $param = $rand.",".$delay;
        $result = json_decode($xigu->sendSM(C('sms_set.smtp_account'),$phone,C('sms_set.smtp_port'),$param),true); 
        // 存储短信发送记录信息
        $result['create_time'] = time();
        $result['pid']=0;
        $r = M('Short_message')->add($result);
        
        if ($result['send_status'] != '000000') {
            $this->out(array('status'=>0,'msg'=>'发送失败，请重新获取'));exit;
        }        
        $telsvcode['code']=$rand;
        $telsvcode['phone']=$phone;
        $telsvcode['time']=$result['create_time'];
        $telsvcode['delay']=$delay;
        session('telsvcode',$telsvcode);
        $res = M('phonecode','tab_')->add($telsvcode);
        if(!$res){
             M('phonecode','tab_')->add($telsvcode);
        }
        
        if ($flag) {
            $this->out(array('status'=>1,'msg'=>'安全码已发送，请查收'));        
        } else
            $this->out(array('status'=>2,'msg'=>'系统异常'));
    }
           /**
           * 必须填写身份信息的手机号注册
           */
          /*public function tel_register(){
              if(empty($_POST['phone']) || empty($_POST['phonecode']) ||empty($_POST['password']) || empty($_POST['real_name']) || empty($_POST['id_card']) || empty($_POST['email']) || empty($_POST['register_ip'])  || empty($_POST['promote_account']) || empty($_POST['register_way'])){
                  $msg = json_encode(array('status'=>2,'msg'=>'请完善注册信息'));
                  echo  $msg;exit;
              }
              $phone = I('post.phone');
              $user = M('user','tab_');
              
              $user_info = $user->where("account = $phone or phone = $phone")->find();
              if($user_info){
              $msg = json_encode(array('status'=>0,'msg'=>'手机号已被占用'));
              echo $msg;exit;}
              $data['account'] = $phone;
              $data['phone'] = I('post.phone');
              $data['phonecode'] = I('post.phonecode');
              $data['password'] = I('post.password');
              $data['real_name'] = I('post.real_name');
              $data['id_card'] = I('post.id_card');
              $data['email'] = I('post.email');
              $data['register_ip'] = I('post.register_ip');
              $data['promote_id'] = I('post.promote_id');
              $data['promote_account'] = I('post.promote_account');
              $data['register_way'] = I('post.register_way');
              $vcode_now = getVcode($data['account'],$data['phonecode'],$data['password'],$data['real_name'],$data['id_card'],$data['email'],$data['register_ip'],$data['promote_id'],$data['promote_account'],$data['register_way'],$this->key);
             
             $flag = vcodeVerify($vcode_now);
              if(!$flag){
                $msg = json_encode(array('status'=>-4,'msg'=>'签名验证失败'));
                    echo  $msg;exit;
              }
              $data['password'] = aes_decode(base64_decode($data['password']));
              $data['idcard'] = aes_decode(base64_decode($data['id_card']));
             
             
               $this->register($data);
            }*/
             /**
           * 不需要填写身份信息的手机号注册
           */
          public function tel_register(){
              if(empty($_POST['phone']) || empty($_POST['phonecode']) ||empty($_POST['password']) || empty($_POST['register_ip'])  || empty($_POST['promote_account']) || empty($_POST['register_way'])){
                  $msg = json_encode(array('status'=>2,'msg'=>'请完善注册信息'));
                  echo  $msg;exit;
              }
              $phone = I('post.phone');
              $user = M('user','tab_');
              
              $user_info = $user->where("account = $phone or phone = $phone")->find();
              if($user_info){
              $msg = json_encode(array('status'=>0,'msg'=>'手机号已被占用'));
              echo $msg;exit;}
              $this->check_phonecode('synchronous');
              $data['account'] = $phone;
              $data['phone'] = I('post.phone');
              $data['phonecode'] = I('post.phonecode');
              $data['password'] = I('post.password');
              $data['email'] = I('post.email');
              $data['register_ip'] = I('post.register_ip');
              $data['promote_id'] = I('post.promote_id');
              $data['promote_account'] = I('post.promote_account');
              $data['register_way'] = I('post.register_way');
              $vcode_now = getVcode($data['account'],$data['phonecode'],$data['password'],$data['email'],$data['register_ip'],$data['promote_id'],$data['promote_account'],$data['register_way'],$this->key);
             
             $flag = vcodeVerify($vcode_now);
              if(!$flag){
                $msg = json_encode(array('status'=>-4,'msg'=>'签名验证失败'));
                    echo  $msg;exit;
              }
              $data['password'] = aes_decode(base64_decode($data['password']));
               $this->register($data);
            }
            /**
             * 必须填写身份信息的用户名注册
             */
           /* public function user_register(){
              if(empty($_POST['account']) || empty($_POST['password']) || empty($_POST['real_name']) || empty($_POST['id_card']) || empty($_POST['email']) || empty($_POST['register_ip'])  || empty($_POST['promote_account']) || empty($_POST['register_way'])){
                    $msg = json_encode(array('status'=>2,'msg'=>'请完善注册信息'));
                    echo  $msg;exit;
                } else if(strlen($_POST['account'])>15||strlen($_POST['account'])<6){
                   $msg = json_encode(array('status'=>3,'msg'=>'用户名长度需在6~15个字符'));
                    echo  $msg;exit;
                   
                }else if(!preg_match('/^[a-zA-Z]+[0-9a-zA-Z_]{5,14}$/', $_POST['account'])){
                   $msg = json_encode(array('status'=>4,'msg'=>'用户名包含特殊字符'));
                    echo  $msg;exit;
                    
                }
                $data['account'] = I('post.account');
                $user = M('user','tab_');
                $where['account'] = $data['account'];
                $user_info = $user->where($where)->find();
                if($user_info){
                $msg = json_encode(array('status'=>0,'msg'=>'用户名已被占用'));
                echo $msg;exit;}
              $data['password'] = I('post.password');
              $data['real_name'] = I('post.real_name');
              $data['id_card'] = I('post.id_card');
              $data['email'] = I('post.email');
              $data['register_ip'] = I('post.register_ip');
              $data['promote_id'] = I('post.promote_id');
              $data['register_way'] = I('post.register_way');             
              $data['promote_account'] = I('post.promote_account');
              $vcode_now = getVcode($data['account'],$data['password'],$data['real_name'],$data['id_card'],$data['email'],$data['register_ip'],$data['promote_id'],$data['promote_account'],$data['register_way'],$this->key);
              
              $flag = vcodeVerify($vcode_now);
              if(!$flag){
                $msg = json_encode(array('status'=>-4,'msg'=>'签名验证失败'));
                    echo  $msg;exit;
              }
              $data['password'] = aes_decode(base64_decode($data['password']));
              $data['idcard'] = aes_decode(base64_decode($data['id_card']));
               $this->register($data);
            }
           */
          /**
           * 不需要填写身份信息的用户名注册
           */
          public function user_register(){
              if(empty($_POST['account']) || empty($_POST['password'])  || empty($_POST['register_ip'])  || empty($_POST['promote_account']) || empty($_POST['register_way'])){
                    $msg = json_encode(array('status'=>2,'msg'=>'请完善注册信息'));
                    echo  $msg;exit;
                } else if(strlen($_POST['account'])>15||strlen($_POST['account'])<6){
                   $msg = json_encode(array('status'=>3,'msg'=>'用户名长度需在6~15个字符'));
                    echo  $msg;exit;
                   
                }else if(!preg_match('/^[a-zA-Z]+[0-9a-zA-Z_]{5,14}$/', $_POST['account'])){
                   $msg = json_encode(array('status'=>4,'msg'=>'用户名包含特殊字符'));
                    echo  $msg;exit;
                    
                }
                $data['account'] = I('post.account');
                $user = M('user','tab_');
                $where['account'] = $data['account'];
                $user_info = $user->where($where)->find();
                if($user_info){
                $msg = json_encode(array('status'=>0,'msg'=>'用户名已被占用'));
                echo $msg;exit;}
              $data['password'] = I('post.password');
              $data['email'] = I('post.email');
              $data['register_ip'] = I('post.register_ip');
              $data['promote_id'] = I('post.promote_id');
              $data['register_way'] = I('post.register_way');             
              $data['promote_account'] = I('post.promote_account');
              $vcode_now = getVcode($data['account'],$data['password'],$data['email'],$data['register_ip'],$data['promote_id'],$data['promote_account'],$data['register_way'],$this->key);
              
              $flag = vcodeVerify($vcode_now);
              if(!$flag){
                $msg = json_encode(array('status'=>-4,'msg'=>'签名验证失败'));
                    echo  $msg;exit;
              }
              $data['password'] = aes_decode(base64_decode($data['password']));
               $this->register($data);
            }
         /**
          *充值平台币成功后的设置
          */
    public function set_deposit($data){
        $deposit = M('deposit',"tab_");
        $map['pay_order_number'] = $data['out_trade_no'];
        $d = $deposit->where($map)->find();
        if(empty($d)){return false;}
        if($d['pay_status'] == 0){
            $data_save['pay_status'] = 1;
            $data_save['order_number'] = $data['trade_no'];
            $map_s['pay_order_number'] = $data['out_trade_no'];
            $r = $deposit->where($map_s)->save($data_save);
            if($r !== false){
                $user = M("user","tab_");
                $user->where("id=".$d['user_id'])->setInc("balance",$d['pay_amount']);
                $user->where("id=".$d['user_id'])->setInc("cumulative",$d['pay_amount']);
            }else{
                $this->record_logs("修改数据失败");
            }
            return true;
        }
        else{
            return true;
        }
    }

        /**
         * 获取测试参数
         */  
        public function pv(){
          $account = I('post.account');
          $password = I('post.password');
          $p = base64_encode(aes_encode($password));
          $p_de = aes_decode(base64_decode($account));
          $v = md5($account.$password.'mgwmd5keyapp');
          $data['p_en'] =$p;
          $data['p_de'] =$p_de;
          $data['v'] =$v;
          print_r($data);exit;
          //$this->out(array('p'=>$p,'v'=>$v,'p_de'=>$p_de));exit;

        }

        /**
         * 测试php://input
         */
        public function php_input(){
          $this->out(file_get_contents("php://input"));
        }

        public function regeng(){
            $data = M('app_version','tab_')->order('serverVersion desc')->find();
            /*$data['appname']="moguwan-4.0apk";
            $data['serverVersion']=C('APP_SERVERVERSION');
            $data['serverFlag']="1";
            $data['lastForce']="1";
            $data['updateurl']=$_SERVER['HTTP_HOST'].$file_url;
            $data['upgradeinfo']="V1.0版本更新，你想不想要试一下哈！！！";
            $data['is_must']="1";
            */
            $where['game_id'] = 68;
            $file_url = M('game_source','tab_')->where($where)->getField('file_url');
            $file_url = substr($file_url,1);
            $data['updateurl']=$_SERVER['HTTP_HOST'].$file_url;
            $this->out($data);
        }
        /**
         * 忘记密码——验证手机号码是否匹配
         * @author zdd
         */
        public function forget_phone_verify(){
             $where['account'] = I('post.account');
             $phone = I('post.phone');
             if(empty($where['account']) || empty($phone)){
              $msg = json_encode(array('status'=>-1,'msg'=>'该用户还未绑定手机号'));
                    echo  $msg;exit;
             }
             $phone_now = $this->userModel->where($where)->getField('phone');
             if($phone != $phone_now){
              $msg = json_encode(array('status'=>0,'msg'=>'手机号码不匹配'));
                    echo  $msg;exit;
             }else{
              $msg = json_encode(array('status'=>1,'msg'=>'ok'));
                    echo  $msg;exit;
             }
        }
        /**
         * 忘记密码——完成修改
         * @author zdd
         */
        public function forget_update(){
            
          $account = I('post.account');
          $phone = I('post.phone');
          $phonecode = I('post.phonecode');
          $password = I('post.password');
          $password_dec = aes_decode(base64_decode($password));
          $vcode = I('post.vcode');
          if(empty($account) || empty($phone) || empty($phonecode) || empty($password) || empty($vcode) ){
            $msg = json_encode(array('status'=>-6,'msg'=>'请完善信息'));
                    echo  $msg;exit;
          }else{
            $vcode_now = getVcode($account,$phone,$phonecode,$password,$this->key);
            $flag = vcodeVerify($vcode_now);
              if(!$flag){
                $msg = json_encode(array('status'=>-4,'msg'=>'签名验证失败'));
                    echo  $msg;exit;
              }else{
                $type = 'synchronous';
                $res = $this->check_phonecode($type);
                if($res){
                  $where['account'] = $account;
                  $where['phone'] = $phone;
                  $data['password'] = think_ucenter_md5($password_dec,UC_AUTH_KEY);
                  $res2 = $this->userModel->where($where)->save($data);
                  if($res2 !== false){
                    $msg = json_encode(array('status'=>1,'msg'=>'ok'));
                    echo  $msg;exit;
                  }else{
                    $msg = json_encode(array('status'=>-5,'msg'=>'服务器故障，请稍后重试'));
                    echo  $msg;exit;
                  }
                }
              }
          }
        }
        /**
         * @author zdd
         * 忘记密码时检测账号
         */
        public function forget_check_account(){
            $account = I('post.account');
            if(empty($account)){
              $msg = json_encode(array('status'=>-1,'msg'=>'参数有误'));
              echo $msg;exit;
            }
            $map['account'] = $account;
            $data = $this->userModel->where($map)->find();
            if(!empty($data)){
               $msg = json_encode(array('status'=>1,'msg'=>$data['phone']));
               echo $msg;exit;
            }else{
              $msg = json_encode(array('status'=>0,'msg'=>'账号不存在'));
              echo $msg;exit;
            }

        }
         /**
         * @author zdd
         * 验证邮箱是否绑定
         */
         public  function emailbangcheck($email){
            $where['email']=I('email');
            if (empty($where['email'])) {
               $this->out(array('status'=>-1,'msg'=>'邮箱不能为空!')); exit; 
            }
            $model=M('User','tab_');
            $count=$model->where($where)->count('id');
            //echo $count;exit;
            if($count>0){
                $this->out(array('status'=>-1,'msg'=>'该邮箱已被其他账号绑定')); exit;
            }else{
                $this->out(array('status'=>1,'msg'=>'OK')); exit;
            }
        }
 /**
   * @author zdd
   * 游戏官包下载
   */
  public function down_file(){
    //print_r(I('post.'));exit;
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
    //print_r($sdata['user_id']);exit;
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
        if(!varify_url($data['game_address'])){
            
            $this->out(array('status'=>'1','msg'=>$data['file_url']));exit;
        }else{
            $this->out(array('status'=>'0','msg'=>"获取下载地址出错"));exit; 
        }
  }
   /**
   * @author zdd
   * APP欢迎页
   */
  public function welcome(){
    $imageurl = M('appimage','tab_')->where('id =22')->getField('image_url');
    $welcome_img=get_cover($imageurl,'path');
    $this->out($welcome_img);exit;
  }
  /**
   * @author zdd
   * APP获取游戏版本
   */
  public function get_game_version(){
    $ids = explode(',',I('ids'));
    $game = M('Game','tab_');
    foreach ($ids as $key => $value) {
         $where['id'] = $value;
         $info = $game->field('id,game_name,icon,game_size,version')->where($where)->find();
         $info['icon']=get_cover($info['icon'],'path');
         $data[] = $info;
    }
    $result['game'] = $data;
    $this->out($result);
  }
  public function is_update(){
    $ids = I('post.ids');
    $game_arr = json_decode(I('post.ids'),true);
    $game = M('Game','tab_');
    $apply = M('Apply','tab_');
    $result =array();
    foreach ($game_arr as $k => $v) {
      $game_id = $v['game_id'];
      $where['id'] = $game_id;
      $version = $game->where($where)->field('version,and_dow_address')->find();
      if($version['version'] != $v['versionName']){
        if($v['promote_id'] == 0){
          $result[$game_id] = $version['and_dow_address'];
        }else{
          $pwhere['promte_id'] = $v['promote_id'];
          $pwhere['game_id'] = $v['game_id'];
          $down_url = $apply->where($pwhere)->getField('pack_url');
          $result[$game_id] = $down_url;
        }
      }
    }
    if(empty($result)){
      $result["-1"] = "全部都已经是最新版本的";
    }
    $this->out($result);
    exit;
  }
  /**
   * 统一输出函数
   */
  protected function out($info = ''){
      $info = json_encode($info);
      $data['in'] = json_encode($_POST);
      //$data['out'] = $info; 
      $data['url'] = $_SERVER['REQUEST_URI'];
      $data['create_time'] = time();
      $data['ip'] = get_client_ip();
      M('Api_record','tab_')->add($data);
      echo $info;exit;
  }
}