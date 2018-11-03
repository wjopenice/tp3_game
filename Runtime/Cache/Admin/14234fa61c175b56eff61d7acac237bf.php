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
            

            
<link rel="stylesheet" type="text/css" href="/Public/static/bootstrap.min.css" media="all">
<!-- <link rel="stylesheet" type="text/css" href="/Public/Admin/css/admin_table.css" media="all"> -->
<link rel="stylesheet" type="text/css" href="/Public/Admin/css/combo.select.css" media="all">
<script src="/Public/Admin/js/jquery.combo.select.js"></script>

<link rel="stylesheet" type="text/css" href="/Public/static/webuploader/webuploader.css" media="all">
<script src="/Public/static/md5.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" src="/Public/static/webuploader/webuploader.js"></script>
    <div class="main-title cf">
        <h2>新增 [游戏原包]</h2>
    </div>
    <!-- 标签页导航 -->
<div class="tab-wrap">
    <ul class="tab-nav nav">
        <li data-tab="tab1" class="current"><a href="javascript:void(0);">基础</a></li>
    </ul>
    <div class="tab-content zc_for">
    <!-- 表单 -->
    <form id="form" action="<?php echo U('add');?>" method="post" class="form-horizontal">
        <!-- 基础文档模型 -->
        <div id="tab1" class="tab-pane in tab1 tab-look">
    		<table  border="0" cellspacing="0" cellpadding="0">
                <tbody>
                  <tr>
                    <td class="l">游戏名称：</td>
                    <td class="r">
                        <select id="game_id" name="game_id" style="display: inline-block;">
                            <option value="" selected="">请选择游戏</option>
                        <?php $_result=get_game_list();if(is_array($_result)): $i = 0; $__LIST__ = $_result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["id"]); ?>" ><?php echo ($vo["game_name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                        </select>
                        <input type="hidden" id="game_name" name="game_name" value=""></input> 
                        游戏id:<span id='game_num'></span>                       
                    </td>

                    <td class="l">原包类型：</td>
                    <td class="r">
                        <input type="radio" class="inp_radio" value="1" name="file_type" checked="checked">安卓
                        <input type="radio" class="inp_radio" value="2" name="file_type">苹果
                    </td>
                  </tr>
                  
                  <tr>
                    <td class="l">文件包：</td>
                    <td class="r" colspan='3'>
                        <div id="uploader" class="wu-example">
                            <div class="btns">
                                <div id="picker">选择文件</div>
                            </div> 
                            <!--用来存放文件信息-->
                            <div id="thelist" class="uploader-list">
                                <!-- <div id="WU_FILE_0" class="item">
                                    <h4 class="info">APP接口文档及demo（wap模式）.rar</h4>
                                    <p class="state">等待上传...</p>
                                    <div class="progress progress-striped active">
                                        <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                                    </div>
                                </div> -->
                            </div>
                        </div>
                        <input type="hidden" id="data" name="data"   value="000"/>
                        <input type="hidden" id="file_id" name="file_id"     value="<?php echo ($data['file_id']); ?>"/>
                        <input type="hidden" id="file_name" name="file_name" value="<?php echo ($data['file_name']); ?>"/>
                        <input type="hidden" id="file_url"  name="file_url"  value="<?php echo ($data['file_url']); ?>"/>
                        <input type="hidden" id="file_size" name="file_size" value="<?php echo ($data['file_size']); ?>"/>
                    </td>
                  </tr>
                  
       
                  <tr>
                    <td class="l">备注：</td>
                    <td class="r" colspan='3'>
                        <textarea name="remark" class="txt_area"></textarea>
                    </td>
                  </tr>
                </tbody>
            </table>
        </div>
        <div class="form-item cf">
            <button class="btn submit-btn  ajax-post hidden " id="submit" type="submit" target-form="form-horizontal">确 定</button>
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
//导航高亮
highlight_subnav('<?php echo U('GameSource/lists');?>');
$('#submit').click(function(){
    $('#form').submit();
});
//下拉框
$(function(){
   $('select').comboSelect();

});

