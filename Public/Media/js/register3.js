var  remFlag=false;
var accFlag=false;
var yxFlag=false;
// 邮箱为空的时候是false
var emailFLag=false;
$("#reaccount").blur(function(){
    if ($.trim($('#reaccount').val()) == '') {
        $('#reacctips').html('<i class="star">* </i><span class="red">用户名不能为空</span>');
        $('#finished').addClass('registerfin').removeClass('finish');
        return false;
    }
    if (!(/^[a-zA-Z]+[0-9a-zA-Z_]{5,14}$/.test($("#reaccount").val())) ){
        $('#reacctips').html('<i class="star">* </i><span class="red">用户账号格式错误</span>');
        $('#finished').addClass('registerfin').removeClass('finish');
    }
    if (/^[a-zA-Z]+[0-9a-zA-Z_]{5,14}$/.test($("#reaccount").val())){
        $('#reacctips').html('<i class="star"></i><span class="red"></span>');
        $.ajax({
            type:'post',
            dataType:'json',
            async:false,
            data:{username:$.trim($('#reaccount').val())},
            url:MODULE+'Member/checkUser',
            success:function(data) {
                if (parseInt(data.status) == 1 ) {
                    $('#reacctips').html('<img src="'+IMG+'/wjmm/quanbuzhengquan.png" >');
                    accFlag=true;
                } else {
                    $('#reacctips').html('<i class="star">* </i><span class="red">该帐号已被注册</span>');
                    $('#finished').addClass('registerfin').removeClass('finish');
                    accFlag=false;
                    return false;
                }
            },
            error:function() {
                alert('服务器故障，请稍候再试。。。。');
            }
        });
    }
    if(remFlag){
        $('#finished').addClass('registerfin').removeClass(' finish');
    }else{
            if((/^[a-zA-Z]+[0-9a-zA-Z_]{5,14}$/.test($("#reaccount").val()))&&/^(?![0-9]+$)[0-9A-Za-z]{6,15}$/.test($("#repassword").val())&&$.trim($('#resurepassword').val())==$.trim($('#repassword').val())  && (/^[\u4e00-\u9fa5]{2,4}$/.test($("#rename").val())) && IdentityCodeValid($("#reid").val()) ){
               if(emailFLag){
                   if(/\w[-\w.+]*@([A-Za-z0-9][-A-Za-z0-9]+\.)+[A-Za-z]{2,14}/.test($("#reemail").val())){
                       if(accFlag==true && yxFlag==true){
                           $('#finished').removeClass('registerfin').addClass('finish');
                       }else{
                           $('#finished').addClass('registerfin').removeClass('finish');
                       }
                   }
               }else{
                   $('#finished').removeClass('registerfin').addClass('finish');
               }
            }else{
                $('#finished').addClass('registerfin').removeClass('finish');
            }
        }

});
// 用户密码
$("#repassword").blur(function(){
    if ($.trim($('#repassword').val()) == '') {
        $('#repasstips').html('<i class="star">* </i><span class="red">密码不能为空</span>');
        $('#finished').addClass('registerfin').removeClass('finish');
        return false;
    }
    if (!(/^(?![0-9]+$)[0-9A-Za-z]{6,15}$/.test($("#repassword").val())) ){
        $('#repasstips').html('<i class="star">* </i><span class="red">密码格式错误</span>');
        $('#finished').addClass('registerfin').removeClass('finish');
        return false;
    }
    if(/^(?![0-9]+$)[0-9A-Za-z]{6,15}$/.test($("#repassword").val())){
        $('#repasstips').html('<img src="'+IMG+'/wjmm/quanbuzhengquan.png" >');
    }
    if(remFlag){
        $('#finished').addClass('registerfin').removeClass(' finish');
    }else{
        if((/^[a-zA-Z]+[0-9a-zA-Z_]{5,14}$/.test($("#reaccount").val()))&&/^(?![0-9]+$)[0-9A-Za-z]{6,15}$/.test($("#repassword").val())&&$.trim($('#resurepassword').val())==$.trim($('#repassword').val())  && (/^[\u4e00-\u9fa5]{2,4}$/.test($("#rename").val())) && IdentityCodeValid($("#reid").val()) ){
            if(emailFLag){
                if(/\w[-\w.+]*@([A-Za-z0-9][-A-Za-z0-9]+\.)+[A-Za-z]{2,14}/.test($("#reemail").val())){
                    if(accFlag==true && yxFlag==true){
                        $('#finished').removeClass('registerfin').addClass('finish');
                    }else{
                        $('#finished').addClass('registerfin').removeClass('finish');
                    }
                }
            }else{
                $('#finished').removeClass('registerfin').addClass('finish');
            }
        }else{
            $('#finished').addClass('registerfin').removeClass('finish');
        }
    }

});

// 确认密码
$("#resurepassword").blur(function(){
    if ($.trim($('#resurepassword').val()) == '') {
        $('#resuretips').html('<i class="star">* </i><span class="red">密码不能为空</span>');
        $('#finished').addClass('registerfin').removeClass('finish');
        return false;
    }
    if ( $.trim($('#resurepassword').val())!== $.trim($('#repassword').val()) ){
        $('#resuretips').html('<i class="star">* </i><span class="red">两次密码不一致</span>');
        $('#finished').addClass('registerfin').removeClass('finish');
        return false;
    }
    if($.trim($('#resurepassword').val())==$.trim($('#repassword').val()) ){
        $('#resuretips').html('<img src="'+IMG+'/wjmm/quanbuzhengquan.png" >');
    }
    if(remFlag){
        $('#finished').addClass('registerfin').removeClass(' finish');
    }else{
        if((/^[a-zA-Z]+[0-9a-zA-Z_]{5,14}$/.test($("#reaccount").val()))&&/^(?![0-9]+$)[0-9A-Za-z]{6,15}$/.test($("#repassword").val())&&$.trim($('#resurepassword').val())==$.trim($('#repassword').val())  && (/^[\u4e00-\u9fa5]{2,4}$/.test($("#rename").val())) && IdentityCodeValid($("#reid").val()) ){
            if(emailFLag){
                if(/\w[-\w.+]*@([A-Za-z0-9][-A-Za-z0-9]+\.)+[A-Za-z]{2,14}/.test($("#reemail").val())){
                    if(accFlag==true && yxFlag==true){
                        $('#finished').removeClass('registerfin').addClass('finish');
                    }else{
                        $('#finished').addClass('registerfin').removeClass('finish');
                    }
                }
            }else{
                $('#finished').removeClass('registerfin').addClass('finish');
            }
        }else{
            $('#finished').addClass('registerfin').removeClass('finish');
        }
    }
});

