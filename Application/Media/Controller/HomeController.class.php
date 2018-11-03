<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Media\Controller;
use Think\Controller;

/**
 * 前台公共控制器
 * 为防止多分组Controller名称冲突，公共Controller名称统一使用分组名称
 */
class HomeController extends Controller {

    /* 空操作，用于输出404页面 */
    public function _empty(){
        $this->redirect('Index/index');
    }


    protected function _initialize(){
        /* 读取站点配置 */
        $config = api('Config/lists');
        C($config); //添加配置

        if(!C('WEB_SITE_CLOSE')){
            $this->error('站点已经关闭，请稍后访问~');
        }
    }

    public function __construct(){
        parent::__construct();
        $this->get_letter_number();
        // 右上角广告
        $single_img = M('adv','tab_')->where('pos_id=2 and status=1')->find();
        $single_img['data'] =__ROOT__. get_cover($single_img['data'],'path');
        $this->assign("single_img",$single_img);
    }
    /**
     * 获取站内信未读数
     */
    public function get_letter_number(){
        $where['status'] = 0;
        $all =  M('inside_letter','tab_')->where($where)->count('id');

        $where1['rec_account'] = session('member_auth.account');
        $read = M('message_letter','tab_')->where($where1)->group('message_id')->getfield('message_id',true);
        $read = count($read);
        $num=$all-$read;
        if($num<0){
           $num=0;
        }
        $this->assign('letter_number',$num);

    }

    /* 用户登录检测 */
    protected function login(){
        /* 用户登录检测 */
        is_login() || $this->error('您还没有登录，请先登录！', U('User/login'));
    }

}
