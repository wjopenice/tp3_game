
$(function () {
    $("#telpassword").blur(function(){
        if ($.trim($('#telpassword').val()) == '') {
            $('#teltips').html('<i class="star">* </i><span class="red">密码不能为空</span>');
            $('#achieve').removeClass('completesuccess').addClass('completeerror');
            return false;
        }
        if (!(/^[a-zA-Z0-9_]{6,15}$/.test($("#telpassword").val())) ){
            $('#teltips').html('<i class="star">* </i><span class="red">密码格式错误</span>');
            $('#achieve').removeClass('completesuccess').addClass('completeerror');
            return false;
        }
        if((/^[a-zA-Z0-9_]{6,15}$/.test($("#telpassword").val()))){
            $('#teltips').html('<img src="'+zqimg+'" >');
            $('#achieve').removeClass('completeerror').addClass('completesuccess');
            window.psd=$.trim($('#telpassword').val());
            
        }
    });



$('#achieve').on('click',function() {
        if ($.trim($('#telpassword').val()) == '') {
            $('#teltips').html('<i class="star">* </i><span class="red">密码不能为空</span>');
            $('#achieve').removeClass('completesuccess').addClass('completeerror');
            return false;
        }
        if (!(/^[a-zA-Z0-9_]{6,15}$/.test($("#telpassword").val())) ){
            $('#teltips').html('<i class="star">* </i><span class="red">密码格式错误</span>');
            $('#achieve').removeClass('completesuccess').addClass('completeerror');
            return false;
        }
       
        var username=$.trim($("#uname").html());
        $.ajax({
            type:'post',
            dataType:'json',
            data:{
                   iss:'mgw',
                   in:'bindphone',
                   name:username,
                   password:psd
                    },
            url:verifypwdurl,
            beforeSend:function(){
                $('#achieve').attr("disabled",true);
            },
            success:function(data) {
                if (parseInt(data.status) == 1 ) {
                    // 成功状态下跳转
                   window.location.href=MODULE+'Member/bindphone/account/'+data.msg+'.html';
                } else {
                    $('#teltips').html('<i class="star">* </i><span class="red">'+data['msg']+'</span>');
                    $('#achieve').removeClass('completesuccess').addClass('completeerror');
                    $('#achieve').attr("disabled",false);
                    return false;
                }
            },
            error:function() {
                $('#achieve').attr("disabled",false);
                alert('服务器故障，请稍候再试。。。。');               
            }
        });
    });


// 手机验证码第二步
    var ipFlag=false;
    $("#telnumber").blur(function(){
        if ($.trim($("#telnumber").val()).length == 0) {
            $('#numtips').html('<i class="star">* </i><span class="red">手机号不能为空</span>');
            $('#ident').addClass('send').removeClass('ck');
            $('#achieve2').removeClass('completesuccess').addClass('completeerror');
            return false;
        }
        if (!(/^1[3|4|5|7|8][0-9]{9}$/.test($("#telnumber").val()))) {
            $('#numtips').html('<i class="star">* </i><span class="red">手机号码格式不正确</span>');
            $('#achieve2').removeClass('completesuccess').addClass('completeerror');
            $('#ident').addClass('send').removeClass('ck');
            return false;
        }
        if (/^1[3|4|5|7|8][0-9]{9}$/.test($("#telnumber").val())) {
            $('#numtips').html('');
            var phoneNum=$.trim($('#telnumber').val());
            $.ajax({
                type: 'post',
                url: phonebangcheck,
                async:false,
                data:{
                    phone:phoneNum,
                    in:'bindphone',
                },
                dataType: 'json',
                success: function (data) {
                    if (parseInt(data.status) == 1 ) {
                        //        ajax请求成功后的逻辑处理  ,显示对号
                        $('#numtips').html('<img src="'+zqimg+'" >');
                        $('.send').addClass('ck').removeClass('send')
                        ipFLag=true;
                    }else{
                        $('#numtips').html('<i class="star">* </i><span class="red">'+data.msg+'</span>');
                        $('#achieve2').addClass('completeerror').removeClass('completesuccess');
                        ipFLag=false;
                        return false;
                    }
                },
                error: function () {
                    alert('服务器故障，请稍候再试。。。。');
                }
            });
        }
         if($.trim($("#telnumber").val()).length !== 0 &&$.trim($('#code').val()) !== ''&& /^1[3|4|5|7|8][0-9]{9}$/.test($("#telnumber").val()) && /^\d{6}$/.test($("#code").val())){
             if(ipFlag=true){
                 $('#achieve2').removeClass('completeerror').addClass('completesuccess');
             }else{
                 $('#achieve2').removeClass('completesuccess').addClass('completeerror');
             }
        }
    });

    $('#ident').click(function () {
         if ($.trim($("#telnumber").val()).length == 0) {
            $('#numtips').html('<i class="star">* </i><span class="red">手机号不能为空</span>');
            $('#achieve2').removeClass('completesuccess').addClass('completeerror');
            $('#ident').addClass('send').removeClass('ck');
            return false;
        }

        if (!(/^1[3|4|5|7|8][0-9]{9}$/.test($("#telnumber").val()))) {
            $('#numtips').html('<i class="star">* </i><span class="red">手机号码格式不正确</span>');
            $('#achieve2').removeClass('completesuccess').addClass('completeerror');
            $('#ident').addClass('send').removeClass('ck');
            return false;
        }
        if ($('#ident').hasClass('send')){
            return false;
        }
        var phoneNum=$.trim($('#telnumber').val());
        $.ajax({
            type: 'post',
            url: sendvcodeurl,
            async:true,
            data:{
                    phone:phoneNum,
                    name:account
                 },
            dataType: 'json',
            beforeSend: function () {
                $('#ident').attr("disabled",true);
                $('#ident').addClass('send').removeClass('ck').val('正在发送...');
            },
            success: function (data) {
                if (parseInt(data.status) == 1 ) {
                    $('#ident').addClass('send').removeClass('ck');
                     $('#ident').attr("disabled",true);
                    var time = 60;
                    $('#ident').val(time + '秒后再获取').addClass('send').removeClass('ck');
                    var timer = setInterval(function () {
                        time--;
                        $('#ident').val(time + '秒后再获取');
                        if (time < 0) {
                            clearInterval(timer);
                            $('#ident').attr("disabled",false);
                            $('#ident').addClass('ck').removeClass('send').val('获取验证码');
                        }
                    }, 1000);

                } else {
                    $('#ident').attr("disabled",false);
                    $('#ident').addClass('send').removeClass('ck');
                    $('#numtips').html(data.msg);
                    $('#achieve2').removeClass('completesuccess').addClass('completeerror');
                    return false;
                }

            },
            error: function (){
                $('#ident').attr("disabled",false);
                alert('服务器故障，请稍候再试。。。。');
            }
        });
    });

    $('#code').blur(function () {
        if ($.trim($("#telnumber").val()).length == 0) {
            $('#numtips').html('<i class="star">* </i><span class="red">手机号不能为空</span>');
            $('#ident').addClass('send').removeClass('ck');
            $('#achieve2').removeClass('completesuccess').addClass('completeerror');
            return false;
        }
        if (!(/^1[3|4|5|7|8][0-9]{9}$/.test($("#telnumber").val()))) {

            $('#numtips').html('<i class="star">* </i><span class="red">手机号码格式不正确</span>');
            $('#achieve2').removeClass('completesuccess').addClass('completeerror');
            $('#ident').addClass('send').removeClass('ck');
            return false;
        }

        if ($.trim($('#code').val()) == '') {
            $('#codetips').html('<i class="star">* </i><span class="red">验证码不能为空</span>');
            $('#achieve2').removeClass('completesuccess').addClass('completeerror');
            return false;}
        if (!(/^\d{6}$/.test($("#code").val())) ){
            $('#codetips').html('<i class="star">* </i><span class="red">验证码格式错误</span>');
            $('#achieve2').removeClass('completesuccess').addClass('completeerror');
            return false;
        }
        if (/^\d{6}$/.test($("#code").val())) {
            $('#codetips').html('<img src="'+zqimg+'" >');
        }
        if($.trim($("#telnumber").val()).length !== 0 &&$.trim($('#code').val()) !== ''&& /^1[3|4|5|7|8][0-9]{9}$/.test($("#telnumber").val()) && /^\d{6}$/.test($("#code").val())){
            if(ipFlag=true){
                $('#achieve2').removeClass('completeerror').addClass('completesuccess');
            }else{
                $('#achieve2').removeClass('completesuccess').addClass('completeerror');
            }
        }
    });

    $('#achieve2').on('click',function() {
        if ($.trim($("#telnumber").val()).length == 0) {
            $('#numtips').html('<i class="star">* </i><span class="red">手机号不能为空</span>');
            $('#achieve2').removeClass('completesuccess').addClass('completeerror');
            return false;
        }
        if (!(/^1[3|4|5|7|8][0-9]{9}$/.test($("#telnumber").val()))) {

            $('#numtips').html('<i class="star">* </i><span class="red">手机号码格式不正确</span>');
            $('#achieve2').removeClass('completesuccess').addClass('completeerror');
            $('#ident').addClass('send').removeClass('ck');
            return false;
        }
        if ($.trim($('#code').val()) == '') {
            $('#codetips').html('<i class="star">* </i><span class="red">验证码不能为空</span>');
            $('#achieve2').removeClass('completesuccess').addClass('completeerror');
            return false;
        }
       
        if($.trim($("#telnumber").val()).length !== 0 &&$.trim($('#code').val()) !== ''&& /^1[3|4|5|7|8][0-9]{9}$/.test($("#telnumber").val()) && /^\d{6}$/.test($("#code").val())){
            if(ipFlag=true){
                $('#achieve2').removeClass('completeerror').addClass('completesuccess');
            }else{
                $('#achieve2').removeClass('completesuccess').addClass('completeerror');
            }
        }
        var phone=$.trim($('#telnumber').val());
        var scode=$.trim($('#code').val());
        $.ajax({
            type:'post',
            dataType:'json',
            data:{
                  in:'bindphone',
                  vcode:scode,
                  phone:phone
            },
            url:phoneurl,
            beforeSend:function(){
                $('#achieve2').attr("disabled",true);
            },
            success:function(data) {
                if (parseInt(data.status) == 1 ) {                   
                    window.location.href=MODULE+'Member/bindphone/account/'+data.msg+'.html';                 

                } else {
                    $('#achieve2').removeClass('completesuccess').addClass('completeerror');
                    $('#achieve2').attr("disabled",false);
                    $('#codetips').html('<i class="star">* </i><span class="red">'+data.msg+'</span>');
                    return false;
                }
            },
            error:function() {
                $('#achieve2').attr("disabled",false);
                alert('服务器故障，请稍候再试。。。。');
            }
        });
    });
});