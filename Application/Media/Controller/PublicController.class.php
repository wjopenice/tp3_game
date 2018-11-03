<?php
namespace Media\Controller;
use Think\Controller;

class PublicController extends BaseController {



   public function login(){
        $refer = $_SERVER['HTTP_REFERER'];
        if($refer){
          $re = strpos($refer, $_SERVER['HTTP_HOST']);
          if($re !== false){
            if(strpos($refer, 'Public/login'))
            {
              session('refer',null); 
            }else if(strpos($refer, 'Member/preg'))
            {
              session('refer',null); 
            }else if(strpos($refer, 'Public/register'))
            {
              session('refer',null); 
            }else{
              session('refer',$refer); 
            }
            
            
          }
           
        }
        
        $this->display();
    }

    public function register(){
        $this->display();
    }
     /**
     * 图片验证码
     * @author zdd
     */
    public function verify($vid=''){
        $config = array(
            'seKey'     => 'ThinkPHP.CN',   //验证码加密密钥
            'fontSize'  => 16,              // 验证码字体大小(px)
            'imageH'    => 42,               // 验证码图片高度
            'imageW'    => 107,               // 验证码图片宽度
            'length'    => 4,               // 验证码位数
            'fontttf'   => '4.ttf',              // 验证码字体，不设置随机获取
        );
        $verify = new \Think\Verify($config);
        $verify->entry($vid);
    }

    /**
     * 判断输入的邮箱是否被绑定
     * @author whh
     */
   public  function emailbangcheck($email){
            $where['email']=I('email');
            //file_put_contents('E:/aaa.html',json_encode($where['email']));
            if (empty($where['email'])) {
               echo json_encode(array('status'=>-1,'msg'=>'邮箱不能为空!')); exit; 
            }
            $model=M('User','tab_');
            $count=$model->where($where)->count('id');
            //echo $count;exit;
            if($count>0){
                echo json_encode(array('status'=>-1,'msg'=>'该邮箱已被其他账号绑定')); exit;
            }else{
                echo json_encode(array('status'=>1,'msg'=>'OK')); exit;
            }
   }
    

}