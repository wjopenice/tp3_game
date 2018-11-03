$(function () {
    
    // 实名认证第一步
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
        }else{
           $('#achieve').removeClass('completeerror').addClass('completesuccess');
       }
        
        var username=$.trim($("#uname").html());
        var psd=$.trim($('#telpassword').val());
        $.ajax({
            type:'post',
            dataType:'json',
            data:{
                    in:"idcard",
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
                    // 成功状态下
                    $('#nobind1').css('display','none');
                    $('#nobind2').css('display','block');
                    $('#nobind3').css('display','none');
                    $('#achieve').attr("disabled",false);
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


    // 实名认证第二步
    $("#name").blur(function(){
        if ($.trim($("#name").val()).length == 0) {
            $('#nametips').html('<i class="star">* </i><span class="red">姓名不能为空</span>');
            $('#achieve2').removeClass('completesuccess').addClass('completeerror');           
            return false;
        }
        if (!(/^[\u4e00-\u9fa5]{2,4}$/.test($("#name").val()))) {
            $('#nametips').html('<i class="star">* </i><span class="red">姓名格式不正确</span>');
            $('#achieve2').removeClass('completesuccess').addClass('completeerror');          
            return false;
        }
        if (/^[\u4e00-\u9fa5]{2,4}$/.test($("#name").val())) {
            $('#nametips').html('<img src="'+zqimg+'" >');
        }
        if(/^[\u4e00-\u9fa5]{2,4}$/.test($("#name").val())&&IdentityCodeValid($.trim($("#idcard").val()))){
            $('#achieve2').removeClass('completeerror').addClass('completesuccess');           
        }
    });

    $("#idcard").blur(function(){
        var  idNumber=$.trim($("#idcard").val());
        if (idNumber.length == 0) {
            $('#idcodetips').html('<i class="star">* </i><span class="red">身份证号不能为空</span>');
            $('#achieve2').removeClass('completesuccess').addClass('completeerror');           
            return false;
        }
        if (!(IdentityCodeValid(idNumber))) {
            $('#idcodetips').html('<i class="star">* </i><span class="red">身份证号格式不正确</span>');
            $('#achieve2').removeClass('completesuccess').addClass('completeerror');            
            return false;
        }

        if ((IdentityCodeValid(idNumber))) {
            $('#idcodetips').html('<img src="'+zqimg+'" >');
        }
        if(/^[\u4e00-\u9fa5]{2,4}$/.test($("#name").val())&&IdentityCodeValid($.trim($("#idcard").val()))){
            $('#achieve2').removeClass('completeerror').addClass('completesuccess');           
        }
    });

    $('#achieve2').on('click',function() {
        if ($.trim($("#name").val()).length == 0) {
            $('#nametips').html('<i class="star">* </i><span class="red">姓名不能为空</span>');
            $('#achieve2').removeClass('completesuccess').addClass('completeerror');            
            return false;
        }
        if (!(/^[\u4e00-\u9fa5]{2,4}$/.test($("#name").val()))) {
            $('#nametips').html('<i class="star">* </i><span class="red">姓名格式不正确</span>');
            $('#achieve2').removeClass('completesuccess').addClass('completeerror');           
            return false;
        }
        var  idNumber=$.trim($("#idcard").val());
        if (idNumber.length == 0) {
            $('#idcodetips').html('<i class="star">* </i><span class="red">身份证号不能为空</span>');
            $('#achieve2').removeClass('completesuccess').addClass('completeerror');            
            return false;
        }
        if (!(IdentityCodeValid(idNumber))) {
            $('#idcodetips').html('<i class="star">* </i><span class="red">身份证号格式不正确</span>');
            $('#achieve2').removeClass('completesuccess').addClass('completeerror');          
            return false;
        }
        if ($('#achieve2').hasClass('completeerror')){
            return false;
        }
        var name=$.trim($("#name").val());
        var idcard=$.trim($("#idcard").val());       
        $.ajax({
            type:'post',
            dataType:'json',
            data:'real_name='+name+'&idcard='+idcard,
            url:cardurl,
            beforeSend:function(){
            	 $('#achieve2').attr("disabled",true);
            },
            success:function(data) {
                if (parseInt(data.status) == 1 ) {
                    // 成功状态下
                    $('#nobind1').css('display','none');
                    $('#nobind2').css('display','none');
                    $('#nobind3').css('display','block');
                    setTimeout(function () {
                    window.location.href=idcardurl;
                    },1000)
                } else {
                    $('#idcodetips').html('<i class="star">* </i><span class="red">'+data.msg+'</span>');
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


});