// 图片验证码
$("#identify").blur(function(){
    if ($.trim($('#identify').val()) == '') {
        $('#reidentifytips').html('<i class="star">* </i><span class="red">验证码不能为空</span>');
        $('#finished').addClass('registerfin').removeClass('finish');
        return false;
    }
    if (!(/^[a-zA-Z0-9]{4}$/.test($("#identify").val())) ){
        $('#reidentifytips').html('<i class="star">* </i><span class="red">验证码格式错误</span>');
        $('#finished').addClass('registerfin').removeClass('finish');
        return false;
    }
    if (/^[a-zA-Z0-9]{4}$/.test($("#identify").val()) ){
        $('#reidentifytips').html('<img src="'+IMG+'/wjmm/quanbuzhengquan.png" >');
    }

    if(remFlag){
        $('#finished').addClass('registerfin').removeClass(' finish');
    }else{
        if((/^[a-zA-Z]+[0-9a-zA-Z_]{5,14}$/.test($("#reaccount").val()))&&/^(?![0-9]+$)[0-9A-Za-z]{6,15}$/.test($("#repassword").val())&&$.trim($('#resurepassword').val())==$.trim($('#repassword').val())  && (/^[\u4e00-\u9fa5]{2,4}$/.test($("#rename").val())) && IdentityCodeValid($("#reid").val()) ){
            if(emailFLag){
                if(/\w[-\w.+]*@([A-Za-z0-9][-A-Za-z0-9]+\.)+[A-Za-z]{2,14}/.test($("#reemail").val())){
                    if(accFlag==true && yxFlag==true){
                        $('#finished').removeClass('registerfin').addClass('finish');
                    }else{
                        $('#finished').addClass('registerfin').removeClass('finish');
                    }
                }
            }else{
                $('#finished').removeClass('registerfin').addClass('finish');
            }
        }else{
            $('#finished').addClass('registerfin').removeClass('finish');
        }
    }
});
// 点击图片验证码进行切换
$('.checkcode').click(function() {
    var e = (new Date).getTime();
    $(this).attr('src', MODULE+'/Public/verify/t/'+e);
});
// 邮箱
$("#reemail").blur(function(){
    if ($.trim($('#reemail').val()) !== '') {
        emailFlag=true;
        if (!(/\w[-\w.+]*@([A-Za-z0-9][-A-Za-z0-9]+\.)+[A-Za-z]{2,14}/.test($("#reemail").val()))){
            $('#reemailtips').html('<i class="star">* </i><span class="red">邮箱格式不正确</span>');
            $('#finished').addClass('registerfin').removeClass('finish');
            return false;
        }
        if(/\w[-\w.+]*@([A-Za-z0-9][-A-Za-z0-9]+\.)+[A-Za-z]{2,14}/.test($("#reemail").val())){
            $.ajax({
                type:'post',
                dataType:'json',
                async:false,
                data:{email:$.trim($('#reemail').val())},
                url:emailbangcheck,
                success:function(data) {
                    if (parseInt(data.status) == 1 ) {
                    //1:表示用户名注册成功
                        $('#reemailtips').html('<img src="'+IMG+'/wjmm/quanbuzhengquan.png" >');
                        yxFlag=true;
                    } else {
                    //否则:表示用户名注册被注册过
                        $('#reemailtips').html('<i class="star">* </i><span class="red">该邮箱已被其它用户绑定，请更换其它邮箱</span>');
                        $('#finished').addClass('registerfin').removeClass('finish');
                        yxFlag=false;
                        return false;
                    }
                },
                error:function() {
                    alert('服务器故障，请稍候再试。。。。');
                }
            });
        }
    }else{
        emailFlag=false;
        $('#reemailtips').html('<i class="star"></i>&nbsp;&nbsp;<span class="zi">请输入常用邮箱 ，该邮箱可用于找回账号密码</span>');
    }
    if(remFlag){
        $('#finished').addClass('registerfin').removeClass(' finish');
    }else{
        if((/^[a-zA-Z]+[0-9a-zA-Z_]{5,14}$/.test($("#reaccount").val()))&&/^(?![0-9]+$)[0-9A-Za-z]{6,15}$/.test($("#repassword").val())&&$.trim($('#resurepassword').val())==$.trim($('#repassword').val())  && (/^[\u4e00-\u9fa5]{2,4}$/.test($("#rename").val())) && IdentityCodeValid($("#reid").val()) ){
            if(emailFLag){
                if(/\w[-\w.+]*@([A-Za-z0-9][-A-Za-z0-9]+\.)+[A-Za-z]{2,14}/.test($("#reemail").val())){
                    if(accFlag==true && yxFlag==true){
                        $('#finished').removeClass('registerfin').addClass('finish');
                    }else{
                        $('#finished').addClass('registerfin').removeClass('finish');
                    }
                }
            }else{
                $('#finished').removeClass('registerfin').addClass('finish');
            }
        }else{
            $('#finished').addClass('registerfin').removeClass('finish');
        }
    }
});
// 名字
$("#rename").blur(function(){
    if ($.trim($('#rename').val()) == '') {
        $('#rerelname').html('<i class="star">* </i><span class="red">姓名不能为空</span>');
        $('#finished').addClass('registerfin').removeClass('finish');
        return false;
    }
    if (!(/^[\u4e00-\u9fa5]{2,4}$/.test($("#rename").val())) ){
        $('#rerelname').html('<i class="star">* </i><span class="red">姓名格式错误</span>');
        $('#finished').addClass('registerfin').removeClass('finish');
        return false;
    }
    if(/^[\u4E00-\u9FA5]{2,4}$/.test($("#rename").val())){
        $('#rerelname').html('<img src="'+IMG+'/wjmm/quanbuzhengquan.png" >');
    }

    if(remFlag){
        $('#finished').addClass('registerfin').removeClass(' finish');
    }else{
        if((/^[a-zA-Z]+[0-9a-zA-Z_]{5,14}$/.test($("#reaccount").val()))&&/^(?![0-9]+$)[0-9A-Za-z]{6,15}$/.test($("#repassword").val())&&$.trim($('#resurepassword').val())==$.trim($('#repassword').val())  && (/^[\u4e00-\u9fa5]{2,4}$/.test($("#rename").val())) && IdentityCodeValid($("#reid").val()) ){
            if(emailFLag){
                if(/\w[-\w.+]*@([A-Za-z0-9][-A-Za-z0-9]+\.)+[A-Za-z]{2,14}/.test($("#reemail").val())){
                    if(accFlag==true && yxFlag==true){
                        $('#finished').removeClass('registerfin').addClass('finish');
                    }else{
                        $('#finished').addClass('registerfin').removeClass('finish');
                    }
                }
            }else{
                $('#finished').removeClass('registerfin').addClass('finish');
            }
        }else{
            $('#finished').addClass('registerfin').removeClass('finish');
        }
    }
});
// 身份证
$("#reid").blur(function(){
    if ($.trim($('#reid').val()) == '') {
        $('#reidcard').html('<i class="star">* </i><span class="red">身份证号不能为空</span>');
        $('#finished').addClass('registerfin').removeClass('finish');
        return false;
    }
    if (!IdentityCodeValid($("#reid").val()) ){
        $('#reidcard').html('<i class="star">* </i><span class="red">身份证号不正确</span>');
        $('#finished').addClass('registerfin').removeClass('finish');
        return false;
    }
    if(IdentityCodeValid($("#reid").val())){
        $('#reidcard').html('<img src="'+IMG+'/wjmm/quanbuzhengquan.png" >');
    }
    if(remFlag){
        $('#finished').addClass('registerfin').removeClass(' finish');
    }else{
        if((/^[a-zA-Z]+[0-9a-zA-Z_]{5,14}$/.test($("#reaccount").val()))&&/^(?![0-9]+$)[0-9A-Za-z]{6,15}$/.test($("#repassword").val())&&$.trim($('#resurepassword').val())==$.trim($('#repassword').val())  && (/^[\u4e00-\u9fa5]{2,4}$/.test($("#rename").val())) && IdentityCodeValid($("#reid").val()) ){
            if(emailFLag){
                if(/\w[-\w.+]*@([A-Za-z0-9][-A-Za-z0-9]+\.)+[A-Za-z]{2,14}/.test($("#reemail").val())){
                    if(accFlag==true && yxFlag==true){
                        $('#finished').removeClass('registerfin').addClass('finish');
                    }else{
                        $('#finished').addClass('registerfin').removeClass('finish');
                    }
                }
            }else{
                $('#finished').removeClass('registerfin').addClass('finish');
            }
        }else{
            $('#finished').addClass('registerfin').removeClass('finish');
        }
    }
});
// 图片点击事件
$('#pics').click(function () {
    if(remFlag == false){
        $('#pics').html('<img src="'+IMG+'/wjmm/tongyixieyi_1.png" alt="" class="remember"><span id="agree" >我已阅读并同意</span>');
        remFlag=true;
        $('#finished').addClass('registerfin').removeClass('finish');
    }else{
        $('#pics').html('<img src="'+IMG+'/wjmm/tongyixieyi_2.png" alt="" class="remember"><span id="agree"  class="red">我已阅读并同意</span>');
        remFlag=false;
        if(/^[a-zA-Z0-9]{4}$/.test($("#identify").val())&&(/^[a-zA-Z]+[0-9a-zA-Z_]{6,15}$/.test($("#reaccount").val()))&& /^(?![0-9]+$)[0-9A-Za-z]{6,15}$/.test($("#repassword").val())&&$.trim($('#resurepassword').val())==$.trim($('#repassword').val()) && (/^[\u4e00-\u9fa5]{2,4}$/.test($("#rename").val())) && IdentityCodeValid($("#reid").val()) ){
            if(accFlag==true){
                $('#finished').removeClass('registerfin').addClass('finish');
            }else{
                $('#finished').addClass('registerfin').removeClass('finish');
            }
        }
    }
});

