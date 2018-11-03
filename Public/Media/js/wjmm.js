/**
 * Created by Administrator on 2017/5/19.
 */
// 第一步
// 找回密码之第一步 用户名
$("#in").blur(function(){
    if($.trim($("#in").val()).length == 0){
        $('#tips').html('<i class="star">* </i><span class="red">账号不能为空</span>');
        $('#net').addClass('next').removeClass('color');
        return false;
    }
    if (!(/^[a-zA-Z0-9]{6,15}$/.test($("#in").val())) ){
        $('#tips').html('<i class="star"></i><span class="red">账号格式不正确</span>');
        $('#net').addClass('next').removeClass('color');
        return false;
    }
    if (/^[a-zA-Z0-9]{6,15}$/.test($("#in").val())){
        $('#tips').html('<i class="star"></i><span class="red"></span>');
        $('#net').addClass('color').removeClass('next');
    }
});

$('#net').on('click',function() {
    var account = $.trim($('#in').val());
    if (account == '') {
        $('#tips').html('<i class="star">* </i><span class="red">账号不能为空</span>');
        $('#net').addClass('next').removeClass('color');
        return false;
    }
    $.ajax({
        type:'post',
        dataType:'json',
        async:false,
        data:{account:account},
        url:usernameverify,
        beforeSend:function(){
            $('#net').attr("disabled",true);
        },
        success:function(data) {
            if (parseInt(data.status) == 1 ) {
                window.location.href=MODULE+'Index/wjmm/account/'+data.msg+'.html';
            }else{
                $('#tips').html('<i class="star">* </i><span class="red">'+data.msg+'</span>');
                $('#net').addClass('next').removeClass('color');
                $('#net').attr("disabled",false);
                return false;
            }
        },
        error:function() {
            $('#net').attr("disabled",false);
            alert('服务器故障，请稍候再试。。。。');
        }
    });
});


// 如果是有手机号 没有邮箱
if(window.myphone!=1 && window.myemail==1 ){
    // 手机显示
    $("#in2").val(ph).attr("readonly","true");
    $("#forget2").show();
    // 邮箱隐藏
    $("#hb_email").hide();
    // 点击手机页面的邮箱找回时
    $("#forget2").find(".emailfindway").click(function(){
        // 弹框出来
        var bind=document.getElementById('nobind');
        bind.style.display='block';
        var shut=document.getElementById('shut');
        shut.onclick=function () {
            bind.style.display='none';
        };
        // 点击手机找回
        var phone_show=document.getElementById('emailfind');
        phone_show.onclick=function () {
            bind.style.display='none';
        };
    })
}

// 如果是有邮箱  没有手机号
if(window.myemail!=1 && window.myphone==1){
    // 手机隐藏 邮箱显示
    $("#forget2").hide();
    $("#hb_email").show();
    $("#email").val(em).attr("readonly","true");
    // 点击邮箱页面的手机找回时
    $("#forgetemail1").find(".iphonefindway").click(function(){
        // 弹框出来
        var bindemil=document.getElementById('nobindemils');
        bindemil.style.display='block';
        var shuted=document.getElementById('shuted');
        shuted.onclick=function () {
            bindemil.style.display='none';
        };
        // 点击手机找回的时候还显示当前的页面
        var email_show=document.getElementById('email_click');
        email_show.onclick=function () {
            bindemil.style.display='none';
        };
    })
}

// 如果是即有邮箱  有手机号
if(window.myemail!=1 && window.myphone!=1){
    $("#forget2").show();
    $("#hb_email").hide();
    $("#in2").val(ph).attr("readonly","true");
    $("#email").val(em).attr("readonly","true");
    // 当在手机找回页面点击邮箱找回的时候
    $("#forget2").find(".emailfindway").click(function(){
        $("#forget2").hide();
        $("#hb_email").show();
    })
    // 当在邮箱找回页面点击手机找回的时候
    $("#forgetemail1").find(".iphonefindway").click(function(){
        $("#forget2").show();
        $("#hb_email").hide();
    })
}

// 发送验证码
$('#fsyzm').click(function () {
    $.ajax({
        type: 'post',
        dataType: 'json',
        async:false,
        url:sendvcodeurl,
        data: {phone:window.myphone,name:account},
        beforeSend: function () {
            $('#fsyzm').addClass('send').removeClass('ck').val('正在发送...');
            $("#fsyzm").attr("disabled",true);
        },
        success: function (data) {
            if (parseInt(data.status) == 1 ) {
                $('#fsyzm').addClass('send').removeClass('ck');
                $("#fsyzm").attr("disabled",true);
                var time = 60;
                $(this).val(time + '秒后再获取').addClass('send').removeClass('ck');
                var timer = setInterval(function (){
                    time--;
                    $('#fsyzm').attr("disabled",true);
                    $('#fsyzm').val(time + '秒后再获取');
                    if (time < 0) {
                        clearInterval(timer);
                        $('#fsyzm').attr("disabled",false);
                        $('#fsyzm').addClass('ck').removeClass('send').val('获取验证码');
                    }
                }, 1000);
            }else{
                $('#fsyzm').addClass('send').removeClass('ck').val(data.msg);
                $('#net2').removeClass('correct').addClass('next');
                $('#net2').attr("disabled",false);
                return false;
            }

        },
        error: function (){
            $("#fsyzm").attr("disabled",false);
            alert('服务器故障，请稍候再试。。。。');
        }
    });
});


