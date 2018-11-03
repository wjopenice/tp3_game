// 小火箭
$(function() {
    var e = $("#rocket-to-top"),
        t = $(document).scrollTop(),
        n,
        r,
        i = !0;
    $(window).scroll(function() {
        var t = $(document).scrollTop();
        t == 0 ? e.css("background-position") == "0px 0px" ? e.fadeOut("slow") : i && (i = !1, $(".level-2").css("opacity", 1), e.delay(100).animate({
                marginTop: "-1000px"
            },
            "normal",
            function() {
                e.css({
                    "margin-top": "-125px",
                    display: "none"
                }),
                    i = !0
            })) : e.fadeIn("slow")
    }),
        e.hover(function() {
                $(".level-2").stop(!0).animate({
                    opacity: 1
                })
            },
            function() {
                $(".level-2").stop(!0).animate({
                    opacity: 0
                })
            }),
        $(".level-3").click(function() {
            function t() {
                var t = e.css("background-position");
                if (e.css("display") == "none" || i == 0) {
                    clearInterval(n),
                        e.css("background-position", "0px 0px");
                    return
                }
                switch (t){
                    case "0px 0px":
                        e.css("background-position", "-298px 0px");
                        break;
                    case "-298px 0px":
                        e.css("background-position", "-447px 0px");
                        break;
                    case "-447px 0px":
                        e.css("background-position", "-596px 0px");
                        break;
                    case "-596px 0px":
                        e.css("background-position", "-745px 0px");
                        break;
                    case "-745px 0px":
                        e.css("background-position", "-298px 0px");
                }
            }
            if (!i) return;
            n = setInterval(t, 50),
                $("html,body").animate({scrollTop: 0},"slow");
        });
});


$(function(){
    $(window).scroll(function () {
        //1 页面滚动时获取卷曲高度
        var h = $(this).scrollTop();
        //获取头部的高度
        var tophead = $("#topPart").height();
        var navHeight = $("#navBar").height();
        if (h > tophead) {
            //让导航部定位
            $("#navBar").addClass("fixed");
        } else {
            $("#navBar").removeClass("fixed");
        }
    })
});



// 排行榜

$(function(){
     var $gameOrder= $(".weekly-list");
            $gameOrder.find("li").eq(0).addClass('current');
            $gameOrder.find("li").each(function(){
                var num=$(this).find('.app-show-title').find('span').html();
                $(this).find('.app-show-block').find('span').html(num);
            });
            $gameOrder.find("li").mouseover(function(){
                $(this).addClass('current').siblings('li').removeClass('current');
            })
})


// 20170804修改开始处

//鼠标样式

// $(function(){
//     var stararr = ["#FFFC82", "#B8F1FD", "#ADF1B9", "#C99BDC", "#FFACFC"];

//     document.onmousemove = function (e) {
//         var star = document.createElement("div");
//         star.className = "star";
//         var num = Math.floor(Math.random() * 5);
//         star.style.backgroundColor = stararr[num];
//         document.body.appendChild(star);

//         star.style.left = getPageX(e) + Math.random() * 50 - 20 + "px";
//         star.style.top = getPageY(e) + Math.random() * 50 - 20 + "px";
//         $(star).animate({
//             "width": 0,
//             "height": 0
//         }, 500, function () {
//             $(star).remove();
//         });
//     };
//     function getPageX(e) {
//         //获取鼠标针对可视区域的位置
//         var x = e.clientX;
//         return scroll().left + x;

//     }


//     function getPageY(e) {
//         //获取鼠标针对可视区域的位置
//         var y = e.clientY;
//         return scroll().top + y;

//     }

//     function scroll() {
//         return {
//             top: window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop || 0,
//             left: window.pageXOffset || document.documentElement.scrollLeft || document.body.scrollLeft || 0
//         };
//     }

//     function animate(tag, obj, fn) {
//         clearInterval(tag.timer);
//         tag.timer = setInterval(function () {
//             var flag = true;
//             for (k in obj) {
//                 if (k == "opacity") {
//                     var leader = getStyle(tag, k) * 100;
//                     var target = obj[k] * 100;
//                     var step = (target - leader) / 10;
//                     step = target > leader ? Math.ceil(step) : Math.floor(step);
//                     leader += step;
//                     tag.style[k] = leader / 100;
//                 } else if (k == "zIndex") {
//                     tag.style.zIndex = obj[k];
//                 } else {
//                     var leader = parseInt(getStyle(tag, k)) || 0;
//                     var target = obj[k];
//                     var step = (target - leader) / 10;
//                     step = target > leader ? Math.ceil(step) : Math.floor(step);
//                     leader += step;
//                     tag.style[k] = leader + "px";
//                 }
//                 if (target != leader) {
//                     flag = false;
//                 }
//             }
//             if (flag) {
//                 clearInterval(tag.timer);
//                 if (typeof fn == "function") {
//                     fn();
//                 }
//             }
//     }, 17)
//     }

