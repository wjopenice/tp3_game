<?php
namespace Callback\Controller;
use Org\Itppay\itppay;
/**
 * 支付回调控制器
 * @author 小纯洁 
 */
class NotifyController extends BaseController {
    /**
    *通知方法
    */
    public function notify()
    {
        
        $apitype = I('get.apitype');#获取支付api类型
        if (IS_POST && !empty($_POST)) {
            $notify = $_POST;
        } elseif (IS_GET && !empty($_GET)) {
            $notify = $_GET;
            unset($notify['method']);
            unset($notify['apitype']);
        } else {
            $notify = file_get_contents("php://input");
            if(empty($notify)){
                $this->record_logs("Access Denied");
                exit('Access Denied');
            }
        }

        $pay_way = $apitype;
        if($apitype == "swiftpass"){$apitype = "weixin";}
        
        $pay = new \Think\Pay($pay_way, C($apitype));
        if ($pay->verifyNotify($notify)) {
            //获取回调订单信息
            $order_info = $pay->getInfo();
            if ($order_info['status']) {
                $pay_where = substr($order_info['out_trade_no'],0,2);
                $result = false;
                switch ($pay_where) {
                    case 'SP':
                        $result = $this->set_spend($order_info);
                        break;
                    case 'PF':
                        $result = $this->set_deposit($order_info);
                        break;
                    case 'AG':
                        $result = $this->set_agent($order_info); 
                        break;
                    case 'QD':
                        $result = $this->set_promoteDeposit($order_info);
                        break;
                    default:
                        exit('accident order data');
                        break;
                }
                if (I('get.method') == "return") {
                    //根据不同订单来源跳转对应的页面
                    switch ($pay_where) {
                        case 'SP':
                            redirect('http://'.$_SERVER['HTTP_HOST'].'/index.php?s=/Home/Promote/index.html');
                            break;
                        case 'PF':
                           redirect('http://'.$_SERVER['HTTP_HOST'].'/media.php?s=/Index/index.html');
                            break;
                        case 'AG':
                            redirect('http://'.$_SERVER['HTTP_HOST'].'/index.php?s=/Home/Promote/index.html');
                            break;
                        case 'QD':
                            redirect('http://'.$_SERVER['HTTP_HOST'].'/index.php?s=/Home/Promote/index.html');
                            break;
                        default:
                            redirect('http://'.$_SERVER['HTTP_HOST'].'/index.php?s=/Home/Promote/index.html');
                            break;
                    }
                   redirect('http://'.$_SERVER['HTTP_HOST'].'/index.php?s=/Home/Promote/index.html');
                } else {
                    $pay->notifySuccess();
                }
            }else{
                $this->record_logs("支付失败！");
            }
        }else{
            $this->record_logs("支付验证失败");
            redirect('http://'.$_SERVER['HTTP_HOST'].'/media.php',3,'支付验证失败');
        }
    }



    public function weixin_zl_notify(){
         /* *
         * 配置信息
         */
        $itppay_config["appid"]=C('weixin_zl.appid');//交易发起所属app
        $itppay_config["key"]=C('weixin_zl.key');//合作密钥

        /* *
         * 获取传递数据
         */
        $data = file_get_contents("php://input");
        $parameter = json_decode($data, true);
        $signature = $parameter["signature"];
        unset($parameter["signature"]);


        $order_info['trade_no']        =$parameter['orderNo'];
        $order_info['out_trade_no']    =$parameter['mchntOrderNo'];
        $pay_where = substr($order_info['out_trade_no'],0,2);
        /* *
         * 签名
         */
        $itpPay = new itpPay($itppay_config);
        $signature_local=$itpPay->setSignature($parameter);

        $logFile = fopen(dirname(__FILE__)."/log.txt", "a+");
        fclose(fopen(dirname(__FILE__)."log2.txt","w"));
        $logFile2 = fopen(dirname(__FILE__)."/log2.txt", "a+");
        if($signature && $signature == $signature_local){
            
            //$parameter["orderNo"]明天云平台生成的订单号
            //$parameter["mchntOrderNo"]商户订单号，可根据商户订单号查询商户网站中该订单信息，并执行业务处理
            //$parameter["orderDt"]下单日期
            //$parameter["paidTime"]订单支付完成时间
            //$parameter["extra"]附加数据
            //$parameter["paySt"]支付结果状态，0:待支付；1:支付中；2:支付成功；3:支付失败；4:已关闭
            
            switch($parameter["paySt"]){
                case 0:
                    fwrite($logFile, "[".$parameter["mchntOrderNo"]."]--->待支付\r\n");
                    fwrite($logFile2, "[".$parameter["mchntOrderNo"]."]--->待支付\r\n");
                    break;
                case 1:
                    fwrite($logFile, "[".$parameter["mchntOrderNo"]."]--->支付中\r\n");
                    fwrite($logFile2, "[".$parameter["mchntOrderNo"]."]--->支付中\r\n");
                    break;
                case 2:
                    fwrite($logFile, "[".$parameter["mchntOrderNo"]."]--->支付成功\r\n");
                    fwrite($logFile2, "[".$parameter["mchntOrderNo"]."]--->支付成功\r\n");
                    switch ($pay_where) {
                        case 'SP':
                            $result = $this->set_spend($order_info);
                            break;
                        case 'PF':
                            $result = $this->set_deposit($order_info);
                            break;
                        case 'AG':
                            $result = $this->set_agent($order_info); 
                            break;
                        case 'QD':
                            $result = $this->set_promoteDeposit($order_info);
                            break;
                        default:
                            exit('accident order data');
                            break;
                    }
                    break;
                case 3:
                    fwrite($logFile, "[".$parameter["mchntOrderNo"]."]--->支付失败\r\n");
                    fwrite($logFile2, "[".$parameter["mchntOrderNo"]."]--->支付失败\r\n");
                    break;
                case 4:
                    fwrite($logFile, "[".$parameter["mchntOrderNo"]."]--->已关闭\r\n");
                    fwrite($logFile2, "[".$parameter["mchntOrderNo"]."]--->已关闭\r\n");
                    break;
            }
            
            foreach($parameter as $k=>$v){
                fwrite($logFile, $k."=".$v."\r\n");
                fwrite($logFile2, $k."=".$v."\r\n");
            }
            fwrite($logFile, "\r\n\r\n");
            fwrite($logFile2, "\r\n\r\n");
            
        }else{
            fwrite($logFile, "[".$parameter["mchntOrderNo"]."]--->验签失败\r\n");
            fwrite($logFile2, "[".$parameter["mchntOrderNo"]."]--->验签失败\r\n");
        }
        fclose($logFile);
        fclose($logFile2);
        echo "{\"success\":\"true\"}";
    }


    public function weixin_zl_return(){
        /* *
         * 获取异步通知日志
         */
        dump(2222);die();
    }



    function wite_text($txt,$name){
        $myfile = fopen($name, "w") or die("Unable to open file!");
        fwrite($myfile, $txt);
        fclose($myfile);
    }
}