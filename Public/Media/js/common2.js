
var del=document.getElementById("shift");
del.onclick=function () {
    this.parentNode.parentNode.style.display = "none";
};
$('#getGift').find('li').each(function () {
    $(this).find('.lingqu').click(function () {
        var giftName= $(this).parent(".draw").find('.gifts').html();
        var giftId= $(this).parent(".draw").find('.gifts').attr('id');
        console.log(giftName);
        console.log(giftId);

        $.ajax({
            type:'post',
            dataType:'json',
            data:{
                gift:$(this).parent(".draw").find('.gifts').html(),
                giftid:$(this).parent(".draw").find('.gifts').attr('id')
            },
            url:gift_url,
            success:function(data) {
                if (parseInt(data.status) == 1 ) {
//                    登录成功时候的状态
                    if(data.msg=='ok'){
                        var success=document.querySelector('.cart-win');
                        var shut=document.querySelector(".shut");
                        var one=document.querySelector('#giftcode');
//                        礼包码
                        one.innerHTML=data.data;
                        success.style.display='block';
                        shut.onclick=function(){
                            success.style.display='none';
                        };
                    }else if(data.msg=='no'){
//                        您已领取过该礼包
                        var receive=document.querySelector('.receiveComplete');
                        var shutno=document.querySelector(".shutno");
                        var one2=document.querySelector('#giftcode2');
//                        礼包码显示
                        one2.innerHTML=data.data;
                        receive.style.display='block';
                        shutno.onclick=function(){
                            receive.style.display='none';
                        };
                        return ;

                    }else if(data.msg=='noc'){
//                        该礼包已领取完，下次请早
                        var noreceive=document.querySelector('.noreceive');
                        var shutnoc=document.querySelector(".shutnoc");
                        noreceive.style.display='block';
                        shutnoc.onclick=function(){
                            noreceive.style.display='none';
                        };
                        return ;
                    }
                }else if (parseInt(data.status) == 0 ) {
                    //登录失败的时候
                    var win=document.querySelector('.pop');
                    var sub=document.querySelector(".sure");
                    var ret=document.querySelector('.return');
                    win.style.display='block';
                    ret.onclick=function(){
                        win.style.display='none';
                    };
                    sub.onclick=function(){
                        window.location.href='login.html'
                    }
                    return ;
                }
            },
            error:function() {
                alert('服务器故障，请稍候再试。。。。');
            }
        });
    })
})
