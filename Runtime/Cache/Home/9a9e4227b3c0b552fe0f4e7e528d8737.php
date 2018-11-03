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
					<a href="http://www.zhishengwh.com" title="" class="logo" style="background:url(<?php echo ($logo); ?>) no-repeat 0 14.5px;width: 160px;"></a>
			
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
			
<div class="news_list">
    <ul  class="list container">
    <?php $__CATE__ = D('Category')->getChildrenId($category['id']);$__LIST__ = D('Document')->page(!empty($_GET["p"])?$_GET["p"]:1,10)->lists($__CATE__, '`level` DESC,`id` DESC', 1,true); if(is_array($__LIST__)): $i = 0; $__LIST__ = $__LIST__;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$article): $mod = ($i % 2 );++$i;?><li style="overflow: hidden;padding: 20px 0;border-bottom: 1px dashed #d6d6d6;">
            <h3 style="float: left;width: 750px;line-height: 28px;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;"><a style="font-size: 16px;font-weight: normal;" href="<?php echo U('Article/detail?id='.$article['id']);?>" target="_blank" title="<?php echo ($article["title"]); ?>"><?php echo ($article["title"]); ?></a></h3>
            <span style="float: left;width: 150px;line-height: 28px;text-align: center;"><?php echo (date('Y-m-d',$article["create_time"])); ?></span>
            <p style="float: left;width: 100px;padding: 0;line-height: 28px;"><?php echo ($article["description"]); ?><a href="<?php echo U('Article/detail?id='.$article['id']);?>">阅读全文&gt;&gt;</a></p>
        </li><?php endforeach; endif; else: echo "" ;endif; ?>
    </ul>
    <div class="page">
        <?php echo ((isset($_page) && ($_page !== ""))?($_page):''); ?>
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