// 手机找回之获取到验证码
$('#code').blur(function () {
    if ($.trim($('#code').val()) == '') {
        $('#tiped').html('<i class="star">* </i><span class="red">验证码不能为空</span>');
        $('#net2').removeClass('correct').addClass('next');
        return false;}
    if (!(/^\d{6}$/.test($("#code").val()))){
        $('#tiped').html('<i class="star">* </i><span class="red">验证码格式错误</span>');
        $('#net2').removeClass('correct').addClass('next');
        return false;
    }
    if (/^\d{6}$/.test($("#code").val())) {
        $('#tiped').html('<i class="star"></i><span class="red"></span>');
    }
    if($.trim($('#code').val()) !== ''&&(/^\d{6}$/.test($("#code").val()))){
        $('#net2').removeClass('next').addClass('correct');
    }

});

// 手机找回之点击下一步
$('#net2').on('click',function() {
    if ($.trim($('#code').val()) == '') {
        $('#tiped').html('<i class="star">* </i><span class="red">验证码不能为空</span>');
        $('#net2').removeClass('correct').addClass('next');
        return false;
    }
    if (!(/^\d{6}$/.test($("#code").val())) ){
        $('#tiped').html('<i class="star">* </i><span class="red">验证码格式错误</span>');
        $('#net2').removeClass('correct').addClass('next');
        return false;
    };
    $.ajax({
        type:'post',
        dataType:'json',
        data:{
            account:account,
            phone:window.myphone,
            vcode:$.trim($('#code').val())
        },
        url: verifyvcodeurl,
        beforeSend:function(){
            $('#net2').attr("disabled",true);
        },
        success:function(data) {
            if (parseInt(data.status) == 1 ){
                window.location.href=MODULE+'Index/wjmm/account/'+data.msg+'.html';
            } else {
                $('#tiped').html('<i class="star">* </i><span class="red">'+data.msg+'</span>');
                $('#net2').removeClass('correct').addClass('next');
                $('#net2').attr("disabled",false);
                return false;
            }
        },
        error:function(XMLHttpRequest, textStatus, errorThrown) {
            $('#net2').attr("disabled",false);
            alert('服务器故障，请稍候再试。。。。');
        }
    });
});


// 邮箱找回之发送邮箱链接
$('#net4').on('click',function() {
    var email=window.myemail;
    $.ajax({
        type:'post',
        dataType:'json',
        data:{
            email:email,
            account: account
        },
        url:tosendemailurl,
        beforeSend: function () {
            $('#net4').attr('disabled', true);
        },
        success:function(data) {
            if (parseInt(data.status) == 1 ){
                $('#forgetemail1').hide();
                $('#forgetemail2').show();
                $('#net4').attr('disabled', false);
            } else {
                $('#net4').attr('disabled', false);
                $('#tips4').html('<i class="star">* </i><span class="red">'+data.msg+'</span>');               
                return false;
            }
        },
        error:function(XMLHttpRequest, textStatus, errorThrown) {
            $('#net4').attr('disabled', false);
            alert('服务器故障，请稍候再试。。。。');
        }
    });
});

// 邮箱找回之跳转邮箱
var hash={
    'qq.com': 'http://mail.qq.com',
    'gmail.com': 'http://mail.google.com',
    'sina.com': 'http://mail.sina.com.cn',
    '163.com': 'http://mail.163.com',
    '126.com': 'http://mail.126.com',
    'yeah.net': 'http://www.yeah.net/',
    'sohu.com': 'http://mail.sohu.com/',
    'tom.com': 'http://mail.tom.com/',
    'sogou.com': 'http://mail.sogou.com/',
    '139.com': 'http://mail.10086.cn/',
    'hotmail.com': 'http://www.hotmail.com',
    'live.com': 'http://login.live.com/',
    'live.cn': 'http://login.live.cn/',
    'live.com.cn': 'http://login.live.com.cn',
    '189.com': 'http://webmail16.189.cn/webmail/',
    'yahoo.com.cn': 'http://mail.cn.yahoo.com/',
    'yahoo.cn': 'http://mail.cn.yahoo.com/',
    'eyou.com': 'http://www.eyou.com/',
    '21cn.com': 'http://mail.21cn.com/',
    '188.com': 'http://www.188.com/',
    'foxmail.com': 'http://www.foxmail.com'
};

$(function() {
    $("#get_email").each(function () {
        var url = $(this).text().split('@')[1];
        for (var j in hash) {
            $(this).attr("href", hash[url]);
        }
    });
    $('#atonce').click(function () {
        var url = $("#get_email").text().split('@')[1];
        for (var j in hash) {
            $(this).attr("href", hash[url]);
        }
    })
});