// 判断
if(remFlag){
    $('#finished').addClass('registerfin').removeClass(' finish');
}else{
    if((/^[a-zA-Z]+[0-9a-zA-Z_]{5,14}$/.test($("#reaccount").val()))&&/^(?![0-9]+$)[0-9A-Za-z]{6,15}$/.test($("#repassword").val())&&$.trim($('#resurepassword').val())==$.trim($('#repassword').val())  && (/^[\u4e00-\u9fa5]{2,4}$/.test($("#rename").val())) && IdentityCodeValid($("#reid").val()) ){
        if(emailFLag){
            if(/\w[-\w.+]*@([A-Za-z0-9][-A-Za-z0-9]+\.)+[A-Za-z]{2,14}/.test($("#reemail").val())){
                if(accFlag==true && yxFlag==true){
                    $('#finished').removeClass('registerfin').addClass('finish');
                }else{
                    $('#finished').addClass('registerfin').removeClass('finish');
                }
            }
        }else{
            $('#finished').removeClass('registerfin').addClass('finish');
        }
    }else{
        $('#finished').addClass('registerfin').removeClass('finish');
    }
}
// 表单提交
$('#finished').click(function () {
    if ($.trim($('#reaccount').val()) == '') {
        $('#reacctips').html('<i class="star">* </i><span class="red">用户名不能为空</span>');
        return false;
    }
    if (!(/^[a-zA-Z]+[0-9a-zA-Z_]{5,14}$/.test($("#reaccount").val())) ){
        $('#reacctips').html('<i class="star">* </i><span class="red">用户账号格式错误</span>');
    }
    if ($.trim($('#repassword').val()) == '') {
        $('#repasstips').html('<i class="star">* </i><span class="red">密码不能为空</span>');
        return false;
    }
    if (!(/^(?![0-9]+$)[0-9A-Za-z]{6,15}$/.test($("#repassword").val())) ){
        $('#repasstips').html('<i class="star">* </i><span class="red">密码格式错误</span>');
        $('#finished').addClass('registerfin').removeClass('finish');
        return false;
    }
    if ($.trim($('#resurepassword').val()) == '') {
        $('#resuretips').html('<i class="star">* </i><span class="red">密码不能为空</span>');
        return false;
    }
    if ( $.trim($('#resurepassword').val())!== $.trim($('#repassword').val()) ){
        $('#resuretips').html('<i class="star">* </i><span class="red">两次密码不一致</span>');
        return false;
    }

    if ($.trim($('#rename').val()) == '') {
        $('#rerelname').html('<i class="star">* </i><span class="red">姓名不能为空</span>');
        return false;
    }
    if (!(/^[\u4e00-\u9fa5]{2,4}$/.test($("#rename").val())) ){
        $('#rerelname').html('<i class="star">* </i><span class="red">姓名格式错误</span>');
        return false;
    }
    if ($.trim($('#reid').val()) == '') {
        $('#reidcard').html('<i class="star">* </i><span class="red">身份证号不能为空</span>');
        return false;
    }
    if (!IdentityCodeValid($("#reid").val()) ){
        $('#reidcard').html('<i class="star">* </i><span class="red">身份证号不正确</span>');
        return false;
    }
    if ($('#finished').hasClass('registerfin')){
        return false;
    }

    if(remFlag){
        $('#finished').addClass('registerfin').removeClass(' finish');
    }else{
        if((/^[a-zA-Z]+[0-9a-zA-Z_]{5,14}$/.test($("#reaccount").val()))&&/^(?![0-9]+$)[0-9A-Za-z]{6,15}$/.test($("#repassword").val())&&$.trim($('#resurepassword').val())==$.trim($('#repassword').val())  && (/^[\u4e00-\u9fa5]{2,4}$/.test($("#rename").val())) && IdentityCodeValid($("#reid").val()) ){
            if(emailFLag){
                if(/\w[-\w.+]*@([A-Za-z0-9][-A-Za-z0-9]+\.)+[A-Za-z]{2,14}/.test($("#reemail").val())){
                    if(accFlag==true && yxFlag==true){
                        $('#finished').removeClass('registerfin').addClass('finish');
                    }else{
                        $('#finished').addClass('registerfin').removeClass('finish');
                    }
                }
            }else{
                $('#finished').removeClass('registerfin').addClass('finish');
            }
        }else{
            $('#finished').addClass('registerfin').removeClass('finish');
        }
    }

 

    // 新修改的ajax
    $.ajax({
        type: 'POST',
        async: true,
        dataType: 'json',
        url:MODULE+'/Member/user_register',
        data:{account:$.trim($('#reaccount').val()),password:$.trim($('#repassword').val()),repass:$.trim($('#resurepassword').val()),verify:$.trim($('#identify').val()),email:$.trim($('#reemail').val()),truename:$.trim($('#rename').val()),card:$.trim($('#reid').val())},
        beforeSend: function() {
            $("#finished").val('注册中').attr("disabled",true);
        },
        success: function(data) {
            if (parseInt(data.status) == 1 ) {
                setTimeout(function() {                           
                    $.ajax({
                        type: 'POST',
                        async: true,
                        dataType: 'json',
                        url: MODULE+'/Member/login.html',
                        beforeSend:function(){
                            $("#finished").val('正在登陆').attr("disabled",true);
                        },
                        data: {account:$.trim($('#reaccount').val()),password:$.trim($('#repassword').val())},
                        success: function(data) {
                          if (parseInt(data.status) == 1 ) {
                              window.location.href=MODULE+'/Member/personalcenter.html';
                          }
                        },
                        error: function() { 
                            location.reload();                    
                        },
                        cache: false
                    }); 
                },1000);
            } else {
                alert(data.msg);
                $("#finished").val('完成注册').attr("disabled",false);
                $('#finished').addClass('registerfin').removeClass('finish');
            }                  
        },
        error: function() {
            alert('服务器故障，稍后再试');
             $("#finished").val('完成注册').attr("disabled",false);
        }
    }); 

});
// 用户名注册部分结束




