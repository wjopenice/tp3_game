<?php
namespace Sdk\Controller;
use Think\Controller;
use User\Api\MemberApi;
use Org\XiguSDK\Xigu;
class UserController extends BaseController{

    /**
    *SDK用户登陆
    */
    public function user_login(){

        #获取SDK上POST方式传过来的数据 然后base64解密 然后将json字符串转化成数组
        $user = json_decode(base64_decode(file_get_contents("php://input")),true);
        #判断数据是否为空
        if(empty($user)){$this->set_message(0,"fail","登陆数据不能为空");}
        #实例化用户接口
        $userApi = new MemberApi();
        // $result = $userApi->login($user["account"],$user['password'],1,$user["game_id"],$user["game_name"]);#调用登陆
        $result = $userApi->login_($user["account"],$user['password'],1,$user["game_id"],$user["game_name"]);#调用登陆

        $res_msg = array();
        switch ($result) {
            case -1:
                $this->set_message(-1,"fail","用户不存在或被禁用");
                break;
            case -2:
                $this->set_message(-2,"fail","密码错误");
                break;
            default:
            //file_put_contents('/data/aaaaa.html',base64_decode(file_get_contents("php://input")));
                if($user['sdk_version']=='1'){
                      //判断用户是否实名认证
                    $wherebang['account']=$user["account"];
                    $banguser=M('user','tab_')->field('id,real_name,idcard')->where($wherebang)->find();
                    if (empty($banguser['real_name']) || empty($banguser['idcard']) ) {
                          $res_msg = array(
                                "status"=>-5,
                                "return_code" => "fail",
                                "return_msg"  => "未实名认证！！",
                                "user_id"     => $banguser["id"],
                            );
                    } else {
                        if(is_array($result)){
                            $user["user_id"] = $result['user_id'];
                            $this->add_user_play($user);
                            $res_msg = array(
                                "status"=>1,
                                "return_code" => "success",
                                "return_msg"  => "登陆成功",
                                "user_id"     => $user["user_id"],
                                "real_name"   => $banguser["real_name"],
                                "idcard"      =>$banguser['idcard'],
                                "token"=>$result['token'],
                            );
                        }else{
                            $this->set_message(0,"fail","未知错误");
                        }
                    }
                }else{
                        //echo $user['sdk_version'];exit;
                        if(is_array($result)){
                            $user["user_id"] = $result['user_id'];
                            $this->add_user_play($user);
                            $res_msg = array(
                                "status"=>1,
                                "return_code" => "success",
                                "return_msg"  => "登陆成功",
                                "user_id"     => $user["user_id"],
                                "real_name"   => $banguser["real_name"],
                                "idcard"      =>$banguser['idcard'],
                                "token"=>$result['token'],
                            );
                        }else{
                            $this->set_message(0,"fail","未知错误");
                        }
                }
               
                break;
        }
        echo base64_encode(json_encode($res_msg));
    }

    public function user_register(){
        #获取SDK上POST方式传过来的数据 然后base64解密 然后将json字符串转化成数组
        $user = json_decode(base64_decode(file_get_contents("php://input")),true);
        #判断数据是否为空
        if(empty($user)){
            $this->set_message(0,"fail","注册数据不能为空");
        }
        #实例化用户接口
        $userApi = new MemberApi();
        // $result = $userApi->register($user['account'],$user['password'],1,$user['promote_id'],$user['promote_account']);
        // user表加game_id
        $result = $userApi->register_($user['account'],$user['password'],1,$user['promote_id'],$user['promote_account'],$phone="",$user["game_id"],$user["game_name"]);
        $res_msg = array();
        if($result > 0){
            $this->set_message(1,"success","注册成功");
        }
        else{
            switch ($result) {
                case -3:
                    $this->set_message(-3,"fail","用户名已存在");
                    break;
                default:
                    $this->set_message(0,"fail","注册失败");
                    break;
            }
        }
    }

