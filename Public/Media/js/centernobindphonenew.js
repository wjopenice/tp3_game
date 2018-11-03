/**
 * Created by 12 on 2017/4/13.
 */
$(function () {
    // 第一步
    $('#instead').click(function () {
        $('#nobind1').css('display','none');
        $('#nobind2').css('display','block');       
    });


// 第二步

    $('#delivery').click(function () {
         if ($.trim($("#telnumber").val()).length == 0) {
            $('#numtips1').html('<i class="star">* </i><span class="red">手机号不能为空</span>');
            $('#achieve').removeClass('completesuccess').addClass('completeerror');
            return false;
        }

        if (!(/^1[3|4|5|7|8][0-9]{9}$/.test($("#telnumber").val()))) {
            $('#numtips1').html('<i class="star">* </i><span class="red">手机号码格式不正确</span>');
            $('#achieve').removeClass('completesuccess').addClass('completeerror');
            $('#ident').addClass('send').removeClass('ck');
            return false;
        }
        var phoneNum=$.trim($('#telnumber').val());
        var username=$.trim($("#uname").val());
        $.ajax({
            type: 'post',
            url:sendvcodeurl,
            data:{
                    phone:phoneNum,
                    name:username
                 },
            dataType: 'json',
            beforeSend: function () {
                $('#delivery').attr("disabled",true);
                $('#delivery').addClass('send').removeClass('ck').val('正在发送...');
            },

            success: function (data) {
                if (parseInt(data.status) == 1 ) {
                $('#delivery').addClass('send').removeClass('ck');
                $('#delivery').attr("disabled",true);
                var time = 60;
                $(this).val(time + '秒后再获取').addClass('send').removeClass('ck');
                var timer = setInterval(function () {
                    time--;
                    $('#delivery').val(time + '秒后再获取');
                    if (time < 0) {
                        $('#delivery').attr("disabled",false);
                        clearInterval(timer);
                        $('#delivery').addClass('ck').removeClass('send').val('获取验证码');
                    }
                }, 1000);

                } else {
                    $('#delivery').attr("disabled",false);
                    $('#delivery').addClass('send').removeClass('ck');
                    $('#numtips1').html(msg);
                }

            },
            error: function (){
                $('#delivery').attr("disabled",false);
                alert('服务器故障，请稍候再试。。。。');
                
            }
        });
    });

// 验证码
    $('#nobindcode').blur(function () {
        if ($.trim($('#nobindcode').val()) == '') {
            $('#codetips').html('<i class="star">* </i><span class="red">验证码不能为空</span>');
            $('#achieve').removeClass('completesuccess').addClass('completeerror');
            return false;
        }
        if (!(/^\d{6}$/.test($("#nobindcode").val())) ){
            $('#codetips').html('<i class="star">* </i><span class="red">验证码格式错误</span>');
            $('#achieve').removeClass('completesuccess').addClass('completeerror');
            return false;
        }
        if (/^\d{6}$/.test($("#nobindcode").val())) {
            $('#codetips').html('<img src="'+zqimg+'" >');
        }
        if($.trim($('#nobindcode').val()) !== ''&& /^\d{6}$/.test($("#nobindcode").val())){
            $('#achieve').removeClass('completeerror').addClass('completesuccess');
        }

    });
    // 完成按钮点击事件
    $('#achieve').click(function () {
        if ($.trim($('#nobindcode').val()) == '') {
            $('#codetips').html('<i class="star">* </i><span class="red">验证码不能为空</span>');
            $('#achieve').removeClass('completesuccess').addClass('completeerror');
            return false;}
        if ($('#achieve').hasClass('completeerror')){
            return false;
        }
        var phone=$.trim($('#telnumber').val());
        var vcode=$.trim($('#nobindcode').val());
        $.ajax({
            type:'post',
            dataType:'json',
            data:{   
                in:'nobindphone',
                phone:phone,
                vcode:vcode
            },
            url:changephurl,
            beforeSend:function(){
                $("#achieve").attr("disabled",true);
            },
            success:function(data) {
                if (parseInt(data.status) == 1 ) {                 
                     window.location.href=MODULE+'Member/nobindphone/account/'+data.msg+'.html';
                } else {
                    $("#achieve").attr("disabled",false);
                    $('#achieve').removeClass('completesuccess').addClass('completeerror');
                    $('#codetips').html('<i class="star">* </i><span class="red">'+data['msg']+'</span>');
                    return false;
                }
            },
            error:function() {
                $("#achieve").attr("disabled",false);
                alert('服务器故障，请稍候再试。。。。');
            }
        });


    });

// 解绑手机第三部
    var phoneFlag=false;
    $("#telnumber2").blur(function(){
        
         if ($.trim($("#telnumber2").val()).length == 0) {
            $('#numtips').html('<i class="star">* </i><span class="red">手机号不能为空</span>');
            $('#ident').addClass('send').removeClass('ck');
            $('#achieve2').removeClass('completesuccess').addClass('completeerror');
            return false;
        }
        if (!(/^1[3|4|5|7|8][0-9]{9}$/.test($("#telnumber2").val()))) {
            $('#numtips').html('<i class="star">* </i><span class="red">手机号码格式不正确</span>');
            $('#ident').addClass('send').removeClass('ck');
            $('#achieve2').removeClass('completesuccess').addClass('completeerror');
            return false;
        }
       if (/^1[3|4|5|7|8][0-9]{9}$/.test($("#telnumber2").val())) {
            $('#numtips').html('');
            var phoneNum=$.trim($('#telnumber2').val());
            $.ajax({
                type: 'post',
                url: phonebangcheck,
                async:false,
                data: {
                       phone:phoneNum,
                       in:'nobindphone'
                     },
                dataType: 'json',
                success: function (data) {
                    if (parseInt(data.status) == 1 ) {
                        $('#numtips').html('<img src="'+zqimg+'" >');
                        $('#ident').addClass('ck').removeClass('send');
                         phoneFlag=true;
                    }else{
                        $('#numtips').html('<i class="star">* </i><span class="red">'+data.msg+'</span>');
                        $('#achieve2').addClass('completeerror').removeClass('completesuccess');
                         phoneFlag=false;
                        return false;
                    }
                },
                error: function () {
                    alert('服务器故障，请稍候再试。。。。');
                }
            });
        }
        if($.trim($("#telnumber2").val()).length !== 0 &&$.trim($('#code').val()) !== ''&& $("#numtips:has(img)").length !==0 &&$("#codetips2:has(img)").length !==0 && /^\d{6}$/.test($("#code").val())){
            $('#achieve2').removeClass('completeerror').addClass('completesuccess');
        }
    });

    $('#ident').click(function () {
        if ($.trim($("#telnumber2").val()).length == 0) {
            $('#numtips').html('<i class="star">* </i><span class="red">手机号不能为空</span>');
            $('#achieve2').removeClass('completesuccess').addClass('completeerror');
            $('#ident').addClass('send').removeClass('ck');
            return false;
        }
        if (!(/^1[3|4|5|7|8][0-9]{9}$/.test($("#telnumber2").val()))) {

            $('#numtips').html('<i class="star">* </i><span class="red">手机号码格式不正确</span>');
            $('#achieve2').removeClass('completesuccess').addClass('completeerror');
            $('#ident').addClass('send').removeClass('ck');
            return false;
        }
        
        var phoneNum=$.trim($('#telnumber2').val());
        var username=$.trim($("#usname").html());
        if(phoneFlag){
             $.ajax({
                type: 'post',
                url: sendvcodeurl,
                data: "phone="+phoneNum+"&name="+username,
                dataType: 'json',
                beforeSend: function () {
                    $('#ident').attr("disabled",true);
                    $('#ident').addClass('send').removeClass('ck').val('正在发送...');
                },
                success: function (data) {
                    if (parseInt(data.status) == 1 ) {
                    $('#ident').attr("disabled",true);
                    $('#ident').addClass('send').removeClass('ck');
                    var time = 60;
                    $(this).val(time + '秒后再获取').addClass('send').removeClass('ck');
                    var timer = setInterval(function () {
                        time--;
                        $('#ident').val(time + '秒后再获取');
                        if (time < 0) {
                            $('#ident').attr("disabled",false);
                            clearInterval(timer);
                            $('#ident').addClass('ck').removeClass('send').val('获取验证码');
                        }
                    }, 1000);
                    } else {
                        $('#ident').attr("disabled",false);
                        $('#ident').addClass('send').removeClass('ck');
                        $('#numtips').html(msg);    
                    }

                },
                error: function (){
                    $('#ident').attr("disabled",false);
                    alert('服务器故障，请稍候再试。。。。');
                }
            });
            }
       
    });

    $('#code').blur(function () {
        if ($.trim($("#telnumber2").val()).length == 0) {
            $('#numtips').html('<i class="star">* </i><span class="red">手机号不能为空</span>');
            $('#ident').addClass('send').removeClass('ck');
            $('#achieve2').removeClass('completesuccess').addClass('completeerror');
            return false;
        }
        if (!(/^1[3|4|5|7|8][0-9]{9}$/.test($("#telnumber2").val()))) {
            $('#numtips').html('<i class="star">* </i><span class="red">手机号码格式不正确</span>');
            $('#ident').addClass('send').removeClass('ck');
            $('#achieve2').removeClass('completesuccess').addClass('completeerror');
            return false;
        }
        if ($.trim($('#code').val()) == '') {
            $('#codetips2').html('<i class="star">* </i><span class="red">验证码不能为空</span>');
            $('#achieve2').removeClass('completesuccess').addClass('completeerror');
            return false;}
        if (!(/^\d{6}$/.test($("#code").val())) ){
            $('#codetips2').html('<i class="star">* </i><span class="red">验证码格式错误</span>');
            $('#achieve2').removeClass('completesuccess').addClass('completeerror');
            return false;
        }
        if (/^\d{6}$/.test($("#code").val())) {
            $('#codetips2').html('<img src="'+zqimg+'" >');
        }
        if($.trim($("#telnumber2").val()).length !== 0 &&$.trim($('#code').val()) !== ''&& $("#numtips:has(img)").length !==0 &&$("#codetips2:has(img)").length !==0 && /^\d{6}$/.test($("#code").val())){
            $('#achieve2').removeClass('completeerror').addClass('completesuccess');
        }
    });
    
    $('#achieve2').on('click',function() {
        if ($.trim($("#telnumber2").val()).length == 0) {
            $('#numtips').html('<i class="star">* </i><span class="red">手机号不能为空</span>');
            $('#ident').addClass('send').removeClass('ck');
            $('#achieve2').removeClass('completesuccess').addClass('completeerror');
            return false;
        }

        if (!(/^1[3|4|5|7|8][0-9]{9}$/.test($("#telnumber2").val()))) {
            $('#numtips').html('<i class="star">* </i><span class="red">手机号码格式不正确</span>');
            $('#ident').addClass('send').removeClass('ck');
            $('#achieve2').removeClass('completesuccess').addClass('completeerror');
            return false;
        }
        if ($.trim($('#code').val()) == '') {
            $('#codetips').html('<i class="star">* </i><span class="red">验证码不能为空</span>');
            $('#achieve2').removeClass('completesuccess').addClass('completeerror');
            return false;}
       
        if($.trim($("#telnumber2").val()).length !== 0 &&$.trim($('#code').val()) !== ''&& $("#numtips:has(img)").length !==0 &&$("#codetips2:has(img)").length !==0 && /^\d{6}$/.test($("#code").val())){
            $('#achieve2').removeClass('completeerror').addClass('completesuccess');
        }

        var phone=$.trim($('#telnumber2').val());
        var scode=$.trim($('#code').val());
        $.ajax({
            type:'post',
            dataType:'json',
            data:{
                 phone:phone,
                 vcode:scode,
                 in:'nobindphone'
                },
            url:phoneurl,
            beforeSend:function(){
              $('#achieve2').attr("disabled",true);
            },
            success:function(data) {
                if (parseInt(data.status) == 1 ){                   
                    window.location.href=MODULE+'Member/nobindphone/account/'+data.msg+'.html';
                }
                 else {
                    $('#achieve2').attr("disabled",false);
                    $('#achieve2').removeClass('completesuccess').addClass('completeerror');
                    $('#codetips2').html('<i class="star">* </i><span class="red">'+data.msg+'</span>');
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
