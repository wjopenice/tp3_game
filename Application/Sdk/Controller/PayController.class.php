<?php
namespace Sdk\Controller;
use Think\Controller;
use Common\Api\GameApi;
class PayController extends BaseController{

    private function pay($param=array()){

        //限制去吧皮卡丘游戏充值
        if($param['game_id'] == "33" || $param['game_id'] == "112" || $param['game_id'] == "212" || $param['game_id'] == "224" || $param['game_id'] == "199" ) {
            echo "游戏暂停充值";exit();
        }

        $table  = $param['code'] == 1 ? "spend" : "deposit";
        $prefix = $param['code'] == 1 ? "SP_" : "PF_";
        $out_trade_no = $prefix.date('Ymd').date('His').sp_random_string(4);
        $user = get_user_entity($param['user_id']);
        switch ($param['apitype']) {
            case 'swiftpass':
                $pay  = new \Think\Pay($param['apitype'],$param['config']);
                break;
            
            default:
                $pay  = new \Think\Pay($param['apitype'],C($param['config']));
                break;
        }
        $param['pay_source'] = empty($param['pay_source'])?0:$param['pay_source'];
        $vo   = new \Think\Pay\PayVo();
        $vo->setBody("充值记录描述")
            ->setFee($param['price'])//支付金额
            ->setPaySource($param['pay_source'])
            ->setTitle($param['title'])
            ->setBody($param['body'])
            ->setOrderNo($out_trade_no)
            ->setService($param['server'])
            ->setSignType($param['signtype'])
            ->setPayMethod("mobile")
            ->setTable($table)
            ->setPayWay($param['payway'])
            ->setGameId($param['game_id'])
            ->setGameName($param['game_name'])
            ->setGameAppid($param['game_appid'])
            ->setServerId(0)
            ->setServerName("")
            ->setUserId($param['user_id'])
            ->setAccount($user['account'])
            ->setUserNickName($user['nickname'])
            ->setPromoteId($user['promote_id'])
            ->setPromoteName($user['promote_account'])
            ->setExtend($param['extend'])
            ->setSdkVersion($param['sdk_version']);
        return $pay->buildRequestForm($vo);
    }

    /**
    *支付宝移动支付
    */
    public function alipay_pay(){
        #获取SDK上POST方式传过来的数据 然后base64解密 然后将json字符串转化成数组
        $request = json_decode(base64_decode(file_get_contents("php://input")),true);
        $game_set_data = get_game_set_info($request['game_id']);

        $request['apitype'] = "alipay";
        $request['config']  = "alipay";
        $request['signtype']= "MD5";
        $request['server']  = "mobile.securitypay.pay";
        $request['payway']  = 1;
        $data = $this->pay($request);
        
        $md5_sign = $this->encrypt_md5(base64_encode($data['arg']),$game_set_data["access_key"]);
        $data = array("orderInfo"=>base64_encode($data['arg']),"out_trade_no"=>$data['out_trade_no'],"order_sign"=>$data['sign'],"md5_sign"=>$md5_sign);
        echo base64_encode(json_encode($data));
    }
    /**
    *其他支付
    */
    public function outher_pay(){
        #获取SDK上POST方式传过来的数据 然后base64解密 然后将json字符串转化成数组 
        $request = json_decode(base64_decode(file_get_contents("php://input")),true);
        $game_set_data = get_game_set_info($request['game_id']);

        if(empty($game_set_data['partner']) || empty($game_set_data['key'])){
            $this->set_message(0,"faill","未设置此应用的威富通账号");
        }

        // if(($request['apk_pck_name'] != $game_set_data['apk_pck_name']) || ($request['apk_md5_sign'] != $game_set_data['apk_pck_sign'])){
        //     $this->set_message(0,"faill","游戏签名包与微信应该包名签名不符");
        // }

        $request['apitype'] = "swiftpass";
        $request['config']  = array("partner"=>$game_set_data['partner'],"email"=>"","key"=>$game_set_data['key']);
        $request['signtype']= "MD5";
        $request['server']  = "unified.trade.pay";
        $request['payway']  = 2;
        $result_data = $this->pay($request);
        
        $data['status'] = 1;
        $data['return_code'] = "success";
        $data['return_msg'] = "下单成功";
        $data['token_id'] = $result_data['token_id'];
        $data['out_trade_no'] = $result_data['out_trade_no'];
        //$data['partner'] = $game_set_data['partner']; //C('weixin.partner');
        //$data['key'] = $game_set_data['key'];
        $data['game_pay_appid'] = $game_set_data['game_pay_appid'];
        echo base64_encode(json_encode($data));
    }