// 再次发送邮箱
$("#resend").click(function(){
    var email=window.myemail;
    $.ajax({
        type:'post',
        dataType:'json',
        data:{
            email:email,
            account: account,
        },
        url:tosendemailurl,
        beforeSend: function () {
            $('#resend').attr('disabled', true);
        },
        success:function(data) {
            if (parseInt(data.status) == 1 ){
                $('#resend').attr('disabled', false);
                // 再次发送邮箱成功的提示
                $("#resendsuc").css("display","block");
                $("#sureBtn").click(function(){
                    $("#resendsuc").css("display","none");
                })
            } else {
                $('#resend').attr('disabled', false);
                return false;
            }
        },
        error:function(XMLHttpRequest, textStatus, errorThrown) {
            $('#resend').attr('disabled', false);
            alert('服务器故障，请稍候再试。。。。');
        }
    });
});

// 第三步之重置密码
$("#reset").blur(function(){
    if ($.trim($('#reset').val()) == '') {
        $('#password').html('<i class="star">* </i><span class="red">密码不能为空</span>');
        $('#net3').removeClass('correct').addClass('next');
        return false;
    }
    if (!(/^(?![0-9]+$)[0-9A-Za-z]{6,15}$/.test($("#reset").val()))){
        $('#password').html('<i class="star">* </i><span class="red">请输入 6-15 位数字、字母</span>');
        $('#net3').removeClass('correct').addClass('next');
        return false;
    }
    if(/^(?![0-9]+$)[0-9A-Za-z]{6,15}$/.test($("#reset").val())){
        $('#password').html('<img src="'+zqimg+'" >');
    }
    if($.trim($('#reset').val()) !== ''&& $.trim($("#confirm").val()).length != 0 &&$.trim($('#reset').val())==$.trim($("#confirm").val())&& /^(?![0-9]+$)[0-9A-Za-z]{6,15}$/.test($("#reset").val())){
        $('#net3').removeClass('next').addClass('correct');
    }
});

$("#confirm").blur(function(){
    if ($.trim($('#confirm').val()) == '') {
        $('#password2').html('<i class="star">* </i><span class="red">密码不能为空</span>');
        $('#net3').removeClass('correct').addClass('next');
        return false;
    }
    if( $.trim($('#reset').val())!== $.trim($('#confirm').val()) ){
        $('#password2').html('<i class="star">* </i><span class="red">两次密码不一致</span>');
        $('#net3').removeClass('correct').addClass('next');
    }
    if($.trim($('#reset').val())==$.trim($('#confirm').val())){
        $('#password2').html('<img src="'+zqimg+'" >');
    }
    if($.trim($('#reset').val()) !== ''&& $.trim($("#confirm").val()).length != 0 &&$.trim($('#reset').val())==$.trim($("#confirm").val())&& /^(?![0-9]+$)[0-9A-Za-z]{6,15}$/.test($("#reset").val())){
        $('#net3').removeClass('next').addClass('correct');
    }
});

if($.trim($('#reset').val()) !== ''&& $.trim($("#confirm").val()).length != 0 &&$.trim($('#reset').val())==$.trim($("#confirm").val())&& /^(?![0-9]+$)[0-9A-Za-z]{6,15}$/.test($("#reset").val())){
    $('#net3').removeClass('next').addClass('correct');
}


$('#net3').click(function () {
    if ($.trim($('#reset').val()) == '') {
        $('#password').html('<i class="star">* </i><span class="red">密码不能为空</span>');
        $('#net3').removeClass('correct').addClass('next');
        return false;
    }
    if (!(/^(?![0-9]+$)[0-9A-Za-z]{6,15}$/.test($("#reset").val()))){
        $('#password').html('<i class="star">* </i><span class="red">密码格式不正确</span>');
        $('#net3').removeClass('correct').addClass('next');
        return false;
    }
    if ($.trim($('#confirm').val()) == '') {
        $('#password2').html('<i class="star">* </i><span class="red">密码不能为空</span>');
        $('#net3').removeClass('correct').addClass('next');
        return false;
    }
    if( $.trim($('#reset').val())!== $.trim($('#confirm').val()) ){
        $('#password2').html('<i class="star">* </i><span class="red">两次密码不一致</span>');
        $('#net3').removeClass('correct').addClass('next');
        return false;
    }
    $.ajax({
        type:'post',
        dataType:'json',
        data:{
            account:account,
            pwd: $.trim($('#reset').val()),
            repwd: $.trim($('#confirm').val())
        },
        url: reseturl,
        beforeSend: function () {
            $('#net3').attr('disabled', true);
        },
        success:function(data) {
            if (parseInt(data.status) == 1 ){
                window.location.href=MODULE+'Index/wjmm/account/'+data.msg+'.html';
            } else {
                $('#net3').attr('disabled', false);
                return false;
            }
        },
        error:function(XMLHttpRequest, textStatus, errorThrown) {
            $('#net3').attr('disabled', false);
            alert('服务器故障，请稍候再试。。。。');
        }
    });
});