    /**
    *手机用户注册
    */
    public function user_phone_register(){
        #获取SDK上POST方式传过来的数据 然后base64解密 然后将json字符串转化成数组
        $user = json_decode(base64_decode(file_get_contents("php://input")),true);
        #判断数据是否为空
        if(empty($user)){$this->set_message(0,"fail","注册数据不能为空");}
        #验证短信验证码
        $this->sms_verify($user['account'],$user['code']);
        #实例化用户接口
        $userApi = new MemberApi();
        // $result = $userApi->register($user['account'],$user['password'],1,$user['promote_id'],$user['promote_account'],$user['account']);
        // // user表加game_id
        wite_text(json_encode($user),dirname(__FILE__).'/a.txt');
        $result = $userApi->register_($user['account'],$user['password'],1,$user['promote_id'],$user['promote_account'],$user['account'],$user["game_id"],$user["game_name"]);
        $res_msg = array();
        if($result > 0){
            session($user['account'],null);
            $this->set_message(1,"success","注册成功");
        }
        else{
            switch ($result) {
                case -3:
                    $this->set_message(-3,"fail","用户名已存在");
                    break;
                default:
                    $this->set_message(0,"fail","注册失败");
                    break;
            }
            
        }
    }

    /**
    *修改用户数据
    */
    public function user_update_data(){
        $user = json_decode(base64_decode(file_get_contents("php://input")),true);
        #判断数据是否为空
        if(empty($user)){$this->set_message(0,"fail","操作数据不能为空");}
        #实例化用户接口
        $data['id'] = $user['user_id'];
        $userApi = new MemberApi();
        switch ($user['code']) {
            case 'phone':
                #验证短信验证码
                $this->sms_verify($user['phone'],$user['sms_code']);
                $data['phone'] = $user['phone'];
                break;
            case 'nickname':
                $data['nickname'] = $user['nickname'];
                break;
            case 'pwd':
                $data['old_password'] = $user['old_password'];
                $data['password'] = $user['password'];
                break;
            case 'idcard':
                $data['real_name'] = $user['real_name'];
                $data['idcard'] = $user['idcard'];
                break;
            default:
                $this->set_message(0,"fail","修改信息不明确");
                break;
        }
        $result = $userApi->updateUser($data);
        if($result == -2){
            $this->set_message(-2,"fail","旧密码输入不正确");
        }
        else if($result == true){
            $this->set_message(1,"success","修改成功");
        }
        else{
            $this->set_message(0,"fail","修改失败");
        }
    }

    /**
    *忘记密码接口
    */
    public function forget_password(){
        $user = json_decode(base64_decode(file_get_contents("php://input")),true);
        $userApi = new MemberApi();
        #验证短信验证码
        $this->sms_verify($user['phone'],$user['code']);
        $result = $userApi->updatePassword($user['user_id'],$user['password']);
        if($result == true){
            $this->set_message(1,"success","修改成功");
        }
        else{
            $this->set_message(0,"fail","修改失败");
        }
    }

