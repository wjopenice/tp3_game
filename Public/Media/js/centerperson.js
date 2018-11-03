$(function(){
    // 旧密码
    $("#oldpassword").blur(function(){
        var opsd=$.trim($('#oldpassword').val());
        var npsd=$.trim($('#newpassword').val());
        var spsd=$.trim($('#surepassword').val());
        if (opsd == '') {
            $('#oldtips').html('<i class="star">* </i><span class="red">旧密码不能为空</span>');
            return false;
        }
        if(!(/[0-9a-zA-Z_]{6,29}/.test(opsd))){
            $('#oldtips').html('<i class="star">* </i><span class="red">旧密码格式不正确</span>');
            return false;
        }else{
            $('#oldtips').html('<img src="'+zqimg+'" >');
        }
      
        
        if(opsd !== ''&& npsd !== ''&& (/[0-9a-zA-Z_]{6,29}/.test(opsd)) && (/^[a-zA-Z0-9_]{6,14}$/.test(npsd)) && spsd == npsd){
           $('#achieve').removeClass('completeerror').addClass('completesuccess');
        }else{
            $('#achieve').removeClass('completesuccess').addClass('completeerror');            
        }

    });



   
// 用户密码
    $("#newpassword").blur(function(){
        var opsd=$.trim($('#oldpassword').val());
        var npsd=$.trim($('#newpassword').val());
        var spsd=$.trim($('#surepassword').val());
        if (npsd == '') {
            $('#newtips').html('<i class="star">* </i><span class="red">密码不能为空</span>');
            return false;
        }
        // 新密码6-16非纯数字(!(/^(?![0-9]+$)[0-9A-Za-z]{6,15}$/.test($("#newpassword").val())) )
        if (!(/^(?![0-9]+$)[0-9A-Za-z]{6,15}$/.test(npsd)) ){
            $('#newtips').html('<i class="star">* </i><span class="red">密码格式错误</span>');
            return false;
        }else{
            $('#newtips').html('<img src="'+zqimg+'" >');           
        }
       
        if(opsd !== ''&& npsd !== ''&& (/[0-9a-zA-Z_]{6,29}/.test(opsd)) && (/^[a-zA-Z0-9_]{6,14}$/.test(npsd)) && spsd == npsd){
           $('#achieve').removeClass('completeerror').addClass('completesuccess');
        }else{
            $('#achieve').removeClass('completesuccess').addClass('completeerror');            
        }

    });
// 确认密码
    $("#surepassword").blur(function(){
        var opsd=$.trim($('#oldpassword').val());
        var npsd=$.trim($('#newpassword').val());
        var spsd=$.trim($('#surepassword').val());
        if (spsd == '') {
            $('#suretips').html('<i class="star">* </i><span class="red">密码不能为空</span>');
            return false;
        }

        if ( spsd!== npsd ){
            $('#suretips').html('<i class="star">* </i><span class="red">两次密码不一致</span>');
            return false;
        }else{
           $('#suretips').html('<img src="'+zqimg+'" >');
        }      
        
        if(opsd !== ''&& npsd !== ''&& (/[0-9a-zA-Z_]{6,29}/.test(opsd)) && (/^[a-zA-Z0-9_]{6,14}$/.test(npsd)) && spsd == npsd){
           $('#achieve').removeClass('completeerror').addClass('completesuccess');
        }else{
            $('#achieve').removeClass('completesuccess').addClass('completeerror');            
        }
    });



    $("#oldpassword").on('focus',function () {
        $('#oldtips').html('<i class="star">* </i> <span class="zi"> 请输入旧密码</span>');
    });
    $("#newpassword").on('focus',function () {
        $('#newtips').html('<i class="star">* </i>  <span class="zi"> 请输入 6-30 位数字、字母</span>');
    });
    $("#surepassword").on('focus',function () {
        $('#suretips').html('<i class="star">* </i><span class="zi"> 请再次输入密码</span>');
    });


    // 完成按钮
    $('#achieve').click(function () {
        var opsd=$.trim($('#oldpassword').val());
        var npsd=$.trim($('#newpassword').val());
        var spsd=$.trim($('#surepassword').val());
        if (opsd == '') {
            $('#oldtips').html('<i class="star">* </i><span class="red">旧密码不能为空</span>');
            return false;
        }
        if (npsd == '') {
            $('#newtips').html('<i class="star">* </i><span class="red">密码不能为空</span>');
            return false;
        }
        if (spsd == '') {
            $('#suretips').html('<i class="star">* </i><span class="red">密码不能为空</span>');
            return false;
        }
        if (spsd !=npsd ) {
            $('#suretips').html('<i class="star">* </i><span class="red">两次密码不一致</span>');
            return false;
        }

    
        if(opsd !== ''&& npsd !== ''&& (/[0-9a-zA-Z_]{6,29}/.test(opsd)) && (/^[a-zA-Z0-9_]{6,14}$/.test(npsd)) && spsd == npsd){
           $('#achieve').removeClass('completeerror').addClass('completesuccess');
        }else{
            $('#achieve').removeClass('completesuccess').addClass('completeerror');            
        }
    var username=$.trim($("#uname").html());   
            $.ajax({
                type: 'post',
                url: pwdsuburl,
                data:{name:username,
                      oldpassword:opsd,
                      newpassword:npsd
                    },
                dataType: 'json',
                success: function (data) {
                    if (parseInt(data.status) == 1 ) {
                        //ajax请求成功后的逻辑处理  ,显示对号
                        location.href=login;
                        //修改成功跳转页面
                    }if (parseInt(data.status) == -1) {                    
                        $('#oldtips').html('<i class="star">* </i><span class="red">'+data.msg+'</span>');
                          return false;
                    }if (parseInt(data.status) == -2) {                      
                          $('#newtips').html('<i class="star">* </i><span class="red">'+data.msg+'</span>');
                          location.href=login;
                          return false;
                    }if (parseInt(data.status) == -3) {                
                          $('#suretips').html('<i class="star">* </i><span class="red">'+data.msg+'</span>');
                          $('#achieve').removeClass('completesuccess').addClass('completeerror');
                          return false;
                    }if(parseInt(data.status) == -4){
                      $('#oldtips').html('<i class="star">* </i><span class="red">'+data.msg+'</span>');
                      $('#achieve').removeClass('completesuccess').addClass('completeerror');
                      return false;
                    }

            
                },
                error: function () {
                    alert('服务器故障，请稍候再试。。。。');
                }
            });





    });


});