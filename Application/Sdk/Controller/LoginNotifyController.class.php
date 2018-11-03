<?php

namespace Sdk\Controller;
use Think\Controller;
use Common\Api\GameApi;
/**
 * 支付游戏回调控制器
 * @author 小纯洁 
 */
class LoginNotifyController extends Controller {

    /**
    *服务器登陆验证
    */
    public function login_verify_bak(){
        $msg = array();
        if(IS_POST && !empty($_POST)){
            $param = $_POST;
            $user = M("user","tab_")->find($param['user_id']);
            if($param['token'] == $user['token']){
                $msg = array(
                    "status"=>1,
                    "user_id"=>$user['id'],
                    "user_account"=>$user['account'],
                );
            }else{
                $msg = array(
                    "status"=>-1,
                    "msg"=>"验证失败",
                );
            }
        }else{
            $msg = array(
                "status"=>-2,
                "msg"=>"数据异常",
            );
        }
        echo json_encode($msg);
    }

    /**
    *服务器登陆验证
    */
    public function login_verify(){
        $msg = array();
        if(IS_POST && !empty($_POST)){ 
            $param = $_POST;
            $user = M("user","tab_")->find($param['user_id']);
            if($param['token'] == $user['token'])
            {
                //获取token的前三位
                $pre = substr($param['token'],0,3);
                //判断截取的前三位是否是kf_
                if($pre === 'kf_')
                {   
                    $UserClass = new Usercontroller;
                    $result = $UserClass->verify_token($param['token']);
                    //判断快发token验证是否通过
                    if($result !== false && $result['user_id'] == $user['id'])
                    {   //快发token验签通过
                        $msg = array(
                            "status"=>1,
                            "user_id"=>$user['id'],
                            "user_account"=>$user['account'],
                        ); 
                    }else
                    {   //验签失败
                        $msg = array(
                            "status"=>-1,
                            "msg"=>"快发验证失败",
                        );
                    }
                }else
                {
                    $msg = array(
                        "status"=>1,
                        "user_id"=>$user['id'],
                        "user_account"=>$user['account'],
                    );
                }
                
            }else
            {
                $msg = array(
                    "status"=>-1,
                    "msg"=>"验证失败",
                );
            }
        }else{
            $msg = array(
                "status"=>-2,
                "msg"=>"数据异常",
            );
        }
        echo json_encode($msg);
    }



}