    /**
    *添加玩家信息
    */
    private function add_user_play($user = array()){
        $user_play = M("UserPlay","tab_");
        $map["game_id"] = $user["game_id"];
        $map["user_id"] = $user["user_id"];
        $res = $user_play->where($map)->find();
        if(empty($res)){
            $user_entity = get_user_entity($user["user_id"]);
            $data["user_id"] = $user["user_id"];
            $data["user_account"] = $user_entity["account"];
            $data["user_nickname"] = $user_entity["nickname"];
            $data["game_id"] = $user["game_id"];
            $data["game_appid"] = $user["game_appid"];
            $data["game_name"] = $user["game_name"];
            $data["server_id"] = 0;
            $data["server_name"] = "";
            $data["role_id"] = 0;
            $data['parent_id']=$user_entity["parent_id"];
            $data['parent_name']=$user_entity["parent_name"];
            $data["role_name"] = "";
            $data["role_level"] = 0;
            $data["bind_balance"] = 0;
            $data["create_time"] = time();
            $data["promote_id"] = $user_entity["promote_id"];
            $data["promote_account"] = $user_entity["promote_account"];
            $user_play->create();
            $user_play->add($data);
        }
    }
    //添加登录信息
    /**
    *短信发送
    */
    public function send_sms()
    {
        $data = json_decode(base64_decode(file_get_contents("php://input")),true);
        $phone = $data['phone'];
        /// 产生手机安全码并发送到手机且存到session
        $rand = rand(100000,999999);
        $xigu = new Xigu(C('sms_set.smtp'));
        $param = $rand.",".'1';
        $result = json_decode($xigu->sendSM(C('sms_set.smtp_account'),$phone,C('sms_set.smtp_port'),$param),true); 
        $result['create_time'] = time();
        //$r = M('Short_message')->add($result);
        #TODO 短信验证数据 
        if($result['send_status'] == '000000') {
            session($phone,array('code'=>$rand,'create_time'=>NOW_TIME));
            echo base64_encode(json_encode(array('status'=>1,'return_code'=>'success','return_msg'=>'验证码发送成功')));
        }
        else{
            $this->set_message(0,"fail","验证码发送失败，请重新获取");
        }
    }

    /**
    *用户基本信息
    */
    public function user_info(){
        $user = json_decode(base64_decode(file_get_contents("php://input")),true);
        $model = M("user","tab_");
        $data = array();
        switch ($user['type']) {
            case 0:
               $data = $model
                ->field("account,nickname,real_name,idcard,phone,balance,bind_balance,game_name")
                ->join("INNER JOIN tab_user_play ON tab_user.id = tab_user_play.user_id and tab_user.id = {$user['user_id']} and tab_user_play.game_id = {$user['game_id']}")
                ->find();
                break;
            default:
                $map['account'] = $user['user_id'];
                $data = $model->field("id,account,nickname,phone,balance")->where($map)->find();
                break;
        }
        
        if(empty($data)){
            $this->set_message(0,"fail","用户数据异常");
        }
        $data['phone'] = empty($data["phone"])?" ":$data["phone"];
        $data['status'] = 1;
        echo base64_encode(json_encode($data));
    }

    /**
    *用户平台币充值记录
    */
    public function user_deposit_record(){
        $data = json_decode(base64_decode(file_get_contents("php://input")),true);
        $map["user_id"] = $data["user_id"];
        $map["pay_status"] = 1;
        //$map["game_id"] = $data["game_id"];
        $deposit = M("deposit","tab_")->where($map)->order("create_time desc")->select();
        if(empty($deposit)){
            echo base64_encode(json_encode(array("status"=>0,"return_code"=>"fail","return_msg"=>"暂无记录")));exit();
        }
        $return_data['status'] = 1;
        $return_data['data'] = $deposit;
        echo base64_encode(json_encode($return_data));
    }
    