// 手机注册开始
var remFlag2=false;
var accFlag2=false;
var yxFlag2=false;
var emailFlag2=false;
$("#reaccount2").blur(function(){
    if ($.trim($('#reaccount2').val()) == '') {
        $('#iptel').html('<i class="star">* </i><span class="red">手机号不能为空</span>');
        $('#finished2').addClass('registerfin').removeClass('finish');
        return false;
    }
    if (!(/^1[3|4|5|7|8][0-9]{9}$/.test($("#reaccount2").val()))){
        $('#iptel').html('<i class="star">* </i><span class="red">手机号格式错误</span>');
        $('#finished2').addClass('registerfin').removeClass('finish');
        return false;
    }

    if(/^1[3|4|5|7|8][0-9]{9}$/.test($("#reaccount2").val())){
        $('#iptel').html('<i class="star"></i><span ></span>');
        $.ajax({
            type: 'post',
            url: MODULE+'Member/checkPhone',
            async:false,
            data: {username:$.trim($('#reaccount2').val())},
            dataType: 'json',
            success: function (data) {
                if (parseInt(data.status) == 1 ) {
                    //        ajax请求成功后的逻辑处理  ,显示对号
                    $('#iptel').html('<img src="'+IMG+'/wjmm/quanbuzhengquan.png" >');
                    $('#ident').addClass('ck').removeClass('send')
                    accFlag2=true;
                }else{
                    $('#iptel').html('<i class="star">* </i><span class="red">该手机已绑定</span>');
                    $('#ident').addClass('send').removeClass('ck');
                    $('#finished').addClass('registerfin').removeClass('finish');
                    accFlag2=false;
                    return false;
                }
            },
            error: function () {
                alert('服务器故障，请稍候再试。。。。');

            }
        });
    }
    if(remFlag2){
        $('#finished2').addClass('registerfin').removeClass('finish');
    }else{
        if( /^1[3|4|5|7|8][0-9]{9}$/.test($("#reaccount2").val())&& /^\d{6}$/.test($("#repassword2").val())&&/^[a-zA-Z0-9_]{5,14}$/.test($("#resurepassword2").val()) && /^[\u4E00-\u9FA5]{2,4}$/.test($("#rename2").val()) &&IdentityCodeValid($("#reid2").val()) ){
           if(emailFlag2){
               if(/\w[-\w.+]*@([A-Za-z0-9][-A-Za-z0-9]+\.)+[A-Za-z]{2,14}/.test($("#reemail2").val())){
                   if(accFlag2==true && yxFlag2== true){
                       $('#finished2').removeClass('registerfin').addClass('finish');
                   }else{
                       $('#finished2').addClass('registerfin').removeClass('finish');
                   }
               }
           }else{
               $('#finished2').removeClass('registerfin').addClass('finish');
           }
        }else{
            $('#finished2').addClass('registerfin').removeClass('finish');
        }
    }
});