$(function(){
    $("#game_name").val($("#game_id option:selected").text());
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

/*获取游戏名称*/
$("#game_id").change(function() {
    $("#game_name").val($("#game_id option:selected").text());
    $("#game_num").html($("#game_id option:selected").val());
});

var userInfo = {userId:"kazaff", md5:""};   //用户会话信息
var chunkSize = 5000 * 1024;        //分块大小
var uniqueFileName = null;          //文件唯一标识符
var md5Mark = null;
var backEndUrl = "<?php echo U('File/shard_upload',array('session_id'=>session_id()));?>";
WebUploader.Uploader.register({
    "before-send-file" : "beforeSendFile", 
    "before-send"      : "beforeSend", 
    "after-send-file"  : "afterSendFile"
}, {
    beforeSendFile: function(file){
        //秒传验证
        var task = new $.Deferred();
        var start = new Date().getTime();
        (new WebUploader.Uploader()).md5File(file, 0, 10*1024*1024).progress(function(percentage){
            //console.log(percentage);
        }).then(function(val){
            //console.log("总耗时: "+((new Date().getTime()) - start)/1000);
            md5Mark = val;
            userInfo.md5 = val;
            $.ajax({
                type: "POST"
                , url: backEndUrl
                , data: {status: "md5Check", md5: val}
                , cache: false
                , timeout: 1000 //todo 超时的话，只能认为该文件不曾上传过
                , dataType: "json"
            }).then(function(data, textStatus, jqXHR){
                alert(data.chunk);
                //console.log(data);
                if(data.ifExist){   //若存在，这返回失败给WebUploader，表明该文件不需要上传
                    task.reject();
                    uploader.skipFile(file);
                    file.path = data.path;
                }else{
                    task.resolve();
                    //拿到上传文件的唯一名称，用于断点续传
                    uniqueFileName = md5(''+userInfo.userId+file.name+file.type+file.lastModifiedDate+file.size);
                }
            }, function(jqXHR, textStatus, errorThrown){    //任何形式的验证失败，都触发重新上传
                task.resolve();
                //拿到上传文件的唯一名称，用于断点续传
                uniqueFileName = md5(''+userInfo.userId+file.name+file.type+file.lastModifiedDate+file.size);
            });
        });
        return $.when(task);
    }
    , beforeSend: function(block){
        //分片验证是否已传过，用于断点续传
        var task = new $.Deferred();
        $.ajax({
            type: "POST"
            , url: backEndUrl
            , data: {
                status: "chunkCheck"
                , name: uniqueFileName
                , chunkIndex: block.chunk
                , size: block.end - block.start
            }
            , cache: false
            , timeout: 1000 //todo 超时的话，只能认为该分片未上传过
            , dataType: "json"
        }).then(function(data, textStatus, jqXHR){
            if(data.ifExist){   //若存在，返回失败给WebUploader，表明该分块不需要上传
                task.reject();
            }else{
                task.resolve();
            }
        }, function(jqXHR, textStatus, errorThrown){    //任何形式的验证失败，都触发重新上传
            task.resolve();
        });

        return $.when(task);
    }
    , afterSendFile: function(file){
        var chunksTotal = 0;
        if((chunksTotal = Math.ceil(file.size/chunkSize)) > 1){
            //合并请求
            var task = new $.Deferred();
            $.ajax({
                type: "POST"
                , url: backEndUrl
                , data: {
                    status: "chunksMerge"
                    , name: uniqueFileName
                    , chunks: chunksTotal
                    , ext: file.ext
                    , md5: md5Mark
                }
                , cache: false
                , dataType: "json"
            }).then(function(data, textStatus, jqXHR){
                //todo 检查响应是否正常
                task.resolve();
                file.path = data.path;
                $("#file_name").val(data.name);
                $("#file_url").val(data.path);
                $("#file_size").val(file.size);
            }, function(jqXHR, textStatus, errorThrown){
                task.reject();
            });
            return $.when(task);
        }else{
            //UploadComlate(file);
        }
    }
});
var uploader = WebUploader.create({
    // 选完文件后，是否自动上传。
    auto: true,
    // swf文件路径
    swf: '/Public/static/webuploader/Uploader.swf',
    // 文件接收服务端。
    server: "<?php echo U('File/shard_upload',array('session_id'=>session_id()));?>",
    // 选择文件的按钮。可选。
    // 内部根据当前运行是创建，可能是input元素，也可能是flash.
    pick: {id:'#picker'},
    //dnd: "#theList",
    paste: document.body,
    // 不压缩image, 默认如果是jpeg，文件上传前会压缩一把再上传！
    resize: false,
    disableGlobalDnd: true,
    fileNumLimit:1,
    threads:3,
    compress: false,
    prepareNextFile: true,
    formData: function(){return $.extend(true, {}, userInfo);},
    duplicate:true,
    chunked:true,
    chunkSize: 5*1000*1024,
    duplicate: true
});
// 当有文件被添加进队列的时候
uploader.on( 'fileQueued', function( file ) {
    $("#thelist").append( '<div id="' + file.id + '" class="item">' +
        '<h4 class="info">' + file.name + '</h4>' +
        '<p class="state">等待上传...</p>' +
    '</div>' );
});

// 文件上传过程中创建进度条实时显示。
uploader.on( 'uploadProgress', function( file, percentage ) {
    var $li = $( '#'+file.id ),
        $percent = $li.find('.progress .progress-bar');
    // 避免重复创建
    if ( !$percent.length ) {
        $percent = $('<div class="progress progress-striped active">' +
          '<div class="progress-bar" role="progressbar" style="width: 0%">' +
          '</div>' +
        '</div>').appendTo( $li ).find('.progress-bar');
    }
    $li.find('p.state').text('上传中');
    $percent.css( 'width', percentage * 100 + '%' );
    $percent.text( (percentage * 100).toFixed(0) + '%' );
});

uploader.on( 'uploadSuccess', function( file , response) {
    $( '#'+file.id ).find('p.state').text('已上传');
    //alert(JSON.stringify(response));

    if(!response.chunk){
        $("#file_name").val(response.name);
        $("#file_url").val(response.path);
        $("#file_size").val(response.size);
    }
    //alert($("#file_name").val()+";"+$("#file_url").val()+";"+$("#file_size").val())
});

uploader.on( 'uploadError', function( file ) {
    $( '#'+file.id ).find('p.state').text('上传出错');
});

uploader.on( 'uploadComplete', function(file) {
    $( '#'+file.id ).find('.progress').fadeOut();
});
</script>

</body>
</html>