//     function getStyle(tag, attr) {
//         return tag.currentStyle ? tag.currentStyle[attr] : getComputedStyle(tag, null)[attr];
//     }

// });



// 20170804修改结尾处


// var close = document.getElementById("cha");
// close.onclick=function () {
//     this.parentNode.style.display = "none";
//
// }







// $(function(){
//     $('.cosplay .content ul').width(800*$('.cosplay .content li').length+'px');
//     $(".cosplay .tab a").mouseover(function(){
//         $(this).addClass('on').siblings().removeClass('on');
//         var index = $(this).index();
//         number = index;
//         var distance = -800*index;
//         $('.cosplay .content ul').stop().animate({
//             left:distance
//         });
//     });
//
//     var auto = 1;  //等于1则自动切换，其他任意数字则不自动切换
//     if(auto ==1){
//         var number = 0;
//         var maxNumber = $('.cosplay .tab a').length;
//         function autotab(){
//             number++;
//             number == maxNumber? number = 0 : number;
//             $('.cosplay .tab a:eq('+number+')').addClass('on').siblings().removeClass('on');
//             var distance = -800*number;
//             $('.cosplay .content ul').stop().animate({
//                 left:distance
//             });
//         }
//         var tabChange = setInterval(autotab,3000);
//         //鼠标悬停暂停切换
//         $('.cosplay').mouseover(function(){
//             clearInterval(tabChange);
//         });
//         $('.cosplay').mouseout(function(){
//             tabChange = setInterval(autotab,3000);
//         });
//     }
// });

// 角色扮演
$(function(){
    $(".cosplay .tab a").mouseover(function(){
        $(this).addClass('on').siblings().removeClass('on');
        var index = $(this).index();
        number = index;
        $('.cosplay .content li').hide();
        $('.cosplay .content li:eq('+index+')').show();
    });

    var auto = 1;  //等于1则自动切换，其他任意数字则不自动切换
    if(auto ==1){
        var number = 0;
        var maxNumber = $('.cosplay .tab a').length;
        function autotab(){
            number++;
            number == maxNumber? number = 0 : number;
            $('.cosplay .tab a:eq('+number+')').addClass('on').siblings().removeClass('on');
            $('.cosplay .content ul li:eq('+number+')').show().siblings().hide();
        }
        var tabChange = setInterval(autotab,3000);
        //鼠标悬停暂停切换
        $('.cosplay').mouseover(function(){
            clearInterval(tabChange);
        });
        $('.cosplay').mouseout(function(){
            tabChange = setInterval(autotab,3000);
        });
    }
     //游戏开服自动切换
    var kf_allAmount = Math.ceil($('.kaifu-b ul li').length/11);
    if( kf_allAmount == 1){
        return false;
    }
    for(var i = 0;i<kf_allAmount;i++){
        var o_span = '<span class="circle_kaifu" style=""></span>';
        var o_spanOn = '<span class="circle_kaifu on" style=""></span>'
        if( i==0 ){
            $('.circle_div').append(o_spanOn);
        }else{
            $('.circle_div').append(o_span);
        }
    }
    $(".circle_div span").bind('mouseover',function(){
        $(this).addClass('on').siblings().removeClass('on');
        var _index = $(this).index();
        number_kf = _index;
        var start = _index*11;
        var end = start + 10;
        resetLi();
        for(var i = start; i <= end ; i++){
            $('.kaifu-b ul li').eq(i).show();
        } 
    });
    function resetLi(){
        for(var i = 0;i<$('.kaifu-b ul li').length;i++){
             $('.kaifu-b ul li').eq(i).hide();
        }
    }
    if(1){
        var number_kf = 0;
        var maxNumber_kf = $('.circle_div span').length;
        function auto_kf(){
            number_kf++;
            number_kf == maxNumber_kf? number_kf = 0 : number_kf;
            $('.circle_div span:eq('+number_kf+')').addClass('on').siblings().removeClass('on');
            start = number_kf*11;
            end = start + 10;
            resetLi();
            for(var i = start; i <= end ; i++){
                $('.kaifu-b ul li').eq(i).show();
            } 
        }
        var change_kf = setInterval(auto_kf,3500);
        //鼠标悬停暂停切换
        $('.kaifu').mouseover(function(){
            clearInterval(change_kf);
        });
        $('.kaifu').mouseout(function(){
            change_kf = setInterval(auto_kf,3500);
        });
    }
});


