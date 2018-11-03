<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<!-- saved from url=(0028)http://tui.anfeng.com/users/ -->
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title><?php echo ($meta_title); ?>-个人中心</title> 
    <link href="/Public/Home/css/p_admin.css" rel="stylesheet" type="text/css">
    <link rel="icon" href="/Public/Media/image/s_logo.png"/>
    <script type="text/javascript" src="/Public/Home/js/jquery-1.11.1.min.js"></script>
    <script type="text/javascript" src="/Public/static/layer/layer.js" ></script>
    <script type="text/javascript" src="/Public/Home/js/common.js"></script>
     <!--[if lt IE 9]>
    <script type="text/javascript" src="/Public/static/jquery-1.10.2.min.js"></script>
    <![endif]--><!--[if gte IE 9]><!-->
    <?php echo hook('pageHeader');?>
</head>

<body id="member">

<!--头部个人信息-->
<div id="top_bar">
  <div id="top_bar_box" class="wrap_w clearfix">
    <div id="l"><a href="#">合作中心</a></div>
    <div id="r"><i><?php echo '今天是:'.date('Y',time()).'年-'.date('m',time()).'月-'.date('d',time()).'日';?></i><span>你好：<?php echo session('promote_auth.account');?></span><a href="<?php echo U('Public/logout');?>" >退出</a>
	
	</div>
  </div>
</div>
<!--结束 头部个人信息-->

<div class="page_main wrap_w clearfix">
  <div class="page_siderbar">
    <!--左边导航-->
    <div id="subnav" class="user_menu">
      <ul>
        <li>
          <h3><span class="ficon ficon-home"></span>管理中心</h3>
          <p><a href="<?php echo U('Promote/index');?>">后台首页</a></p>
        </li>
        <li>
            <h3><span class="ficon ficon-game"></span>游戏管理</h3>
            <p><a href="<?php echo U('Apply/index');?>">申请游戏</a></p>
            <p><a href="<?php echo U('Apply/my_game');?>">我的游戏</a></p>
            
        </li>
        <li>
              <h3><span class="ficon ficon-pay"></span>充值管理</h3>
              <p><a href="<?php echo U('Promote/alipay');?>">支付宝充值</a></p>
              <p><a href="<?php echo U('Promote/alipay_list');?>">支付宝充值平台币明细</a></p>

          </li>
        <li>
            <h3><span class="ficon ficon-act"></span>代充管理</h3>
             <p><a href="<?php echo U('Charge/agent_pay');?>">会长代充</a></p>
             <p><a href="<?php echo U('Charge/agent_pay_list');?>">代充汇总</a></p>
        <?php if($parent_id == 0): ?><!-- <p><a href="<?php echo U('Charge/agency');?>">转移平台币</a></p>
             <p><a href="<?php echo U('Charge/agency_list');?>">转移记录</a></p>--><?php endif; ?>
	           <p><a href="<?php echo U('Charge/agency');?>">转移平台币</a></p>
             <p><a href="<?php echo U('Charge/agency_list');?>">转移平台币记录</a></p>
             <p><a href="<?php echo U('Charge/agency_bang');?>">转移绑定平台币</a></p>
             <p><a href="<?php echo U('Charge/agency_bang_list');?>">转移绑定平台币记录</a></p>  
             <p><a href="<?php echo U('Charge/promote_game_list');?>">游戏绑定平台币余额记录</a></p>
            <!-- <p><a href="<?php echo U('Charge/agent_list');?>">消费平台币</a></p>
            <p><a href="<?php echo U('Charge/fill_list');?>">申请额度</a></p>
            <p><a href="<?php echo U('Charge/transfer_list');?>">平台币交易转移</a></p> -->
        </li>
        <li>
            <h3><span class="ficon ficon-docs"></span>数据管理</h3>
            <p><a href="<?php echo U('Query/recharge');?>">充值明细</a></p>
            <p><a href="<?php echo U('Query/bindrecharge');?>">绑币充值明细</a></p>
            <p><a href="<?php echo U('Query/register');?>">注册明细</a></p>
        </li>
        <li>
            <h3><span class="ficon ficon-star"></span>财务管理</h3>
            <p><a href="<?php echo U('Query/my_earning');?>">我的结算</a></p>
            <p><a href="<?php echo U('Query/bill');?>">账单查询</a></p>
        </li>
        <li>
            <h3><span class="ficon ficon-person"></span>账户管理</h3>
            <p><a href="<?php echo U('Promote/base_info');?>">我的基本信息</a></p>
            <?php if(PARENT_ID == 0): ?><p id="mychlid"><a href="<?php echo U('Promote/mychlid');?>">子帐号管理</a></p><?php endif; ?>
        </li>
      </ul>
    </div>
    <!--结束 左边导航-->  
  </div>

  <div class="page_content">
    <div id="container">
        
    <script src="/Public/static/layer/layer.js" type="text/javascript"></script>
    <script src='/Public/static/zeroclipboard/jquery.zclip.min.js'></script>
      <!-- <div id="total"> -->
        <div class="yulan">
          <div class="user">
            <p><img src="/Public/Home/images/promote/ico_user.jpg"> <i><?php echo session('promote_auth.account');?></i></p>
          </div>
          <ul class="clearfix">
                      <li>
                          <div class="box">平台币余额<span>￥<?php echo ($balance); ?></span></div>
            </li>
            <li>
              <div class="box">昨日收入<span>￥<?php echo ($yesterday); ?></span></div>
            </li>
            <li>
              <div class="box">今日收入<span>￥<?php echo ($today); ?></span></div>
            </li>
            <li>
              <div class="box">本月收入<span>￥<?php echo ($month); ?></span></div>
            </li>
            <li>
              <div class="box">总计收入<span>￥<?php echo ($total); ?></span></div>
            </li>
          </ul>
        </div>
        <style>
            .sharelink {
                margin: 20px auto;
                border:1px solid #eee;
            }
            .sharelinktitle {
                background:#eee;
                line-height:40px;
                font-size:16px;
                font-weight:bold;
                border-bottom:1px solid #eee;
                padding-left:10px;
            }
            .sharelinkcontent {
                clear:both;
                padding: 20px;
                position:relative;
                line-height:38px;
            }
            .sharelinkcontent:after{
                content:' ';
                overflow:hidden;
                display:inline-table;
                clear:both;
            }
            .sharelinkcontent span{                
                display:block;
                line-height:30px;                
                float:left;                
            }
            .slc1 input {
                border:1px solid #eee;
                border-right:none;
                padding:2px 6px;
                line-height:30px;
                outline:none;
                width:360px;
            }
            .slc2 {
                background:#e02222;
                color:#fff;
                border:1px solid #e02222;
                cursor:pointer;
                padding:2px 6px;
            }
            #copytip{
                position:absolute;                
                z-index:8888;
                border:1px solid #eee;
                padding:2px 8px;
                background:#fff;
                top:-14px;
                left:490px;
                font-size:14px;
                line-height:24px;
                border-radius:5px;
            }
        </style>
        <div class="sharelink">
            <div class="sharelinktitle">全站推广链接</div>
            <div class="sharelinkcontent">
                <span class="slc1"><input type="text" id="links" value="<?php echo ($url); ?>" /></span>
                <span class="slc2" id="copybtn">复制推广链接</span>
            </div>
        </div>
        <script src="/Public/Home/js/jquery.zclip.js"></script>
        <script>
            $(function() {
                $('#copybtn').zclip({
                    path:'/Public/static/zeroclipboard/ZeroClipboard.swf',
                    copy: $('#links').val(),
                    afterCopy: function() {
                        $('#copytip').remove();
                        layer.msg("复制成功",{icon:1});
                        setTimeout(function() {
                            $('#copytip').remove();
                        },3000);
                    }
                });
            });
        </script>
        <div id="news">
         <h2>新闻公告</h2>
         <ul>
            <!-- <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li><span><?php echo ($vo["create_time"]); ?></span><p>·<a target="_blank" href="#"><?php echo ($vo["title"]); ?></a></p></li><?php endforeach; endif; else: echo "" ;endif; ?> -->
            <?php $__CATE__ = D('Category')->getChildrenId(39);$__LIST__ = D('Document')->lists_limit($__CATE__, '`level` DESC,`id` DESC', 1,true,20); if(is_array($__LIST__)): $i = 0; $__LIST__ = $__LIST__;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$article): $mod = ($i % 2 );++$i;?><li>
                <span class="fr">
                   <?php echo (date('Y-m-d',$article["create_time"])); ?>
                </span>
                <p>
                  <a href="<?php echo U('Home/Article/detail?id='.$article['id']);?>" target="_blank">
                    【公告】<?php echo ($article["title"]); ?>
                  </a>
                </p>
              </li><?php endforeach; endif; else: echo "" ;endif; ?>
          </ul>
        </div>

    </div>
  </div>
