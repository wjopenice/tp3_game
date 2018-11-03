/**
 * Created by user on 2017/4/19.
 */
//分页功能的实现
function page(opt){
    if(!opt.id){return false};
    var obj = document.getElementById(opt.id);
    var aAdiv=document.createElement('div');
    aAdiv.className="aBtns";
    var nowNum = opt.nowNum || 1;
    var allNum = opt.allNum || 5;
    var callBack = opt.callBack || function(){};

    // 当 当前页面大于等于2的 添加上 上一页
    if(nowNum>=2){
        var oA = document.createElement('a');
        oA.id = '#' + (nowNum - 1);
        oA.innerHTML = '上一页';
        aAdiv.appendChild(oA);
        obj.appendChild(aAdiv);
    }

    if(allNum<=5){
        for(var i=1;i<=allNum;i++){

            var oA = document.createElement('a');
            oA.id = '#' + i;
            oA.innerHTML = i;
            if(nowNum == i){
                oA.className = "active";
            }
            else{
                oA.className = "";
            }
            aAdiv.appendChild(oA);
        }
        obj.appendChild(aAdiv);
    }
    else{
        for(var i=1;i<=5;i++){
            var oA = document.createElement('a');


            if(nowNum == 1 || nowNum == 2){

                oA.id = '#' + i;
                oA.innerHTML = i;
                if(nowNum == i){
                    oA.className = "active";
                }
                else{
                    oA.className = "";
                }

            }
            else if( (allNum - nowNum) == 0 || (allNum - nowNum) == 1 ){

                oA.id = '#' + (allNum - 5 + i);

                if((allNum - nowNum) == 0 && i==5){
                    oA.innerHTML = (allNum - 5 + i);
                    oA.className = "active";
                }
                else if((allNum - nowNum) == 1 && i==4){
                    oA.innerHTML = (allNum - 5 + i);
                    oA.className = "active";
                }
                else{
                    oA.innerHTML = (allNum - 5 + i) ;
                    oA.className = "";
                }

            }
            else{
                oA.id = '#' + (nowNum - 3 + i);
                oA.innerHTML = (nowNum - 3 + i);

                if(i==3){
                    oA.className = "active";
                }
                else{
                    oA.className = "";
                }
            }
            aAdiv.appendChild(oA);
        }
        aAdiv.appendChild(oA);
        obj.appendChild(aAdiv);
        var oDiv=document.createElement('div');
        oDiv.className="jump";
        var str="";
        str='<input type="text"/>页<span >跳转</span>';
        oDiv.innerHTML=str;
        obj.appendChild(oDiv);
    }

    if( (allNum - nowNum) >= 1 ){
        var oA = document.createElement('a');
        oA.id = '#' + (parseInt(nowNum) + 1);
        oA.innerHTML = '下一页';
        aAdiv.appendChild(oA);
        obj.appendChild(aAdiv);
    }

    if( (allNum - nowNum) >= 3 && allNum>=6 ){

        var oA = document.createElement('a');
        oA.id = '#' + allNum;
        oA.innerHTML = '尾页';
        aAdiv.appendChild(oA);
        obj.appendChild(aAdiv);

    }

    //callBack(nowNum,allNum);

    var aA = obj.getElementsByTagName('a');

    for(var i=0;i<aA.length;i++){
        aA[i].index=i;
        aA[i].onclick = function(){

            var nowNum = parseInt(this.getAttribute('id').substring(1));

            for(var j=0;j<aA.length;j++){
                
                  aA[j].className = "";   
            }
             aA[this.index].className = "active";  
            
            callBack(nowNum,allNum);

            return false;

        };


    }

//            输入框输入内容后跳转
    $(".jump").find("span").click(function(){
        var inputNum=$(".jump").find("input[type=text]").val();

        var re = /^[1-9]\d*$/;//只能输入非零的正整数；
        if(re.test(inputNum)) {
            if (parseInt(inputNum) > allNum) {
                alert("请输入" + allNum + "以内的页码");
            }else if(parseInt(inputNum) >0){
                var nowNum = inputNum;
                obj.innerHTML='';

                page({

                      id : opt.id,
                    nowNum : nowNum,
                    allNum : allNum,
                    callBack : callBack

                });

               
                  callBack(nowNum,allNum);

                return false;
            }
        }else{
            console.log(inputNum);
        }

    })

//         输入框回车事件
    $(".jump").find("input").keydown(function (e) {
        if (e.which == 13) {
            $(".jump").find("span").trigger("click");//触发搜索按钮的点击事件
        }
    });
  

}