    /**
     * 掌灵微信支付
     * @return [type] [description]
     */
    public function weixin_zl(){
        #获取SDK上POST方式传过来的数据 然后base64解密 然后将json字符串转化成数组 
        $request = json_decode(base64_decode(file_get_contents("php://input")),true);
        $prefix = $request['code'] == 1 ? "SP_" : "PF_";
        $out_trade_no = $prefix.date('Ymd').date('His').sp_random_string(4);
        $request['pay_order_number'] = $out_trade_no;
        $request['pay_status'] = 0;
        $request['pay_way']    = 2;
        $request['spend_ip']   = get_client_ip();
        if($request['code'] == 1 ){
            #TODO添加消费记录
            $this->add_spend($request);
        }else{
            #TODO添加平台币充值记录
            $this->add_deposit($request);
        }
        $data['status'] = 1;
        $data['return_code'] = "success";
        $data['return_msg']  = "下单成功";
        $data['appid']  =   C('weixin_zl.appid');
        $data['appkey']  =  C('weixin_zl.key');
        $data['out_trade_no'] = $out_trade_no;
        $data['notify_url'] = "http://" . $_SERVER['HTTP_HOST'] . "/callback.php/Notify/weixin_zl_notify";
        //$data['agent_id'] = C('jubaobar.parent');//"1234567890";
        echo base64_encode(json_encode($data));

    }
    /**
     * 查询支付结果
     * @return [type] [description]
     */
    public function weixin_zl_notify(){
        #获取SDK上POST方式传过来的数据 然后base64解密 然后将json字符串转化成数组 
        $request = json_decode(base64_decode(file_get_contents("php://input")),true);
        $pay_where = substr($request['out_trade_no'],0,2);
        $map['pay_order_number']=$request['out_trade_no'];
        if($pay_where=="SP"){
            $res=M('spend','tab_')->where($map)->find();
        }elseif($pay_way=="PF"){
            $res=M('deposit','tab_')->where($map)->find();
        }
        if($res['pay_status']==1){
            $data['status'] = 1;
            $data['return_msg']  = "支付成功";
        }else{
            $data['status'] = 0;
            $data['return_msg']  = "支付失败";
        }

        echo base64_encode(json_encode($data));
        
    }

    public function jubaobar_pay(){
        #获取SDK上POST方式传过来的数据 然后base64解密 然后将json字符串转化成数组
        $request = json_decode(base64_decode(file_get_contents("php://input")),true);
        $prefix = $request['code'] == 1 ? "SP_" : "PF_";
        $out_trade_no = $prefix.date('Ymd').date('His').sp_random_string(4);
        $request['pay_order_number'] = $out_trade_no;
        $request['pay_status'] = 0;
        $request['pay_way']    = 3;
        $request['spend_ip']   = get_client_ip();
        if($request['code'] == 1 ){
            #TODO添加消费记录
            $this->add_spend($request);
        }else{
            #TODO添加平台币充值记录
            $this->add_deposit($request);
        }
        $data['status'] = 1;
        $data['return_code'] = "success";
        $data['return_msg']  = "下单成功";
        $data['appid']  =   C("jubaobar.appid");
        $data['out_trade_no'] = $out_trade_no;
        //$data['agent_id'] = C('jubaobar.parent');//"1234567890";
        echo base64_encode(json_encode($data));
    }