$('#ident').click(function (){
    if ($.trim($('#reaccount2').val()) == '') {
        $('#iptel').html('<i class="star">* </i><span class="red">手机号不能为空</span>');
        $('#ident').addClass('send').removeClass('ck');
        $('#finished2').addClass('registerfin').removeClass('finish');
        return false;
    }
    if (!(/^1[3|4|5|7|8][0-9]{9}$/.test($("#reaccount2").val()))){
        $('#iptel').html('<i class="star">* </i><span class="red">手机号格式错误</span>');
        $('#ident').addClass('send').removeClass('ck');
        $('#finished2').addClass('registerfin').removeClass('finish');
        return false;
    }
    if ($('#ident').hasClass('send')) return false;
    $("#reaccount2").attr("disabled",true).css("background","#fff");
    $.ajax({
        type: 'post',
        url: MODULE+'Member/telsvcode',
        data: {phone:$.trim($('#reaccount2').val())},
        dataType: 'json',
        beforeSend: function () {
            $('#ident').addClass('send').removeClass('ck').html('正在发送...');
        },
        success: function (data) {
            var time = 60;
            $('#ident').html(time + '秒后再获取').addClass('send').removeClass('ck');
            var timer = setInterval(function () {
                time--;
                $('#ident').html(time + '秒后再获取');
                if (time < 0) {
                    clearInterval(timer);
                    $('#ident').addClass('ck').removeClass('send').html('获取验证码');
                    $("#reaccount2").attr("disabled",false);
                }
            }, 1000);
        },
        error: function () {
            alert('服务器故障，请稍候再试。。。。');
            $("#reaccount2").attr("readonly",false);
        }
    });

    if(remFlag2){
        $('#finished2').addClass('registerfin').removeClass('finish');
    }else{
        if( /^1[3|4|5|7|8][0-9]{9}$/.test($("#reaccount2").val())&& /^\d{6}$/.test($("#repassword2").val())&&/^[a-zA-Z0-9_]{5,14}$/.test($("#resurepassword2").val()) && /^[\u4E00-\u9FA5]{2,4}$/.test($("#rename2").val()) &&IdentityCodeValid($("#reid2").val()) ){
            if(emailFlag2){
                if(/\w[-\w.+]*@([A-Za-z0-9][-A-Za-z0-9]+\.)+[A-Za-z]{2,14}/.test($("#reemail2").val())){
                    if(accFlag2==true && yxFlag2== true){
                        $('#finished2').removeClass('registerfin').addClass('finish');
                    }else{
                        $('#finished2').addClass('registerfin').removeClass('finish');
                    }
                }
            }else{
                $('#finished2').removeClass('registerfin').addClass('finish');
            }
        }else{
            $('#finished2').addClass('registerfin').removeClass('finish');
        }
    }
});
// 验证码
$("#repassword2").blur(function(){
    if ($.trim($('#repassword2').val()) == '') {
        $('#ipcode').html('<i class="star">* </i><span class="red">验证码不能为空</span>');
        $('#finished2').addClass('registerfin').removeClass('finish');
        return false;
    }
    if (!(/^\d{6}$/.test($("#repassword2").val())) ){
        $('#ipcode').html('<i class="star">* </i><span class="red">验证码格式错误</span>');
        $('#finished2').addClass('registerfin').removeClass('finish');
        return false;
    }
    if(/^\d{6}$/.test($("#repassword2").val())){
        $('#ipcode').html('<img src="'+IMG+'/wjmm/quanbuzhengquan.png" >');
    }
    if(remFlag2){
        $('#finished2').addClass('registerfin').removeClass('finish');
    }else{
        if( /^1[3|4|5|7|8][0-9]{9}$/.test($("#reaccount2").val())&& /^\d{6}$/.test($("#repassword2").val())&&/^[a-zA-Z0-9_]{5,14}$/.test($("#resurepassword2").val()) && /^[\u4E00-\u9FA5]{2,4}$/.test($("#rename2").val()) &&IdentityCodeValid($("#reid2").val()) ){
            if(emailFlag2){
                if(/\w[-\w.+]*@([A-Za-z0-9][-A-Za-z0-9]+\.)+[A-Za-z]{2,14}/.test($("#reemail2").val())){
                    if(accFlag2==true && yxFlag2== true){
                        $('#finished2').removeClass('registerfin').addClass('finish');
                    }else{
                        $('#finished2').addClass('registerfin').removeClass('finish');
                    }
                }
            }else{
                $('#finished2').removeClass('registerfin').addClass('finish');
            }
        }else{
            $('#finished2').addClass('registerfin').removeClass('finish');
        }
    }
});
// 设置密码
$("#resurepassword2").blur(function(){
    if ($.trim($('#resurepassword2').val()) == '') {
        $('#ippassword').html('<i class="star">* </i><span class="red">密码不能为空</span>');
        $('#finished2').addClass('registerfin').removeClass('finish');
        return false;
    }
    if (!(/^(?![0-9]+$)[0-9A-Za-z]{6,15}$/.test($("#resurepassword2").val())) ){
        $('#ippassword').html('<i class="star">* </i><span class="red">密码格式错误</span>');
        $('#finished2').addClass('registerfin').removeClass('finish');
        return false;
    }
    if(/^(?![0-9]+$)[0-9A-Za-z]{6,15}$/.test($("#resurepassword2").val())){
        $('#ippassword').html('<img src="'+IMG+'/wjmm/quanbuzhengquan.png" >');
    }
    if(remFlag2){
        $('#finished2').addClass('registerfin').removeClass('finish');
    }else{
        if( /^1[3|4|5|7|8][0-9]{9}$/.test($("#reaccount2").val())&& /^\d{6}$/.test($("#repassword2").val())&&/^[a-zA-Z0-9_]{5,14}$/.test($("#resurepassword2").val()) && /^[\u4E00-\u9FA5]{2,4}$/.test($("#rename2").val()) &&IdentityCodeValid($("#reid2").val()) ){
            if(emailFlag2){
                if(/\w[-\w.+]*@([A-Za-z0-9][-A-Za-z0-9]+\.)+[A-Za-z]{2,14}/.test($("#reemail2").val())){
                    if(accFlag2==true && yxFlag2== true){
                        $('#finished2').removeClass('registerfin').addClass('finish');
                    }else{
                        $('#finished2').addClass('registerfin').removeClass('finish');
                    }
                }
            }else{
                $('#finished2').removeClass('registerfin').addClass('finish');
            }
        }else{
            $('#finished2').addClass('registerfin').removeClass('finish');
        }
    }
});

