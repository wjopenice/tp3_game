<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE HTML>
<html>
	<head>
		<meta charset="UTF-8">
		<title><?php echo C('CH_SET_TITLE');?></title>
		<meta name="keywords" content="<?php echo C('CH_SET_META_KEY');?>" />
		<meta name="description" content="<?php echo C('CH_SET_META_DESC');?>" />
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta charset="utf-8">
		<meta name="renderer" content="webkit">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="description" content="<?php echo C('WEB_SITE_KEYWORD2');?>">
		<meta name="keywords" content="<?php echo C('WEB_SITE_DESCRI2');?>">
		<link href="/Public/Home/css/global.min.css" rel="stylesheet">
		<link href="/Public/Home/css/index_01.min.css" rel="stylesheet">
		<link rel="icon" href="/Public/Media/image/s_logo.png"/>
		<script type="text/javascript" src="/Public/Home/js/jquery-1.11.1.min.js"></script>
		<script type="text/javascript" src="/Public/static/layer/layer.js" ></script>
		<script type="text/javascript" src="/Public/Home/js/common.js"></script>
		<script>
        var _hmt = _hmt || [];
        (function() {
          var hm = document.createElement("script");
          hm.src = "https://hm.baidu.com/hm.js?bc19aa51515f215def6b091f540c83ea";
          var s = document.getElementsByTagName("script")[0]; 
          s.parentNode.insertBefore(hm, s);
        })();
        </script>
		<!--<![endif]-->
		<!-- 页面header钩子，一般用于加载插件CSS文件和代码 -->
		<?php echo hook('pageHeader');?>
	</head>
	<body>
		<div class="header">
			<div class="section topBar">
				<h1 class="hid"></h1>
					<?php $logo = C('CH_SET_LOGO');$logo = get_cover($logo);$logo = $logo['path']; ?>
					<a href="http://game.zhishengwh.com/media.php" title="" class="logo" style="background:url(<?php echo ($logo); ?>) no-repeat 0 14.5px;width: 160px;"></a>
			
				<p id="userInfo" class="uInfo"><?php echo ($ad["cName"]); ?></p>
			</div>
			<div class="nav">
				<ul class="section">
					<?php $__NAV__ = M('Channel')->field(true)->where("status=1")->order("sort")->select(); if(is_array($__NAV__)): $i = 0; $__LIST__ = $__NAV__;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$nav): $mod = ($i % 2 );++$i; if(($nav["pid"]) == "0"): ?><li>
                            <a href="<?php echo (get_nav_url($nav["url"])); ?>" target="<?php if(($nav["target"]) == "1"): ?>_blank<?php else: ?>_self<?php endif; ?>"><?php echo ($nav["title"]); ?></a>
                        </li><?php endif; endforeach; endif; else: echo "" ;endif; ?>
				</ul>
			</div>
		</div>
		<div class="intro-con">
			
<link href="/Public/Home/css/index.css" rel="stylesheet">
<div class="banner"> 
	<div class="carousel-main">
    <ul id="carouselMain">
      <li class="carousel-panel banner-0 active"> <span class="car-item car-bounce"></span></li>
      <li class="carousel-panel banner-1"> <span class="car-item car-bounce"></span> <a href="#" class="banner-btn">马上加入</a> </li>
      <li class="carousel-panel banner-2"> <span class="car-item car-left"></span> <span class="car-item car-right"></span> <a href="#" class="banner-btn">马上加入</a> </li>
      <li class="carousel-panel banner-3"> <span class="car-item car-top"></span> <span class="car-item car-bottom"></span> <a href="#" class="banner-btn">马上加入</a> </li>
    </ul>
  </div>
  <form action="<?php echo U('login');?>" method="post" class="login-form">
  <div class="loginBox">
    <div class="red logErr" id="qt_global_text"></div>
    <table class="formTb loginTb">
      <tbody>
        <tr>
          <th><label for="qt_account" class="form-label">帐号</label></th>
          <td><input class="form-control" type="text" name="account" placeholder="手机号/用户名/邮箱" autocomplete="off" id="qt_account"></td>
        </tr>
        <tr>
          <th><label for="qt_password" class="form-label">密码</label></th>
          <td><input class="form-control" type="password" name="password" maxlength="20" placeholder="输入您的密码" id="qt_password"></td>
        </tr>
        <tr>
          <th></th>
          <td>
            <!-- <label>
              <input type="checkbox" id="qtis_autologin" checked="checked" value="1">
                下次自动登录
              </label> -->
          </td>
        </tr>
        <tr>
          <td colspan="2"><input type="submit" value="登录" class="btn btn-b ajax-post" id="qt_btn" target-form='login-form'></td>
        </tr>
      </tbody>
    </table>
    <div class="change"> <a class="ca" href="<?php echo U('Index/register');?>">还没加入，马上注册</a> </div>
  </div>
  </form>
