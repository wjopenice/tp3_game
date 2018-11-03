//未登录弹窗
    function nologin_box(){
    var $pop=$("<div class='pop'>");
    var str='';
    str='<div class="win-box">' +
        '<div class="mess-t">' +
        '<p>领取礼包请先登录</p>' +
        '</div>' +
        '<div class="mess-b">' +
        '<a href="javascript:;" class="sure">登&nbsp;录</a>' +
        '<a href="javascript:;" class="return">返&nbsp;回</a>' +
        '</div></div>';
    $pop.html(str);
    $pop.appendTo("body");
    $pop.show();
    $(".return").click(function(){
        $(".pop").hide();
    });
    $(".sure").click(function(){
        window.location.href=MODULE+'/Public/login.html'
    })
}
//礼包领取成功弹出框
function login_ok(data){
    var $cart=$("<div class='cart-win'>");
    var str='';
    str= '<div class="win-box">' +
        '<div class="success">' +
        '<p>领取成功</p>' +
        '</div><div class="ma">' +
        '<p class="lbcode">礼包码：<span class="giftcodesed" id="giftcode"></span></p>' +
        '</div><button class="shut">关 &nbsp;闭</button>' +
        '</div>';
    $cart.html(str);
    $cart.appendTo("body");
    $cart.show();
    $('.giftcodesed').html('<span class="giftcodesed" id="giftcode">'+data.data+'</span>');
    $(".shut").click(function(){
        $(".cart-win").hide();
    });
}

//   您已领取过该礼包
function  login_no(data) {
    var $receive=$("<div class='receiveComplete'>");
    var str='';
    str=  '<div class="win-box">' +
        '<div class="receivesuccess">' +
        '<p>您已领取过该礼包</p></div>' +
        '<div class="ma">' +
        '<p>礼包码：<span class="giftcodesed" id="giftcode2">'+data.data+'</span></p>' +
        '</div>' +
        '<button class="shutno">关 &nbsp;闭</button>' +
        '</div>'
    $receive.html(str);
    $receive.appendTo("body");
    $receive.show();
    // $('.giftcodesed').html('<span class="giftcodesed" id="giftcode2"></span>');
    $(".shutno").click(function(){
        $(".receiveComplete").hide();
    });
}
//该礼包已领取完，下次请早
function  login_noc(data) {
    var $noreceive=$("<div class='noreceive'>");
    var str='';
    str= '<div class="win-box">' +
        '<div class="noreceivetitle">' +
        '<p>该礼包已领取完</p>' +
        '</div>' +
        '<button class="shutnoc">关 &nbsp;闭</button>' +
        '</div>';
    $noreceive.html(str);
    $noreceive.appendTo("body");
    $noreceive.show();
    $(".shutnoc").click(function(){
        $(".noreceive").hide();
    });
}

$.ajax({
    type:'post',
    dataType:'json',
    url:MODULE+'/Member/is_login',
    data:'',
    success:function(data) {
        var num=parseInt(data.status);        
        if (num == 1 ) {
            $('#entry').text('您好，'+data.account+'【个人中心】' ).append("<a id='nums'>"+letter_number+"</a>").attr('href',MODULE+'/Member/personalcenter');
            $('#enroll').html(' <a href="javascript:;" id="logout">| &nbsp;退出登录</a>');
            $("#nums").css({"color":"white","background-color":"#333","font-family":"Arial","font-size":"10px","text-align":"center","border-radius":"50%","display":"inline-block","line-height":"16px","width":"16px","height":"16px","margin-left":"-5px","margin-top":"-30px","margin-right":"20px"});
            $("#entry").css({"position":"relative","color":"#db7622"});
            $("#nums").css({"position":"absolute","top":"24px","right":"-22px","background-color":"#db7622"});
            $("#entry").mouseover(function(){
                $(this).css("color","#ed6557");
                $("#nums").css({"color":"white","background-color":"#ed6557","font-family":"Arial","font-size":"10px","text-align":"center","border-radius":"50%","display":"inline-block","line-height":"16px","width":"16px","height":"16px","margin-left":"-5px","margin-top":"-30px","margin-right":"20px"})
            });
            $("#entry").mouseout(function(){
                $(this).css("color","#db7622");
                $("#nums").css({"color":"white","background-color":"#db7622","font-family":"Arial","font-size":"10px","text-align":"center","border-radius":"50%","display":"inline-block","line-height":"16px","width":"16px","height":"16px","margin-left":"-5px","margin-top":"-30px","margin-right":"20px"})
            })
            $('#logout').click(function () {
                $.ajax({
                    type: 'POST',
                    async: true,
                    dataType: 'json',
                    url:MODULE+'/Member/logout',  
                    data:'',                 
                    success: function(data) {
                        if (data.reurl) {                            
                            window.location.href = data.reurl;
                        } else {                            
                            window.location.reload();
                        }
                    },
                    cache: false
                });
            });
        }
        else if (num == 0 ) {
            //登录失败的时候
            $('#entry').html('<a href="'+MODULE+'/Public/login'+'" id="entry">登录 &nbsp;|&nbsp;</a>')
            $('#enroll').html(' <a href="'+MODULE+'/Public/register'+'" id="enroll">注册</a>')
        }
    },
    error:function() {
        // alert('服务器故障，请稍候再试。。。。');
    }
});

// 激活码未登录的弹框

    function code_login_box(){
    var $pop=$("<div class='pop'>");
    var str='';
    str='<div class="win-box">' +
        '<div class="mess-t">' +
        '<p>未登录请先登录</p>' +
        '</div>' +
        '<div class="mess-b">' +
        '<a href="javascript:;" class="sure">登&nbsp;录</a>' +
        '<a href="javascript:;" class="return">返&nbsp;回</a>' +
        '</div></div>';
    $pop.html(str);
    $pop.appendTo("body");
    $pop.show();
    $(".return").click(function(){
        $(".pop").hide();
    });
    $(".sure").click(function(){
        window.location.href=MODULE+'/Public/login.html'
    })
}

// 激活码兑换成功的弹框

    function code_sucess_box(){
    var $pop=$("<div class='pop'>");
    var str='';
    str='<div class="win-box">' +
        '<div class="mess-t">' +
        '<p>激活码兑换成功</p>' +
        '</div>' +
        '<div class="mess-b" style="margin:20px 0 0 226px">' +
        '<a href="javascript:;" class="sure">确&nbsp;定</a>' +
        '</div></div>';
    $pop.html(str);
    $pop.appendTo("body");
    $pop.show();
    $(".sure").click(function(){
        location.reload();
    })
}