// 邮箱
$("#reemail2").blur(function(){
   if($.trim($('#reemail2').val()) !== ''){
       emailFlag=true;
       if (!(/\w[-\w.+]*@([A-Za-z0-9][-A-Za-z0-9]+\.)+[A-Za-z]{2,14}/.test($("#reemail2").val()))){
           $('#ipsureemail').html('<i class="star">* </i><span class="red">邮箱格式不正确</span>');
           $('#finished2').addClass('registerfin').removeClass('finish');
           return false;
       }
       if(/\w[-\w.+]*@([A-Za-z0-9][-A-Za-z0-9]+\.)+[A-Za-z]{2,14}/.test($("#reemail2").val())){
           $.ajax({
               type:'post',
               dataType:'json',
               async:false,
               data:{email:$.trim($('#reemail2').val())},
               url:emailbangcheck,
               success:function(data) {
                   if (parseInt(data.status) == 1 ) {
                   //1:表示用户名注册成功
                       $('#ipsureemail').html('<img src="'+IMG+'/wjmm/quanbuzhengquan.png" >');
                       yxFlag2=true;
                   } else {
                   //否则:表示用户名注册被注册过
                       $('#ipsureemail').html('<i class="star">* </i><span class="red">该邮箱已被其它用户绑定，请更换其它邮箱</span>');
                       $('#finished2').addClass('registerfin').removeClass('finish');
                       yxFlag2=false;
                       return false;
                   }
               },
               error:function() {
                   alert('服务器故障，请稍候再试。。。。');
               }
           });
       }
   }else{
       emailFlag2=false;
       $('#ipsureemail').html('<i class="star"></i> &nbsp;<span class="zi">请输入常用邮箱，该邮箱可用于找回账号密码</span>');
   }

    if(remFlag2){
        $('#finished2').addClass('registerfin').removeClass('finish');
    }else{
        if( /^1[3|4|5|7|8][0-9]{9}$/.test($("#reaccount2").val())&& /^\d{6}$/.test($("#repassword2").val())&&/^[a-zA-Z0-9_]{5,14}$/.test($("#resurepassword2").val()) && /^[\u4E00-\u9FA5]{2,4}$/.test($("#rename2").val()) &&IdentityCodeValid($("#reid2").val()) ){
            if(emailFlag2){
                if(/\w[-\w.+]*@([A-Za-z0-9][-A-Za-z0-9]+\.)+[A-Za-z]{2,14}/.test($("#reemail2").val())){
                    if(accFlag2==true && yxFlag2== true){
                        $('#finished2').removeClass('registerfin').addClass('finish');
                    }else{
                        $('#finished2').addClass('registerfin').removeClass('finish');
                    }
                }
            }else{
                $('#finished2').removeClass('registerfin').addClass('finish');
            }
        }else{
            $('#finished2').addClass('registerfin').removeClass('finish');
        }
    }
});
// 姓名
$("#rename2").blur(function(){
    if ($.trim($('#rename2').val()) == '') {
        $('#ipname').html('<i class="star">* </i><span class="red">姓名不能为空</span>');
        $('#finished2').addClass('registerfin').removeClass('finish');
        return false;
    }
    if (!(/^[\u4E00-\u9FA5]{2,4}$/.test($("#rename2").val())) ){
        $('#ipname').html('<i class="star">* </i><span class="red">姓名格式错误</span>');
        $('#finished2').addClass('registerfin').removeClass('finish');
        return false;
    }
    if(/^[\u4E00-\u9FA5]{2,4}$/.test($("#rename2").val())){
        $('#ipname').html('<img src="'+IMG+'/wjmm/quanbuzhengquan.png" >');
    }
    if(remFlag2){
        $('#finished2').addClass('registerfin').removeClass('finish');
    }else{
        if( /^1[3|4|5|7|8][0-9]{9}$/.test($("#reaccount2").val())&& /^\d{6}$/.test($("#repassword2").val())&&/^[a-zA-Z0-9_]{5,14}$/.test($("#resurepassword2").val()) && /^[\u4E00-\u9FA5]{2,4}$/.test($("#rename2").val()) &&IdentityCodeValid($("#reid2").val()) ){
            if(emailFlag2){
                if(/\w[-\w.+]*@([A-Za-z0-9][-A-Za-z0-9]+\.)+[A-Za-z]{2,14}/.test($("#reemail2").val())){
                    if(accFlag2==true && yxFlag2== true){
                        $('#finished2').removeClass('registerfin').addClass('finish');
                    }else{
                        $('#finished2').addClass('registerfin').removeClass('finish');
                    }
                }
            }else{
                $('#finished2').removeClass('registerfin').addClass('finish');
            }
        }else{
            $('#finished2').addClass('registerfin').removeClass('finish');
        }
    }
});
// 身份证
$("#reid2").blur(function(){
    if ($.trim($('#reid2').val()) == '') {
        $('#ipcard').html('<i class="star">* </i><span class="red">身份证号不能为空</span>');
        $('#finished2').addClass('registerfin').removeClass('finish');
        return false;
    }
    if (!IdentityCodeValid($("#reid2").val()) ){
        $('#ipcard').html('<i class="star">* </i><span class="red">身份证号不正确</span>');
        $('#finished2').addClass('registerfin').removeClass('finish');
        return false;
    }
    if(IdentityCodeValid($("#reid2").val())){
        $('#ipcard').html('<img src="'+IMG+'/wjmm/quanbuzhengquan.png" >');
    }
    if(remFlag2){
        $('#finished2').addClass('registerfin').removeClass('finish');
    }else{
        if( /^1[3|4|5|7|8][0-9]{9}$/.test($("#reaccount2").val())&& /^\d{6}$/.test($("#repassword2").val())&&/^[a-zA-Z0-9_]{5,14}$/.test($("#resurepassword2").val()) && /^[\u4E00-\u9FA5]{2,4}$/.test($("#rename2").val()) &&IdentityCodeValid($("#reid2").val()) ){
            if(emailFlag2){
                if(/\w[-\w.+]*@([A-Za-z0-9][-A-Za-z0-9]+\.)+[A-Za-z]{2,14}/.test($("#reemail2").val())){
                    if(accFlag2==true && yxFlag2== true){
                        $('#finished2').removeClass('registerfin').addClass('finish');                  
                    }else{
                        $('#finished2').addClass('registerfin').removeClass('finish');
                    }
                }
            }else{
                $('#finished2').removeClass('registerfin').addClass('finish');
            }
        }else{
            $('#finished2').addClass('registerfin').removeClass('finish');
        }
    }
});