</div>
<!--底部信息-->
<div class="copyright">
  <div class="links"><a href="<?php echo U('Article/detail',array('id'=>32));?>">关于我们</a>|<a href="<?php echo U('Article/lists',array('category'=>tui_gg));?>">游戏公告</a>|<a href="<?php echo U('Article/lists',array('category'=>tui_zx));?>">游戏资讯</a></div>
  <div class="kf">
    <span>
        <span>客服电话：<?php echo C("MT_SITE_T2");?></span>
        <span>客服邮箱：<?php echo C("MT_SITE_E2");?></span>
        <span>服务时间：<?php echo C("MT_SITE_TIME2");?></span>
    </span>
  </div>
  <p>网络备案:<?php echo C('WEB_SITE_ICP2');?>&nbsp;&nbsp;网络文化经营许可证编号：<?php echo C('MT_SITE_LICENSE2');?>
                 版权所有:<?php echo C('MT_SITE_B2');?></p>
</div>
<!--结束 底部信息-->
</body>
</html>
<script type="text/javascript">
  var $window = $(window), $subnav = $("#subnav"), url;
  /* 左边菜单高亮 */
  url = window.location.pathname + window.location.search;
  url = url.replace(/(\/(p)\/\d+)|(&p=\d+)|(\/(id)\/\d+)|(&id=\d+)|(\/(group)\/\d+)|(&group=\d+)/, "");
  $subnav.find("a[href='" + url + "']").parent().addClass("cur");
  //导航高亮
  // function highlight_subnav(url){
  //   alert(url);
  //   $('.user_menu').find('a[href="'+url+'"]').closest('li').addClass('cur');
  // }
 if($('#data_form').length>0){
   $("#pagehtml a").on("click",function(event){
    event.preventDefault();//使a自带的方法失效，即无法调整到href中的URL(http://www.baidu.com)
    var geturl = $(this).attr('href');
    $('#data_form').attr('action',geturl);
    $('#data_form').submit();

})
};
</script>