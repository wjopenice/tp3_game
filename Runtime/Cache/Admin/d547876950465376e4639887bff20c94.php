<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo ($meta_title); ?>-<?php echo C('WEB_SITE_TITLE');?></title>
    <!-- <link href="/Public/favicon.ico" type="image/x-icon" rel="shortcut icon"> -->
    <link href="<?php echo get_cover(C('SITE_ICO'),'path');?>" type="image/x-icon" rel="shortcut icon">
    <link rel="stylesheet" type="text/css" href="/Public/Admin/css/base.css" media="all">
    <link rel="stylesheet" type="text/css" href="/Public/Admin/css/common.css" media="all">
    <link rel="stylesheet" type="text/css" href="/Public/Admin/css/module.css">
    <link rel="stylesheet" type="text/css" href="/Public/Admin/css/style.css" media="all">
    <link rel="stylesheet" type="text/css" href="/Public/Admin/css/<?php echo (C("COLOR_STYLE")); ?>.css" media="all">
     <!--[if lt IE 9]>
    <script type="text/javascript" src="/Public/static/jquery-1.10.2.min.js"></script>
    <![endif]--><!--[if gte IE 9]><!-->
    <script type="text/javascript" src="/Public/static/jquery-2.0.3.min.js"></script>
    <script type="text/javascript" src="/Public/Admin/js/jquery.mousewheel.js"></script>
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
    