// 图片点击
$('#prints').click(function () {
    if(remFlag2 == false){
        $('#prints').html('<img src="'+IMG+'/wjmm/tongyixieyi_1.png" alt="" class="remember"><span id="agree" >我已阅读并同意</span>');
        remFlag2 =true;
        $('#finished2').addClass('registerfin').removeClass('finish');
    }else{
        $('#prints').html('<img src="'+IMG+'/wjmm/tongyixieyi_2.png" alt="" class="remember"><span id="agree"  class="red">我已阅读并同意</span>');
        remFlag2 =false;
        if( /^1[3|4|5|7|8][0-9]{9}$/.test($("#reaccount2").val())&& /^\d{6}$/.test($("#repassword2").val())&&/^[a-zA-Z0-9_]{5,14}$/.test($("#resurepassword2").val()) && /^[\u4E00-\u9FA5]{2,4}$/.test($("#rename2").val()) &&IdentityCodeValid($("#reid2").val()) ){
            if(emailFlag2){
                if(/\w[-\w.+]*@([A-Za-z0-9][-A-Za-z0-9]+\.)+[A-Za-z]{2,14}/.test($("#reemail2").val())){
                    if(accFlag2==true && yxFlag2== true){
                        $('#finished2').removeClass('registerfin').addClass('finish');
                    }else{
                        $('#finished2').addClass('registerfin').removeClass('finish');
                    }
                }
            }else{
                $('#finished2').removeClass('registerfin').addClass('finish');
            }
        }
    }
});

if(remFlag2){
    $('#finished2').addClass('registerfin').removeClass('finish');
}else{
    if( /^1[3|4|5|7|8][0-9]{9}$/.test($("#reaccount2").val())&& /^\d{6}$/.test($("#repassword2").val())&&/^[a-zA-Z0-9_]{5,14}$/.test($("#resurepassword2").val()) && /^[\u4E00-\u9FA5]{2,4}$/.test($("#rename2").val()) &&IdentityCodeValid($("#reid2").val()) ){
        if(emailFlag2){
            if(/\w[-\w.+]*@([A-Za-z0-9][-A-Za-z0-9]+\.)+[A-Za-z]{2,14}/.test($("#reemail2").val())){
                if(accFlag2==true && yxFlag2== true){
                    $('#finished2').removeClass('registerfin').addClass('finish');
                }else{
                    $('#finished2').addClass('registerfin').removeClass('finish');
                }
            }
        }else{
            $('#finished2').removeClass('registerfin').addClass('finish');
        }
    }else{
        $('#finished2').addClass('registerfin').removeClass('finish');
    }
}

