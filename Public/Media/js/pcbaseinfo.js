/**
 * Created by user on 2017/4/5.
 */
$(function() {
    $("#baseInfo_r").find(".user_account").val(useInfo.user_account);
    $("#baseInfo_r").find(".user_nickName").val(useInfo.user_nickName);
    $("#baseInfo_r").find(".plat_yue").val(useInfo.plat_yue);
    var imgSrc=[img+"personalcenter/baseinfo/anqundengji.png",img+"personalcenter/baseinfo/anquandengjigao.png"];
    if($.trim(useInfo.phoneNum)){
        $("#baseInfo_r").find(".phoneNum").val(hideInfo (useInfo.phoneNum,3,4));
        $(".bind_phone").html('<a   href="'+changephoneurl+'" id="bind">更&nbsp;&nbsp;换</a>');
        
        $("#baseInfo_l p").find("img").attr("src",imgSrc[1]);
        $("#baseInfo_l .safe").html(' 安全级别：<span class="level_gao">高</span>');
    }else{
        $("#baseInfo_r").find(".phoneNum").val("未绑定");
        $(".bind_phone").html('<a href="'+bindphoneurl+'" id="bind">绑&nbsp;&nbsp;定</a>');
        $("#baseInfo_l p").find("img").attr("src",imgSrc[0]);
        $("#baseInfo_l .safe").html(' 安全级别：<span class="level_di">低</span>');
    }
    if($.trim(useInfo.name)){
        $("#baseInfo_r").find(".name").val(hideInfo (useInfo.name,1,0));
        $(".rz_name").html('');
    }else{
        $("#baseInfo_r").find(".name").val("未绑定");
        $(".rz_name").html('<a href="'+bindcard+'" id="identify">实名认证</a>');
    }
    if($.trim(useInfo.idCard)){
        $("#baseInfo_r").find(".idCard").val(hideInfo (useInfo.idCard,3,4));
    }else{
        $("#baseInfo_r").find(".idCard").val("未绑定");
    }

    function hideInfo (str,frontLen,endLen) {
        var len = str.length-frontLen-endLen;
        var xing = '';
        for (var i=0;i<len;i++) {
            xing+='*';
        }
        return str.substring(0,frontLen)+xing+str.substring(str.length-endLen);
    }

    $("#xiugia").find("input").click(function(){
    	$("#xiugai-tk").parent(".noreceive").show();
    	$("#xiugai-tk").find('input[name=cancelBtn]').click(function(){
    		$("#xiugai-tk").parent(".noreceive").hide();
    	});
    	$("#xiugai-tk").find('input[name=sureBtn]').click(function(){
    		var  newNick=$.trim($("#xiugai-tk").find('input[name=niCheng]').val());  
    		 if(newNick){
                 $.ajax({
                        type: "POST",
                        url: MODULE+'/Member/modify_nickname',
                        data: {
                                account:$("#baseInfo_l").find(".user_ac").find("span").html(),
                                nickname:newNick
                         },
                        async: false,
                        dataType:'json',
                        beforeSend:function(){
                            $("#xiugai-tk").find('input[name=sureBtn]').attr("disabled",true);
                        },
                        success:function(data){                           
                            if(data.status==1){
                            	$("#xiugai-tk").parent(".noreceive").hide();
                                $("#xiugai-tk").find('input[name=sureBtn]').attr("disabled",false);
                                $("#baseInfo_l").find(".user_nick").find("span").html(newNick);                    
                                $("#yhnic").find("label").html(' <span>用户昵称：</span><input id="changeNic" value='+newNick+' type="text"  readonly /> ');                                
                            }else{
                                $("#xiugai-tk").find('input[name=sureBtn]').attr("disabled",false);
                               	$("#xiugai-tk").parent(".noreceive").hide();
                            }
                        },
                        error:function(){
                            $("#xiugai-tk").find('input[name=sureBtn]').attr("disabled",false);
                            $("#xiugia").html('<input type="button" value="修改" name="xgnc"/>');
                            alert("服务器端故障，请稍后重试!")
                            $("#xiugai-tk").parent(".noreceive").hide();
                        }
                    })
            }else{
            	$("#xiugai-tk").parent(".noreceive").hide();
            }     
    	})
           
             
    })


})
