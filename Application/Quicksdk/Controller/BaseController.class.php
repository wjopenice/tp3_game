<?php
namespace Quicksdk\Controller;

class BaseController extends EncryptController
{    
	public function __construct()
	{
        /* 读取站点配置 */
        $config = api('Config/lists');
        C($config); //添加配置
        parent::__construct();
	}
	/**
	 *设置接口输出信息
	 *@param  int     $status 提示状态 
	 *@param  string  $return_code 提示代码
	 *@param  string  $return_msg  提示信息
	 *@return string  base64加密后的json格式字符串
	 *@author 小纯洁
	 */
	protected function output($code,$msg,$data)
	{
		if(empty($data))
		{    
			 $statusInfo['code'] = $code;
			 $statusInfo['msg'] = $msg;
             $return['statusInfo'] = $statusInfo;
		}else
		{
             $statusInfo['code'] = $code;
			 $statusInfo['msg'] = $msg;
             $return['statusInfo'] = $statusInfo;
             $return['data'] = $data;
		}
	    echo json_encode($return);
	    exit();
	}
	

}