</head>
<body>
    <!-- 头部 -->
    <div class="header">
        <!-- Logo -->
        <span class="logo" ><img src="<?php echo get_cover(C('HT_LOGO'),'path');?>" width="100%" height="100%" style="width: 150px;height: 40px;padding-top: 5px;" /></span>
        <!-- /Logo -->

        <!-- 主导航 -->
        <ul class="main-nav">
            <?php if(is_array($__MENU__["main"])): $key = 0; $__LIST__ = $__MENU__["main"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$menu): $mod = ($key % 2 );++$key;?><li class="<?php echo ((isset($menu["class"]) && ($menu["class"] !== ""))?($menu["class"]):''); ?>"><a href="<?php echo (U($menu["url"])); ?>"><i class="menu_<?php echo ($key); ?>"></i><?php echo ($menu["title"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
        </ul>
        <!-- /主导航 -->

        <!-- 用户栏 -->
        <div class="user-bar">
            <span style="display:block;float:left;margin:0 10px;color:#fff;">你好，<em title="<?php echo session('user_auth.username');?>"><?php echo session('user_auth.username');?></em></span>
            <a href="javascript:;" style="float:left;" class="user-entrance"><i class="icon-user"></i></a>
            <ul class="nav-list user-menu hidden">
                <li><i  class="man_modify"></i><a href="/media.php" target="_blank">网站首页</a></li>
                <li><i  class="man_modify"></i><a href="<?php echo U('User/updatePassword');?>">修改密码</a></li>
                <li><i  class="man_quit"></i><a href="<?php echo U('Public/logout');?>">退出</a></li>
            </ul>   
        </div>
    </div>
    <!-- /头部 -->
    <!-- 边栏 -->
    <div class="sidebar">
        <div class="user_nav">
           <span><img src="/Public/Admin/images/tx.jpg"></span>
           <p><?php echo session('user_auth.username');?></p>
           <p style="margin-top:0px;"><!-- 管理员 --><?php echo ($quanxian); ?></p>
        </div>
        <div  class="fgx">功能菜单</div>
        <!-- 子导航 -->
        
            <div id="subnav" class="subnav">
                <?php if(!empty($_extra_menu)): ?>
                    <?php echo extra_menu($_extra_menu,$__MENU__); endif; ?>
                <?php if(is_array($__MENU__["child"])): $i = 0; $__LIST__ = $__MENU__["child"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sub_menu): $mod = ($i % 2 );++$i;?><!-- 子导航 -->
                    <?php if(!empty($sub_menu)): if(!empty($key)): ?><h3><i class="icon icon-unfold"></i><?php echo ($key); ?></h3><?php endif; ?>
                        <ul class="side-sub-menu">
                            <?php if(is_array($sub_menu)): $i = 0; $__LIST__ = $sub_menu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$menu): $mod = ($i % 2 );++$i;?><li>
                                    <a class="item" href="<?php echo (U($menu["url"])); ?>"><?php echo ($menu["title"]); ?></a>
                                </li><?php endforeach; endif; else: echo "" ;endif; ?>
                        </ul><?php endif; ?>
                    <!-- /子导航 --><?php endforeach; endif; else: echo "" ;endif; ?>
            </div>
        
        <!-- /子导航 -->
    </div>
    <!-- /边栏 -->

    <!-- 内容区 -->
    <div id="main-content">
        <div id="top-alert" class="fixed alert alert-error" style="display: none;">
            <button class="close fixed" style="margin-top: 4px;">&times;</button>
            <div class="alert-content">这是内容</div>
        </div>
        <div id="main" class="main">
            
            <!-- nav -->
            <?php if(!empty($_show_nav)): ?><div class="breadcrumb">
                <span>您的位置:</span>
                <?php $i = '1'; ?>
                <?php if(is_array($_nav)): foreach($_nav as $k=>$v): if($i == count($_nav)): ?><span><?php echo ($v); ?></span>
                    <?php else: ?>
                    <span><a href="<?php echo ($k); ?>"><?php echo ($v); ?></a>&gt;</span><?php endif; ?>
                    <?php $i = $i+1; endforeach; endif; ?>
            </div><?php endif; ?>
            <!-- nav -->
            

            
<link rel="stylesheet" type="text/css" href="/Public/Admin/css/admin_table.css" media="all">
<script type="text/javascript" src="/Public/static/uploadify/jquery.uploadify.min.js"></script>
    <div class="main-title cf">
        <h2>新增 [游戏]</h2>
    </div>
    <!-- 标签页导航 -->
<div class="tab-wrap">
    <ul class="tab-nav nav">
        <li data-tab="tab1" class="current"><a href="javascript:void(0);">基础</a></li>
        <li data-tab="tab2" ><a href="javascript:void(0);">扩展</a></li>
        <li data-tab="tab3" ><a href="javascript:void(0);">设置</a></li>
    </ul>
    <div class="tab-content zc_for">
    <!-- 表单 -->
    <form id="form" action="<?php echo U('add');?>" method="post" class="form-horizontal">
        <!-- 基础 -->
        <div id="tab1" class="tab-pane in tab1 tab-look">
    		<table  border="0" cellspacing="0" cellpadding="0">
                <tbody>
                  <tr>
                    <td class="l">游戏名称：</td>
                    <td class="r">
                        <input type="text" class="txt " name="game_name" value="" placeholder="请输入游戏名称">
                        <input type="hidden"  name="game_appid" value="">
                    </td>
                    <td class="l">游戏地址：</td>
                    <td class="r" >
                        <input type="text" class="txt " name="game_address" value="">
                    </td>
                  </tr>
                  <tr>
                    <td class="l">游戏类型：</td>
                    <td class="r">
                        <select id="game_type_id" name="game_type_id">                            
                            <?php $_result=get_game_type_all();if(is_array($_result)): $i = 0; $__LIST__ = $_result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["id"]); ?>"><?php echo ($vo["type_name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                        </select>
                        <input type="hidden" id="game_type_name" name="game_type_name" value=""></input>
                    </td>
                    <td class="l">开放类型：</td>
                    <td class="r">
                        <label class="inp_radio">
                            <input type="radio" class="inp_radio" value="0" name="category" checked >不限                        
                            <?php $_result=get_opentype_all();if(is_array($_result)): $i = 0; $__LIST__ = $_result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><input type="radio" class="inp_radio" value="<?php echo ($vo["id"]); ?>" name="category" ><?php echo ($vo["open_name"]); endforeach; endif; else: echo "" ;endif; ?>
                        </label>
                    </td>
                </tr>

                <tr>
                    <td class="l">游戏折扣：</td>
                    <td class="r">
                        <input type="text" class="txt" name="discount" value="" placeholder="游戏折扣为1-10">
                    </td>
                    <td class="l">游戏版本号：</td>
                    <td class="r">
                        <input type="text" class="txt" name="version_num" value="" placeholder="格式为纯数字">
                    </td>
                  </tr>

                  <tr>
                    <td class="l">绑币折扣：</td>
                    <td class="r">
                        <input type="text" class="txt" name="bind_discount" value="" placeholder="请输入1-10的数字,如:4.5即为45%">
                        <span>请输入1-10的数字,如:4.5即为45%</span>
                    </td>
                  </tr>

                  <tr>
                    <td class="l">分成比例：</td>
                    <td class="r">
                        <input type="text" class="txt" name="ratio" value="" placeholder="分成比例为1~100%">
                    </td>
                    <td class="l">注册单价</td>
                    <td class="r">
                        <input type="text" class="txt" name="money" value="" placeholder="注册单价(元) 大于0的整数">
                    </td>
                  </tr>
                  <tr>
                    <td class="l">游戏排序：</td>
                    <td class="r">
                        <input type="text" class="txt" name="sort" value="">
                    </td>

                    <td class="l">游戏版本：</td>
                    <td class="r">
                        <input type="text" class="txt" name="version" value="">
                    </td>
                    <!-- <td class="l">游戏简写：</td>
                    <td class="r">
                        <input type="text" class="txt" name="short" value="">
                    </td> -->
                  </tr>
                  <tr>
                    <!-- <td class="l">游戏大小：</td>
                    <td class="r">
                        <input type="text" class="txt" name="game_size" value="">
                    </td> -->
                  </tr>
                  <tr>
                    <td class="l">游戏图标：<span class="infonotice2">(尺寸：115*115px)</span> </td>
                    <td class="r">
                        <input type="file" id="upload_picture_icon">
                        <input type="hidden" name="icon" id="cover_id_icon"/>
                        <div class="upload-img-box">
                        <?php if(!empty($data['icon'])): ?><div class="upload-pre-item"><img src="<?php echo (get_cover($data['icon'],'path')); ?>"/></div><?php endif; ?>
                        </div> 
                                               
                    </td>
                    <td class="l">游戏封面：<span class="infonotice2">(尺寸：275*160px)</span>   </td>
                    <td class="r">
                        <input type="file" id="upload_picture_cover">
                        <input type="hidden" name="cover" id="cover_id_cover"/>
                        <div class="upload-img-box">
                        <?php if(!empty($data['cover'])): ?><div class="upload-pre-item"><img src="<?php echo (get_cover($data['cover'],'path')); ?>"/></div><?php endif; ?>
                        </div>
                        
                    </td>
                  </tr>
                  <tr>
                      <td class="l">游戏截图：<span class="infonotice2">(尺寸：750*1334px)</span>   </td>
                      <td class="r" >
                          <?php echo hook('UploadImages', array('name'=>'screenshot','value'=>''));?>
                          
                      </td>

                    <td class="l">游戏简介：</td>750*1334
                    <td class="r" >
                        <input type="text" class="txt " name="features" style="" value="">
                    </td>
                  </tr>
                  <tr>
                    <td class="l">游戏评分：</td>
                    <td class="r">
                        <input type="text" class="txt" name="game_score" value="" placeholder="请输入小于10的一位小数">
                    </td>
                    <td class="l">推荐指数：</td>
                    <td class="r">
                        <input type="text" class="txt" name="recommend_level" value="" placeholder="请输入小于10的一位小数">
                    </td>
                  </tr>
                  <tr>
                    <td class='l'>游戏大小</td>
                    <td class='r'><input type="text" class='txt' name='game_size' value=""></td>
                    <td  class='l'>虚拟下载量</td>
                    <td  class='r'><input type="text" class='txt' name='dow_mynum' value=""></td>
                  </tr>
                </tbody>
            </table>
        </div>
        <!-- 扩展 -->
        <div id="tab2" class="tab-pane  tab2 tab-look">
            <table  border="0" cellspacing="0" cellpadding="0">
                <tbody>
                  <tr>
                    <td class="l">游戏状态：</td>
                    <td class="r">
                        <label class="inp_radio">
                            <input type="radio" class="inp_radio" value="0" name="game_status" checked="checked">关闭
                            <input type="radio" class="inp_radio" value="1" name="game_status" >开启
                            <input type="radio" class="inp_radio" value="2" name="game_status" >下线

                        </label>
                    </td>
                    <td class="l">推荐状态：</td>
                    <td class="r">
                        <label class="inp_radio">
                            <input type="radio" class="inp_radio" value="0" name="recommend_status">不推荐
                            <input type="radio" class="inp_radio" value="1" name="recommend_status" checked="checked">推荐
                            <input type="radio" class="inp_radio" value="2" name="recommend_status" >热门
                            <input type="radio" class="inp_radio" value="3" name="recommend_status" >最新
                        </label>
                    </td>
                  </tr>
                  <tr>
                    <!-- <td class="l">充值状态：</td>
                    <td class="r">
                        <label class="inp_radio">
                            <input type="radio" class="inp_radio" value="0" name="pay_status">关闭
                            <input type="radio" class="inp_radio" value="1" name="pay_status" checked="checked">开启
                        </label>
                    </td> -->
                    <td class="l">下载状态：</td>
                    <td class="r">
                        <label class="inp_radio">
                            <input type="radio" class="inp_radio" value="0" name="dow_status">关&nbsp;&nbsp;&nbsp;闭
                            <input type="radio" class="inp_radio" value="1" name="dow_status" checked="checked">开启
                        </label>
                    </td>
                     <td class="l">所属cp：</td>
                    <td class="r">
                        <input type="text" class="txt" name="cp_name" value="<?php echo ($data['cp_name']); ?>">
                    </td>
                  </tr>
                  <tr>
                    <td class="l">语言：</td>
                    <td class="r">
                        <input type="text" class="txt" name="language" value="">
                    </td>
                    <td class="l">开发商：</td>
                    <td class="r">
                        <input type="text" class="txt" name="developers" value="">
                    </td>

                  </tr>
                  <tr>
                    <td class="l">游戏币名称：</td>
                    <td class="r">
                        <input type="text" class="txt" name="game_coin_name" value="">
                    </td>
                    <td class="l">游戏币比例：</td>
                    <td class="r">
                        <input type="text" class="txt" name="game_coin_ration" value="">
                    </td>
                  </tr>
                  <tr>
                    <td class="l">详细介绍：</td>
                    <td class="r" colspan='3'>
                        <textarea name="introduction" class="txt_area"></textarea>
                    </td>
                  </tr>
                  <tr>
                    <td class="l">下线原因：</td>
                    <td class="r" colspan='3'>
                        <textarea name="off_reason" class="txt_area"><?php echo ($data['off_reason']); ?></textarea>
                    </td>
                  </tr>
                </tbody>
            </table>
        </div>
        <!-- 设置 -->
        <div id="tab3" class="tab-pane  tab3 tab-look">
            <table  border="0" cellspacing="0" cellpadding="0">
                <tbody>
                  <tr>
                    <td class="l">游戏登陆通知地址：</td>
                    <td class="r" colspan='3'>
                        <input type="text" class="txt txt_title" name="login_notify_url" value="">
                    </td>
                  </tr>
                  <tr>
                    <td class="l">游戏支付通知地址：</td>
                    <td class="r" colspan='3'>
                        <input type="text" class="txt txt_title" name="pay_notify_url" value="">
                    </td>
                  </tr>
                  <tr>
                    <td class="l">游戏角色获取地址：</td>
                    <td class="r" colspan='3'>
                        <input type="text" class="txt txt_title" name="game_role_url" value="">
                    </td>
                  </tr>
                  <tr>
                    <td class="l">游戏礼包领取地址：</td>
                    <td class="r" colspan='3'>
                        <input type="text" class="txt txt_title" name="game_gift_url" value="">
                    </td>
                  </tr>
                  <tr>
                    <td class="l">游戏key：</td>
                    <td class="r">
                        <input type="text" class="txt" name="game_key" value="">
                        (游戏支付通知时的加密key)
                    </td>
                    <td class="l">访问秘钥：</td>
                    <td class="r">
                        <input type="text" class="txt" name="access_key" value="Jl5RMy5UVVsnWSJWX1VAXlFSWCErI1EtJUQsJl9fVSg=">
                        (SDK访问服务器时的加密key)
                    </td>
                  </tr>
                  <tr>
                    <td class="l">威富通商户号：</td>
                    <td class="r">
                        <input type="text" class="txt" name="partner" value="">
                        (威富通商户号)
                    </td>
                    <td class="l">威富通秘钥：</td>
                    <td class="r" colspan='3'>
                        <input type="text" class="txt" name="key" value="">
                    </td>
                  </tr>
                  <tr>
                    <td class="l">游戏支付appid：</td>
                    <td class="r">
                        <input type="text" class="txt" name="game_pay_appid" value="">
                        (微信支付时用的的APPID)
                    </td>
                    <td class="l">游戏合作id：</td>
                    <td class="r" colspan='3'>
                        <input type="text" class="txt" name="agent_id" value="">

                    </td>
                  </tr>
                  <tr>
                    <td class="l">游戏包名：</td>
                    <td class="r">
                        <input type="text" class="txt" name="apk_pck_name" value="">
                        (安卓生成APK时的包名)
                    </td>
                    <td class="l">游戏签名：</td>
                    <td class="r" colspan='3'>
                        <input type="text" class="txt" name="apk_pck_sign" value="">
                        (安卓生成APK时的签名)
                    </td>
                  </tr>
                </tbody>
            </table>
        </div>
        <div class="form-item cf">
            <button class="btn submit-btn ajax-post hidden" id="submit" type="submit" target-form="form-horizontal">确 定</button>
            <a class="btn btn-return" href="javascript:history.back(-1);">返 回</a>
        </div>
    </form>
    </div>