    /**
    *用户消费记录
    */
    public function user_spend_record(){
        $data = json_decode(base64_decode(file_get_contents("php://input")),true);
        $map["user_id"] = $data["user_id"];
        $map["pay_status"] = 1;
        //0平台币，1支付宝，2微信，3绑币
        //平台币和支付宝
        $spend = M("spend","tab_")->where($map)->field("pay_time,pay_way,pay_amount")->select();
        //绑币
        $bind_spend=M("bind_spend","tab_")->where($map)->field("pay_time,pay_amount")->select();
        foreach ($bind_spend as $key => $value) {
            $bind_spend[$key]['pay_way']=3;
        }
        if(empty($spend) && empty($bind_spend)){
            echo base64_encode(json_encode(array("status"=>0,"return_code"=>"fail","return_msg"=>"暂无记录")));exit();
        }elseif(empty($spend) && !empty($bind_spend)){
            $merge_spend=$bind_spend;
        }elseif(!empty($spend) && empty($bind_spend)){
            $merge_spend=$spend;
        }else{
            //合并 排序
            $merge_spend=array_merge($spend,$bind_spend);
        }
        
        $this->sort_array($merge_spend,'pay_time','desc');
        $return_data['status'] = 1;
        $return_data['data'] = $merge_spend;

        //file_put_contents('/home/wwwroot/test.898sm.com/Data/bbbbb.log',$return_data['status'].'----'.json_encode($return_data['data']));
        echo base64_encode(json_encode($return_data));
    }
     /** 
     * 对二维数组进行排序 
     * @param $array 
     * @param $keyid 排序的键值 
     * @param $order 排序方式 'asc':升序 'desc':降序 
     * @param $type  键值类型 'number':数字 'string':字符串 
     */  
    public function sort_array(&$array, $keyid, $order = 'asc', $type = 'number') {  
        if (is_array($array)) {  
            foreach ($array as $val) {  
                $order_arr[] = $val[$keyid];  
            }  
            $order = ($order == 'asc') ? SORT_ASC : SORT_DESC;  
            $type = ($type == 'number') ? SORT_NUMERIC : SORT_STRING;  
            array_multisort($order_arr, $order, $type, $array);  
        }  

    }
    /** 
     * 消息中心所有消息记录
     * @param $array 
     * @param $order 排序方式 'asc':升序 'desc':降序 
     * @author whh 
     */
    public function user_message_letter(){
        $data = json_decode(base64_decode(file_get_contents("php://input")),true);
        $del_map["rec_account"] = $data["user_account"];
        $del_map["status"]=1;//删除

        //查找用户删除的消息id
        $del_mids=M('message_letter','tab_')->where($del_map)->order('id desc')->getfield('message_id',true);
        if($del_mids){
           $del_str=implode($del_mids,',');
           $red_map['id']=array('not in ',$del_str); 
        }

        $red_map['rec_account']=$data["user_account"];
        $red_map['status']=0;//已读
        //查找用户已读但是未被删除的消息id
        $red_mids=M('message_letter','tab_')->where($red_map)->order('id desc')->getfield('message_id',true);

        //除去删除的消息以外，所有的信息详情
        $all_message=M('inside_letter','tab_')->where($red_map)->order('id desc')->getfield('id,title,send_content,create_time',true);

        if(empty($all_message)){
            echo base64_encode(json_encode(array("status"=>0,"return_code"=>"fail","return_msg"=>"暂无消息")));exit();
        }
        foreach ($all_message as $k => $v) {
            if (in_array($all_message[$k]['id'],$red_mids)) {
                //已读
                $all_message[$k]['is_read']=1;
            } else {
                //未读
                $all_message[$k]['is_read']=0;
            }
            
        }
        $return_data['status'] = 1;
        $return_data['data'] = $all_message;
        echo base64_encode(json_encode($return_data));
    }
      /** 
     * 消息中心--消息详情
     * @param $array 
     * @author whh 
     */
    public function user_letter_detail(){
        $data = json_decode(base64_decode(file_get_contents("php://input")),true);
        //查找这条消息这个用户是否读过
        $wherered['message_id']=$data['message_id'];
        $wherered['rec_account']=$data['user_account'];
        $is_detail=M('message_letter','tab_')->where($wherered)->find();
        //查询该消息详情

        $detail_data=M('inside_letter','tab_')->find($data['message_id']);
        if ($is_detail) {
            $return_data['status'] = 1;
            $return_data['data'] = $detail_data;
            echo base64_encode(json_encode($return_data));
        } else {
            $data['send_account']=$detail_data['send_account'];
            $data['rec_account']=$data['user_account'];
            $data['message_id']=$detail_data['id'];
            $data['create_time']=time();
            $red_add = M('message_letter','tab_')->add($data);
            $wherenum['id']=$detail_data['id'];
            $add_num = M('inside_letter','tab_')->where($wherenum)->setInc('number',1);
            $return_data['status'] = 1;
            $return_data['data'] = $detail_data;
            echo base64_encode(json_encode($return_data));
        }
        
    }
     /** 
     * 消息中心--删除消息
     * @param $array 
     * @author whh 
     */
    public function user_letter_del(){
        $data = json_decode(base64_decode(file_get_contents("php://input")),true);
        $wheredel['message_id']=$data['message_id'];
        $wheredel['rec_account']=$data['user_account'];
        $del['status']=1;
        $res=M('message_letter','tab_')->where($wheredel)->save($del);
        if ($res===false) {
            echo base64_encode(json_encode(array("status"=>0,"return_code"=>"fail","return_msg"=>"删除消息失败！")));exit();
        } else {
            $return_data['status'] = 1;
            $return_data['data'] = "删除消息成功！";
            echo base64_encode(json_encode($return_data));
        }
        
    }

