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