</div>

        </div>
        <div class="cont-ft">
            <div class="copyright">
                <div class="fl">感谢使用<a href="http://www.vlcms.com">致晟</a>游戏运营平台 V2.0.6.15</div>
                <div class="fr"></div>
            </div>
        </div>
    </div>
    <!-- /内容区 -->
    <script type="text/javascript">
    (function(){
        var ThinkPHP = window.Think = {
            "ROOT"   : "", //当前网站地址
            "APP"    : "/admin.php?s=", //当前项目地址
            "PUBLIC" : "/Public", //项目公共目录地址
            "DEEP"   : "<?php echo C('URL_PATHINFO_DEPR');?>", //PATHINFO分割符
            "MODEL"  : ["<?php echo C('URL_MODEL');?>", "<?php echo C('URL_CASE_INSENSITIVE');?>", "<?php echo C('URL_HTML_SUFFIX');?>"],
            "VAR"    : ["<?php echo C('VAR_MODULE');?>", "<?php echo C('VAR_CONTROLLER');?>", "<?php echo C('VAR_ACTION');?>"]
        }
    })();
    </script>
    <script type="text/javascript" src="/Public/static/think.js"></script>
    <script type="text/javascript" src="/Public/Admin/js/common.js"></script>
    <script type="text/javascript">
        +function(){
            var $window = $(window), $subnav = $("#subnav"), url;
            $window.resize(function(){
                $("#main").css("min-height", $window.height() - 130);
            }).resize();

            /*初始化导航菜单*/
             $subnav.find(".icon").addClass("icon-fold");
             $subnav.find(".side-sub-menu").siblings(".side-sub-menu").hide();
            
            /* 左边菜单高亮 */
            url = window.location.pathname + window.location.search;
            url = url.replace(/(\/(p)\/\d+)|(&p=\d+)|(\/(id)\/\d+)|(&id=\d+)|(\/(group)\/\d+)|(&group=\d+)/, "");
            $subnav.find("a[href='" + url + "']").parent().addClass("current");
            //显示选中的菜单
            $subnav.find("a[href='" + url + "']").parent().parent().prev("h3").find("i").removeClass("icon-fold");
            $subnav.find("a[href='" + url + "']").parent().parent().show();

            /* 左边菜单显示收起 */
            $("#subnav").on("click", "h3", function(){
                var $this = $(this);
                $this.find(".icon").toggleClass("icon-fold");
                $this.next().slideToggle("fast").siblings(".side-sub-menu:visible").
                      prev("h3").find("i").addClass("icon-fold").end().end().hide();
            });
            $("#subnav h3 a").click(function(e){e.stopPropagation()});
            /* 头部管理员菜单 */
            $(".user-bar").mouseenter(function(){
                var userMenu = $(this).children(".user-menu ");
                userMenu.removeClass("hidden");
                clearTimeout(userMenu.data("timeout"));
            }).mouseleave(function(){
                var userMenu = $(this).children(".user-menu");
                userMenu.data("timeout") && clearTimeout(userMenu.data("timeout"));
                userMenu.data("timeout", setTimeout(function(){userMenu.addClass("hidden")}, 100));
            });

            /* 表单获取焦点变色 */
            $("form").on("focus", "input", function(){
                $(this).addClass('focus');
            }).on("blur","input",function(){
                        $(this).removeClass('focus');
                    });
            $("form").on("focus", "textarea", function(){
                $(this).closest('label').addClass('focus');
            }).on("blur","textarea",function(){
                $(this).closest('label').removeClass('focus');
            });

            // 导航栏超出窗口高度后的模拟滚动条
            var sHeight = $(".sidebar").height();
            var subHeight  = $(".subnav").height();
            var diff = subHeight - sHeight; //250
            var sub = $(".subnav");
            if(diff > 0){
                $(window).mousewheel(function(event, delta){
                    if(delta>0){
                        if(parseInt(sub.css('marginTop'))>-10){
                            sub.css('marginTop','0px');
                        }else{
                            sub.css('marginTop','+='+10);
                        }
                    }else{
                        if(parseInt(sub.css('marginTop'))<'-'+(diff-10)){
                            sub.css('marginTop','-'+(diff-10));
                        }else{
                            sub.css('marginTop','-='+10);
                        }
                    }
                });
            }
        }();
    </script>
    