    /**
    *用户领取礼包- 
    */
    public function user_gift_record(){
        $data = json_decode(base64_decode(file_get_contents("php://input")),true);
        $map["user_id"] = $data["user_id"];
        $map["game_id"] = $data["game_id"];
        $gift = M("GiftRecord","tab_")
        ->field("tab_gift_record.game_id,tab_gift_record.game_name,tab_giftbag.giftbag_name ,tab_giftbag.digest,tab_gift_record.novice,tab_gift_record.status,tab_giftbag.start_time,tab_giftbag.end_time")
        ->join("LEFT JOIN tab_giftbag ON tab_gift_record.gift_id = tab_giftbag.id where user_id = {$data['user_id']} and tab_gift_record.game_id = {$data['game_id']}")
        ->select();
        if(empty($gift)){
            echo base64_encode(json_encode(array("status"=>0,"return_code"=>"fail","return_msg"=>"暂无记录")));exit();
        }
        foreach ($gift as $key => $val) {
            $gift[$key]['icon'] = $this->set_game_icon($val[$key]['game_id']);
            $gift[$key]['now_time'] = NOW_TIME;
        }
        
        $return_data['status'] = 1;
        $return_data['data'] = $gift;
        echo base64_encode(json_encode($return_data));
    }

    /**
    *用户平台币(绑定和非绑定)
    */
    public function user_platform_coin(){
        $data = json_decode(base64_decode(file_get_contents("php://input")),true);
        $user_play = M("UserPlay","tab_");
        $platform_coin = array();
        $user_data = array();
        #非绑定平台币信息
        $user_data = get_user_entity($data["user_id"]);
        $platform_coin['status'] = 1;
        $platform_coin["balance"] = $user_data["balance"];
        #绑定平台币信息
        $map["user_id"] = $data["user_id"];
        $map["game_id"] = $data["game_id"];
        $user_data = $user_play->where($map)->find();
        $platform_coin["bind_balance"] = $user_data["bind_balance"];
        echo base64_encode(json_encode($platform_coin));
    }
    /**
    *获取支付方式
    */
    public function get_pay_server(){
       $data['status'] =1;
       $data['wx_game'] =0;
       $data['zfb_game'] =1;
       $data['jby_game'] =0;
       echo base64_encode(json_encode($data));
    }

    //解绑手机
    public function user_phone_unbind(){
        $data = json_decode(base64_decode(file_get_contents("php://input")),true);
        $this->sms_verify($data['phone'],$data['code']);
        $map['id']=$data['user_id'];
        $user=M('user','tab_')->where($map)->setField('phone',"");
        if($user){
            echo base64_encode(json_encode(array('status'=>1,'return_msg'=>'解绑成功')));

        }else{
             echo base64_encode(json_encode(array('status'=>-1,'return_msg'=>'解绑失败')));

        }
    }
}