$('#finished2').click(function () {
    if ($.trim($('#reaccount2').val()) == '') {
        $('#iptel').html('<i class="star">* </i><span class="red">手机号不能为空</span>');
        return false;
    }
    if ($.trim($('#repassword2').val()) == '') {
        $('#ipcode').html('<i class="star">* </i><span class="red">验证码不能为空</span>');
        return false;
    }
    if ($.trim($('#resurepassword2').val()) == '') {
        $('#ippassword').html('<i class="star">* </i><span class="red">密码不能为空</span>');
        return false;
    }
    if ($.trim($('#rename2').val()) == '') {
        $('#ipname').html('<i class="star">* </i><span class="red">姓名不能为空</span>');
        return false;
    }
    if ($.trim($('#reid2').val()) == '') {
        $('#ipcard').html('<i class="star">* </i><span class="red">身份证号不能为空</span>');
        return false;
    }
    // 表单提交
    if ($('#finished2').hasClass('registerfin')){
        return false;
    }
    
    // 新修改的ajax
     $.ajax({
        type: 'POST',
        async: true,
        dataType: 'json',
        data:{account:$.trim($('#reaccount2').val()),password:$.trim($('#resurepassword2').val()),vcode:$.trim($('#repassword2').val()),email:$.trim($('#reemail2').val()),truename:$.trim($('#rename2').val()),card:$.trim($('#reid2').val())},
        url:MODULE+'Member/telregister',
        beforeSend: function() {
            $('#finished2').val('注册中').attr("disabled",true);
        },
        success: function(data) {
            if (parseInt(data.status) == 1 ) {
                setTimeout(function() {                           
                    $.ajax({
                        type: 'POST',
                        async: true,
                        dataType: 'json',
                        url:MODULE+'Member/login.html',
                        data: {account:$.trim($('#reaccount2').val()),password:$.trim($('#resurepassword2').val())},
                        beforeSend:function(){
                            $("#finished2").val('正在登陆').attr("disabled",true);
                        },
                        success: function(data) {
                          if (parseInt(data.status) == 1 ) {
                              window.location.href=MODULE+'/Member/personalcenter.html';
                          }
                        },
                        error: function() { 
                            location.reload();                    
                        },
                        cache: false
                    }); 
                },1000);
            } else {
                alert(data.msg);
                $('#finished2').addClass('registerfin').removeClass('finish');
                $("#finished2").val('完成注册').attr("disabled",false);
            }                  
        },
        error: function() {
            alert('服务器故障，稍后再试');
            $("#finished2").val('完成注册').attr("disabled",false);
        },
    }); 


});

// 身份证验证的方法
function IdentityCodeValid(code) {
    var pass = true;
    var idcard = code;

    if(idcard==""){
        pass = false;
    }
    idcard = idcard.toUpperCase();
    if (!(/(^\d{15}$)|(^\d{17}([0-9]|X)$)/.test(idcard))){
        //jQuery('#idcardspan').show();
        pass = false;
        //jQuery("#idcardspan").attr("class", "error");

    }
    //校验位按照ISO 7064:1983.MOD 11-2的规定生成，X可以认为是数字10。
    //下面分别分析出生日期和校验位

    var len, re;
    len = idcard.length;

    if (len == 15){
        re = new RegExp(/^(\d{6})(\d{2})(\d{2})(\d{2})(\d{3})$/);

        var arrSplit = idcard.match(re);
        //检查生日日期是否正确

        var dtmBirth = new Date('19' + arrSplit[2] + '/' + arrSplit[3] + '/' + arrSplit[4]);
        var bGoodDay;
        bGoodDay = (dtmBirth.getYear() == Number(arrSplit[2])) && ((dtmBirth.getMonth() + 1) == Number(arrSplit[3])) && (dtmBirth.getDate() == Number(arrSplit[4]));

        if (!bGoodDay){
            pass = false;
        }else{
            //将15位身份证转成18位
            //校验位按照ISO 7064:1983.MOD 11-2的规定生成，X可以认为是数字10。
            var arrInt = new Array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
            var arrCh = new Array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
            var nTemp = 0, i;
            idcard = idcard.substr(0, 6) + '19' + idcard.substr(6, num.length - 6);

            for(i = 0; i < 17; i ++){
                nTemp += idcard.substr(i, 1) * arrInt[i];
            }
            idcard += arrCh[nTemp % 11];
            //jQuery('#idcardspan').show();
            //ShowMsg("idcardspan", 0, "身份证确认无误。");
            //jQuery("#idcardspan").attr("class", "ok");
        }
    }

    if (len == 18){
        re = new RegExp(/^(\d{6})(\d{4})(\d{2})(\d{2})(\d{3})([0-9]|X)$/);
        var arrSplit = idcard.match(re);
        //检查生日日期是否正确
        var dtmBirth = new Date(arrSplit[2] + "/" + arrSplit[3] + "/" + arrSplit[4]);
        var bGoodDay;
        bGoodDay = (dtmBirth.getFullYear() == Number(arrSplit[2])) && ((dtmBirth.getMonth() + 1) == Number(arrSplit[3])) && (dtmBirth.getDate() == Number(arrSplit[4]));
        if(!bGoodDay){
            pass = false;
        }else{
            //检验18位身份证的校验码是否正确。
            //校验位按照ISO 7064:1983.MOD 11-2的规定生成，X可以认为是数字10。
            var valnum;
            var arrInt = new Array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
            var arrCh = new Array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
            var nTemp = 0, i;
            for(i = 0; i < 17; i ++){
                nTemp += idcard.substr(i, 1) * arrInt[i];
            }
            valnum = arrCh[nTemp % 11];
            if (valnum != idcard.substr(17, 1)){
                pass = false;
            }
            //jQuery('#idcardspan').show();
            //ShowMsg("idcardspan", 0, "确认身份证无误！");
            //jQuery("#idcardspan").attr("class", "ok");
        }
    }

    //if(!pass) alert(tip);
    return pass;
}
