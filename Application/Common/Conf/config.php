<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

/**
 * 系统配文件
 * 所有系统级别的配置
 */
return array(
    /*配置公共函数库*/
    "LOAD_EXT_FILE"=>"extend",
    // 加载扩展配置文件
    'LOAD_EXT_CONFIG' => 'pay_config', 
    /* 模块相关配置 */
    'AUTOLOAD_NAMESPACE' => array('Addons' => ONETHINK_ADDON_PATH), //扩展模块列表
    'DEFAULT_MODULE'     => 'Home',
    'MODULE_DENY_LIST'   => array('Common','User','Admin','Install'),
    //'MODULE_ALLOW_LIST'  => array('Home','Admin'),

    /* 系统数据加密设置 */
    'DATA_AUTH_KEY' => 'oq0d^*AcXB$-2[]PkFaKY}eR(Hv+<?g~CImW>xyV', //默认数据加密KEY

    /* 用户相关设置 */
    'USER_MAX_CACHE'     => 1000, //最大缓存用户数
    'USER_ADMINISTRATOR' => 1, //管理员用户ID

    /* URL配置 */
    'URL_CASE_INSENSITIVE' => false, //默认false 表示URL区分大小写 true则表示不区分大小写
    'URL_MODEL'            => 3, //URL模式
    'VAR_URL_PARAMS'       => '', // PATHINFO URL参数变量
    'URL_PATHINFO_DEPR'    => '/', //PATHINFO URL分割符

    /* 全局过滤配置 */
    'DEFAULT_FILTER' => '', //全局过滤函数

    /*错误日志配置*/
    'LOG_RECORD' => true, // 开启日志记录
    'LOG_LEVEL'  => 'EMERG,ALERT,CRIT,ERR', // 只记录EMERG ALERT CRIT ERR 错误
    'LOG_TYPE'   => 'File', // 日志记录类型 默认为文件方式

    /* 数据库配置 */
    'DB_TYPE'   => 'mysql', // 数据库类型
    'DB_HOST'   => '47.104.109.151', // 服务器地址
    'DB_NAME'   => 'u7858', // 数据库名
    'DB_USER'   => 'root', // 用户名
    'DB_PWD'    => '12345678',  // 密码
    'DB_PORT'   => '3306', // 端口
    'DB_PREFIX' => 'sys_', // 数据库表前缀

    /* 文档模型配置 (文档模型核心配置，请勿更改) */
    'DOCUMENT_MODEL_TYPE' => array(2 => '主题', 1 => '目录', 3 => '段落'),
    'SESSION_OPTIONS'         =>  array(
       // 'name'                =>  'BJYSESSION',                    //设置session名
        'expire'              =>  43200,                      //SESSION保存12小时
        //'use_trans_sid'       =>  1,                               //跨页传递
        //'use_only_cookies'    =>  0,                               //是否只开启基于cookies的session的会话方式
    ),
    'DATA_CACHE_TYPE' => 'Memcache',  // 数据缓存类型
    'MEMCACHE_HOST'   => '127.0.0.1',
    'MEMCACHE_PORT'   => '11211',
    'DATA_CACHE_TIME' => '3600',
);