<script type="text/javascript" src="/Public/static/datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript">
//alert($("#cover_id_icon").parent().find('.upload-img-box').html());
//导航高亮
highlight_subnav('<?php echo U('Game/lists');?>');
$('#submit').click(function(){
    var txt=$.trim($('input[name=cp_name]').val());
    $('input[name=cp_name]').val(txt);
    $('#form').submit();
});

$(function(){
    $("input[name='game_appid']").val("<?php echo generate_game_appid();?>");
    $("#game_type_name").val($("#game_type_id option:selected").text());
    
    $('.date').datetimepicker({
        format: 'yyyy-mm-dd',
        language:"zh-CN",
        minView:2,
        autoclose:true
    });
    $('.time').datetimepicker({
        format: 'yyyy-mm-dd hh:ii',
        language:"zh-CN",
        minView:2,
        autoclose:true
    });
    showTab();

});

/*获取游戏类型名称*/
$("#game_type_id").change(function() {
    $("#game_type_name").val($("#game_type_id option:selected").text());
});

//上传游戏图标
/* 初始化上传插件 */
$("#upload_picture_icon").uploadify({
    "height"          : 30,
    "swf"             : "/Public/static/uploadify/uploadify.swf",
    "fileObjName"     : "download",
    "buttonText"      : "上传图标",
    "uploader"        : "<?php echo U('File/uploadPicture',array('session_id'=>session_id()));?>",
    "width"           : 120,
    'removeTimeout'   : 1,
    'fileTypeExts'    : '*.jpg; *.png; *.gif;',
    "onUploadSuccess" : upload_picture_icon<?php echo ($field["name"]); ?>,
    'onFallback' : function() {
        alert('未检测到兼容版本的Flash.');
    }
});
function upload_picture_icon<?php echo ($field["name"]); ?>(file, data){
    var data = $.parseJSON(data);
    var src = '';
    if(data.status){
        $("#cover_id_icon").val(data.id);
        src = data.url || '' + data.path;
        $("#cover_id_icon").parent().find('.upload-img-box').html(
            '<div class="upload-pre-item"><img src="' + src + '"/></div>'
        );
    } else {
        updateAlert(data.info);
        setTimeout(function(){
            $('#top-alert').find('button').click();
            $(that).removeClass('disabled').prop('disabled',false);
        },1500);
    }
}