</div>

<div class="notices_box header">
    <p class="notices_text">平台公告</p>
    <?php $__CATE__ = D('Category')->getChildrenId(51);$__LIST__ = D('Document')->lists_limit($__CATE__, '`level` DESC,`id` DESC', 1,true,2); if(is_array($__LIST__)): $i = 0; $__LIST__ = $__LIST__;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;?><div class="notices_modular">
        <i style="float: left;">●</i><a href="<?php echo U('Article/detail?id='.$list['id']);?>" target="_blank" class="notices_title ofh" title="<?php echo ($list["title"]); ?> <?php echo (date('Y-m-d',$list["create_time"])); ?>"><?php echo ($list["title"]); ?></a>
        <span class="label_new"></span>
    </div><?php endforeach; endif; else: echo "" ;endif; ?>
    <a class="notices_more" href="<?php echo U('Article/lists?category=tui_news');?>">查看更多<i></i></a>
</div>

<div class="bgWhite moduleA"></div>

<div class="section addSteps moduleA">
  <h3 class="ind_titb">如何加入我们<br><font>How to join us</font></h3>
  <ul class="clear join_us">
    <li><i class="join_01"></i><font>STEP1</font><br><font>注册账号</font></li>
    <li><i class="join_02"></i><font>STEP2</font><br><font>创建渠道</font></li>
    <li><i class="join_03"></i><font>STEP3</font><br><font>获取资源</font></li>
    <li class="last"><i class="join_04"></i><font>STEP4</font><br><font>推广分成</font></li>
  </ul>
</div>
	<div class="bgWhite moduleA" style="padding-top:15px;">
		<div class="section">
			<h3 class="ind_titb">联系我们<br><font>Contact us</font></h3>
			<ul class="clear cont_us">
				<li>
					<i class="cont_adress"></i>
					<b>公司地址</b><br>
					<span><?php echo C('CH_SET_ADDRESS');?></span>					
				</li>
				<li>					
					<i class="cont_phone"></i>
					<b>联系方式</b><br>
					<span><?php echo C('CH_SET_SERVER_TEL');?></span>					
				</li>
				<li>					
					<i class="cont_email"></i>
					<b>公司邮箱</b><br>
					<span><?php echo C('CH_SET_SERVER_EMAIL');?></span>					
				</li>
			</ul>
		</div>
	</div>
	<div class="section moduleA">
		<h3 class="ind_titb">友情链接<br><font>Friendly link</font></h3>
		<div class="frdlink clear" >    
            <?php if(is_array($links)): $i = 0; $__LIST__ = $links;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><a target="_blank" href="<?php echo ($vo["link_url"]); ?>" title="<?php echo ($vo["title"]); ?>" ><?php echo ($vo["title"]); ?></a><?php endforeach; endif; else: echo "" ;endif; ?>
		</div>
	</div>

		</div>
		<div class="footer">  网络备案:<?php echo C('CH_SET_FOR_THE_RECORD');?>&nbsp;&nbsp;网络文化经营许可证编号：<?php echo C('CH_SET_LICENSE');?>
                 版权所有:<?php echo C('CH_SET_COPYRIGHT');?></div>
		<!-- <div class="toolBar">
			<ul>
				<li><a href="mailto:tui@vlcms.com" target="_blank"><span class="ico-contact"></span>联系我们</a></li>
				<li><a href="#" target="_blank"><span class="ico-help"></span>帮助中心</a></li>
				<li><a href="javascript:void(0)" id="goTop"><span class="ico-gotop"></span>回到顶部</a></li>
			</ul>
		</div> -->
		<script type="text/javascript">
			(function(){
				var ThinkPHP = window.Think = {
					"ROOT"   : "", //当前网站地址
					"APP"    : "/index.php?s=", //当前项目地址
					"PUBLIC" : "/Public", //项目公共目录地址
					"DEEP"   : "<?php echo C('URL_PATHINFO_DEPR');?>", //PATHINFO分割符
					"MODEL"  : ["<?php echo C('URL_MODEL');?>", "<?php echo C('URL_CASE_INSENSITIVE');?>", "<?php echo C('URL_HTML_SUFFIX');?>"],
					"VAR"    : ["<?php echo C('VAR_MODULE');?>", "<?php echo C('VAR_CONTROLLER');?>", "<?php echo C('VAR_ACTION');?>"]
				}
			})();
		</script>
	</body>
</html>