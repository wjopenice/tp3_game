$(function () {
    // 第一步
    $('#changed').click(function () {
        $('#nobind1').css('display','none');
        $('#nobind1_1').css('display','block');
    });
    //  第一步之---输入内容
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
        if ($('#achieve').hasClass('completeerror')){
            return false;
        }
        var username=$.trim($("#uname").html());
        var psd=$.trim($('#telpassword').val());
        $.ajax({
            type:'post',
            dataType:'json',
            data:{
                    in:"nobindemail",
                    iss:"mgw",
                    name:username,
                    password:psd

                },
            url:verifypwdurl,
            beforeSend:function(){
                $('#achieve').attr("disabled",true);
            },
            success:function(data) {
                if (parseInt(data.status) == 1 ) {
                    // 成功状态下对应的显示
                    window.location.href=MODULE+'Member/nobindemail/account/'+data.msg+'.html';
                } else {
                    $('#teltips').html('<i class="star">* </i><span class="red">'+data.msg+'</span>');
                    $('#achieve').removeClass('completesuccess').addClass('completeerror');
                    $('#achieve').attr("disabled",false);
                    return false;
                }
            },
            error:function() {
                alert('服务器故障，请稍候再试。。。。');
                $('#teltips').html('<i class="star">* </i><span class="red">错误</span>');
                $('#achieve').removeClass('completesuccess').addClass('completeerror');
                $('#achieve').attr("disabled",false);
                return false;
            }
        });
    });


  // 第二部  
    $("#emailcenter").blur(function(){
        if ($.trim($('#emailcenter').val()) == '') {
            $('#centeremailtips').html('<i class="star">* </i><span class="red">邮箱不能为空</span>');
            $('#achieve2').removeClass('completesuccess').addClass('completeerror');
            return false;
        }
        if (!(/\w[-\w.+]*@([A-Za-z0-9][-A-Za-z0-9]+\.)+[A-Za-z]{2,14}/.test($("#emailcenter").val())) ){
            $('#centeremailtips').html('<i class="star">* </i><span class="red">邮箱格式错误</span>');
            $('#achieve2').removeClass('completesuccess').addClass('completeerror');
            return false;
        }
        if((/\w[-\w.+]*@([A-Za-z0-9][-A-Za-z0-9]+\.)+[A-Za-z]{2,14}/.test($("#emailcenter").val()))){
            $('#centeremailtips').html('');
             var email=$.trim($('#emailcenter').val());
            $.ajax({
                type:'post',
                dataType:'json',
                data:{
                        email:email,
                        in:"nobindemail",
                     },
                url:emailbangcheck,
                
                success:function(data) {
                    if (parseInt(data.status) == 1 ) {
                        $('#centeremailtips').html('<img src="'+zqimg+'" >');
                        $('#achieve2').removeClass('completeerror').addClass('completesuccess');
                    } else {
                        $('#centeremailtips').html('<i class="star">* </i><span class="red">'+data.msg+'</span>');
                        $('#achieve2').removeClass('completesuccess').addClass('completeerror');
                        return false;
                    }
                },
                error:function() {
                    alert('服务器故障，请稍候再试。。。。');
                }
            });
        }
    });
    $('#achieve2').on('click',function() {

        if ($.trim($('#emailcenter').val()) == '') {
            $('#centeremailtips').html('<i class="star">* </i><span class="red">邮箱不能为空</span>');
            $('#achieve2').removeClass('completesuccess').addClass('completeerror');
            return false;
        }
        if (!(/\w[-\w.+]*@([A-Za-z0-9][-A-Za-z0-9]+\.)+[A-Za-z]{2,14}/.test($("#emailcenter").val())) ){
            $('#centeremailtips').html('<i class="star">* </i><span class="red">邮箱格式错误</span>');
            $('#achieve2').removeClass('completesuccess').addClass('completeerror');
            return false;
        }
        // if ($('#achieve2').hasClass('completeerror')){
        //     return false;
        // }
        window.email=$.trim($('#emailcenter').val());
        $.ajax({
            type:'post',
            dataType:'json',
            data:{
                    email:email,
                    name:account,
                    in:"nobindemail"
                 },
            url:tosendemailurl,
            beforeSend:function(){
                $('#achieve2').attr("disabled",true);
            },
            success:function(data) {
                if (parseInt(data.status) == 1 ) {
                    // 发送邮箱链接
                    // ajax请求成功后的逻辑处理                   
                    $('#nobind2').css('display','none');
                    $('#get_email').html($('#emailcenter').val());
                    $('#nobind3').css('display','block');
                    $('#achieve2').attr("disabled",false);
                } else {
                    $('#centeremailtips').html('<i class="star">* </i><span class="red">'+data.msg+'</span>');
                    $('#achieve2').removeClass('completesuccess').addClass('completeerror');
                    $('#achieve2').attr("disabled",false);
                    return false;
                }
            },
            error:function() {
                $('#achieve2').attr("disabled",false);
                alert('服务器故障，请稍候再试。。。。');
            }
        });
    });
    
    // 邮箱第三部
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
    $(function(){
        $("#get_email").each(function() {
            var url = $(this).text().split('@')[1];
            for (var j in hash){
                $(this).attr("href", hash[url]);
            }
        });
        $('#achieve3').click(function () {
            var url = $("#get_email").text().split('@')[1];     
           
            for (var j in hash){
                $(this).attr("href", hash[url]);
            }
        })
    });

 // 再次发送邮箱
$("#resend").click(function(){
    window.email=$.trim($('#emailcenter').val());
    $.ajax({        
        type:'post',
        dataType:'json',
        data:{
                email:email,
                name:account,
                in:"nobindemail",
             },
        url:tosendemailurl,
        beforeSend:function(){
            $('#resend').attr("disabled",true);
        },
        success:function(data) {

            if (parseInt(data.status) == 1 ){
                
               $('#resend').attr('disabled', false);
               $("#resendsuc").css("display","block");
                $("#sureBtn").click(function(){
                    $("#resendsuc").css("display","none");
                })

            } else {  
            // 再次发送邮箱失败            
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



});