//上传游戏封面
/* 初始化上传插件 */
$("#upload_picture_cover").uploadify({
    "height"          : 30,
    "swf"             : "/Public/static/uploadify/uploadify.swf",
    "fileObjName"     : "download",
    "buttonText"      : "上传封面",
    "uploader"        : "<?php echo U('File/uploadPicture',array('session_id'=>session_id()));?>",
    "width"           : 120,
    'removeTimeout'   : 1,
    'fileTypeExts'    : '*.jpg; *.png; *.gif;',
    "onUploadSuccess" : upload_picture_cover<?php echo ($field["name"]); ?>,
    'onFallback' : function() {
        alert('未检测到兼容版本的Flash.');
    }
});
function upload_picture_cover<?php echo ($field["name"]); ?>(file, data){
    var data = $.parseJSON(data);
    var src = '';
    if(data.status){
        $("#cover_id_cover").val(data.id);
        src = data.url || '' + data.path;
        $("#cover_id_cover").parent().find('.upload-img-box').html(
            '<div class="upload-pre-item"><img src="' + src + '"/></div>'
        );
    } else {
        updateAlert(data.info);
        setTimeout(function(){
            $('#top-alert').find('button').click();
            $(that).removeClass('disabled').prop('disabled',false);
        },1500);
    }
}
</script>

</body>
</html>