    /**
    *平台币支付
    */
    public function platform_coin_pay(){
        #获取SDK上POST方式传过来的数据 然后base64解密 然后将json字符串转化成数组
        $request = json_decode(base64_decode(file_get_contents("php://input")),true);
        #记录信息
        $user_entity = get_user_entity($request['user_id']);
        $out_trade_no = "PF_".date('Ymd').date('His').sp_random_string(4);
        $request['order_number']     = $out_trade_no;
        $request['pay_order_number'] = $out_trade_no;
        $request['out_trade_no']     = $out_trade_no;
        $request['title'] = $request['title'];
        $request['pay_status'] = 1;
        $request['pay_way'] = 0;
        $request['spend_ip']   = get_client_ip();

        //限制去吧皮卡丘游戏充值
        if($request['game_id'] == "33" || $request['game_id'] == "112" || $request['game_id'] == "212" || $request['game_id'] == "224" || $request['game_id'] == "199"  ) {
            echo base64_encode(json_encode(array("status"=>-3,"return_code"=>"fail","return_msg"=>"游戏暂停充值")));
            exit();
        }

        $result = false;
        switch ($request['code']) {
            case 1:#非绑定平台币
                $user = M("user","tab_");
                if($user_entity['balance'] < $request['price']){
                    echo base64_encode(json_encode(array("status"=>-2,"return_code"=>"fail","return_msg"=>"余额不足")));
                    exit();
                }
                #扣除平台币
                $user->where("id=".$request["user_id"])->setDec("balance",$request['price']);
                #TODO 添加绑定平台币消费记录
                $result = $this->add_spend($request);
                break;
             case 2:#绑定平台币
                $user_play = M("UserPlay","tab_");
                $user_play_map['user_id'] = $request['user_id'];
                $user_play_map['game_id'] = $request['game_id'];
                $user_play_data = $user_play->where($user_play_map)->find();

                if($user_play_data['bind_balance'] < $request['price']){
                    echo base64_encode(json_encode(array("status"=>-2,"return_code"=>"fail","return_msg"=>"余额不足")));
                    exit();
                }
                #扣除平台币
                $user_play->where($user_play_map)->setDec("bind_balance",$request['price']);
                #TODO 添加绑定平台币消费记录
                $result = $this->add_bind_spned($request);
                break;
            default:
                echo base64_encode(json_encode(array("status"=>-3,"return_code"=>"fail","return_msg"=>"支付方式不明确")));
                exit();
            break;
        }
        $game = new GameApi();
        $game->game_pay_notify($request,$request['code']);
        if($result){
            echo base64_encode(json_encode(array("return_status"=>1,"return_code"=>"success","return_msg"=>"支付成功","out_trade_no"=>$out_trade_no)));
        }
        else{
            echo base64_encode(json_encode(array("status"=>-1,"return_code"=>"fail","return_msg"=>"支付失败")));
        }
    }

    /**
    *支付验证
    */
    public function pay_validation(){
        #获取SDK上POST方式传过来的数据 然后base64解密 然后将json字符串转化成数组
        $request = json_decode(base64_decode(file_get_contents("php://input")),true);
        $out_trade_no = $request['out_trade_no'];
        $pay_where = substr($out_trade_no,0,2);
        $result = 0;
        $map['pay_order_number'] = $out_trade_no;
        switch ($pay_where) {
            case 'SP':
                $data = M('spend','tab_')->field('pay_status')->where($map)->find();
                $result = $data['pay_status'];
                break;
            case 'PF':
                $data = M('deposit','tab_')->field('pay_status')->where($map)->find();
                $result = $data['pay_status'];
                break;
            case 'AG':
                $data = M('agent','tab_')->field('pay_status')->where($map)->find();
                $result = $data['pay_status'];
                break;
            default:
                exit('accident order data');
                break;
        }
        if($result){
            echo base64_encode(json_encode(array("status"=>1,"return_code"=>"success","return_msg"=>"支付成功")));
            exit();
        }else{
            echo base64_encode(json_encode(array("status"=>0,"return_code"=>"fail","return_msg"=>"支付失败")));
            exit();
        }
    }


    
}
