<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace Common\Api;
class GameApi {

	public function game_login(){

	}

	public function game_pay_notify($param=null,$code=1){

		$pay_map['pay_status'] = 1;
		$pay_map['pay_game_status'] = 0;
		if($code==2){
            $pay_map['pay_order_number'] = $param['out_trade_no'];
            $pay_data = M("Bind_spend","tab_")->where($pay_map)->find();
        }else{
            $pay_map['pay_order_number'] = $param['out_trade_no'];
            $pay_data = M("Spend","tab_")->where($pay_map)->find();
        }
		if(empty($pay_data)){
			$this->error_record("游戏已通知或未找到相关数据"); return true;
		}
		$game = M('GameSet',"tab_");
		$map['game_id'] = $pay_data['game_id'];
		$game_data = $game->where($map)->find();
		if(empty($game_data)){ $this->error_record("未找到指定游戏数据"); return false;}
		if(empty($game_data['pay_notify_url'])){$this->error_record("未设置游戏支付通知地址"); return false;}

		$md5_sign = md5($pay_data['pay_order_number'].$pay_data['pay_amount']."1".$pay_data['extend'].$game_data['game_key']);
		$data = array(
			"out_trade_no" => $pay_data['pay_order_number'],
			"price"        => $pay_data['pay_amount'],
			"pay_status"   => 1,
			"extend"       => $pay_data['extend'],
			"signType"     => "MD5",
			"sign"         => $md5_sign
		);
		$result = $this->post($data,$game_data['pay_notify_url']);
		if($result == "success"){
			$this->update_game_pay_status($pay_data['pay_order_number'],$code);
		}else{
			\Think\Log::record("游戏支付通知信息：".$result.";游戏通知地址：".$game_data['pay_notify_url']);
		}
	}

	/**
    *修改游戏支付状态
    */
    private function update_game_pay_status($out_trade_no="",$code=1){
        $result = false;
        $map['pay_order_number'] = $out_trade_no;
        $data = array("pay_game_status"=>1);
        switch ($code) {
            case 1:
                $result = M('spend',"tab_")->where($map)->setField($data);
                break;
            default:
                $result = M('BindSpend',"tab_")->where($map)->setField($data);
                break;
        }
        return $result;
    }

	public function error_record($msg=""){
		\Think\Log::record($msg);
	}

	/**
	*post提交数据
    */
    protected function post($param,$url){
    	$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($param));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);//要求结果为字符串且输出到屏幕上
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);//设置等待时间
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
    }

}