
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

function animate(tag, attr, target) {
    clearInterval(tag.timer);
    tag.timer = setInterval(function () {
        //获取任意的样式属性
        //如果未设置某个属性。取出了auto，转换为NaN，这时为了程序可以执行。使用短路操作
        var leader = parseInt(getStyle(tag, attr)) || 0;
        var step = ( target - leader ) / 10;
        step = target > leader ? Math.ceil(step) : Math.floor(step);
        leader = leader + step;
        //设置的时候，设置给对应样式
        tag.style[attr] = leader + "px";
        if (leader == target) {
            clearInterval(tag.timer);
        }
    }, 17);
}

//获取计算后的样式
function getStyle(tag, attr) {
    return tag.currentStyle ? tag.currentStyle[attr] : getComputedStyle(tag, null)[attr];
}/**
 * Created by 12 on 2017/3/9.
 */








