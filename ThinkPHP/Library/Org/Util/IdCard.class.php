<?php
/**
 * PHP版身份证校验类
 *
 * @author  yanghb2008@gmail.com
 * @date    2010-11-24
 */
namespace Org\Util;

class IdCard
{
    private static $_error = '';

    /**
     * 得到具体的错误信息
     *
     * @return  string
     */
    public static function getError()
    {
        return self::$_error;
    }

    /**
     * 校验身份证号是否合法
     *
     * @param   string  $idCard 身份证号
     *
     * @return  bool
     */
    public static function isIdCardValid($idCard)
    {
        $idCard = self::_transformIdCard15To18($idCard);
        if (strlen($idCard) == 18) {
            if (strtoupper($idCard) === self::_createIdCard($idCard)) {
                $birthYear = substr($idCard,6,4);
                if ($birthYear < date('Y') && $birthYear > 1900 
                    && checkdate(substr($idCard,10,2), substr($idCard,12,2), $birthYear)) {
                    return true;
                }
            }
            self::$_error = '输入的身份证号码有误，请输入正确的身份证号码[错误代码001]';
            return false;
        }
        self::$_error = '身份证应为15或18位[错误代码002]';
        return false;
    }

    /**
     * 根据身份证号得到用户生日
     *
     * @param   string  $idCard 身份证号
     * @param   string  $separator  分隔符
     *
     * @return  string
     */
    public static function getBirthday($idCard, $separator='-')
    {
        if (self::isIdCardValid($idCard)) {
            $idCard = self::_transformIdCard15To18($idCard);
            return substr($idCard, 6, 4) . $separator . substr($idCard, 10, 2) . $separator . substr($idCard, 12, 2);
        }
        return '0000' . $separator . '00' . $separator . '00';
    }

    /**
     * 根据身份证号得到用户性别
     *
     * @param   string  $idCard 身份证号
     * @param   string  $returnType 默认返回类型
     *
     * @return  string
     */
    public static function getSex($idCard, $returnType=0)
    {
        $sexArray = array(
            array('1', '2', '0'),
            array('男', '女', '未知'),
            array('man', 'woman', 'unknown'),
        );
        if (self::isIdCardValid($idCard)) {
            $idCard = self::_transformIdCard15To18($idCard);
            // 1 为男性 2 为女性
            return substr($idCard, 16,1) % 2 == 0 ? $sexArray[$returnType][1] : $sexArray[$returnType][0];
        }
        return $sexArray[$returnType][2];
    }

    /**
     * 根据身份证号得到用户年龄
     *
     * @param   string  $idCard 身份证号
     * @param   string  $isGetFullAge   是否取足岁
     *
     * @return  int
     */
    public static function getAge($idCard, $isGetFullAge=0)
    {
        if (self::isIdCardValid($idCard)) {
            $idCard = self::_transformIdCard15To18($idCard);
            $cardYear   = substr($idCard,6,4);
            $cardMonth  = substr($idCard,10,2);
            $cardDay    = substr($idCard,12,2);

            $nowAge = 1;
            $fullAge = 0;
            $nowYear = date('Y', time());
            $nowDays = date('z', time());
            $birthDays = date('z', mktime(0, 0, 0, $cardMonth, $cardDay, $cardYear));
            $difference = $nowDays - $birthDays;
            $fullAge = $difference > 0 ? ($nowYear - $cardYear) : ($nowYear - $cardYear - 1);
            $nowAge = $fullAge + 1;
            if($isGetFullAge==0)  return $nowAge;
            else return $fullAge;
        }
    }

    /**
     * 根据身份证号得到用户省份
     *
     * @param   string  $idCard 身份证号
     *
     * @return  string
     */
    public static function getProvince($idCard)
    {
        if (self::isIdCardValid($idCard)) {
            $idCard = self::_transformIdCard15To18($idCard);
            $provinceList = array(
                11 => '北京',
                12 => '天津',
                13 => '河北',
                14 => '山西',
                15 => '内蒙古',
                21 => '辽宁',
                22 => '吉林',
                23 => '黑龙江',
                31 => '上海',
                32 => '江苏',
                33 => '浙江',
                34 => '安徽',
                35 => '福建',
                36 => '江西',
                37 => '山东',
                41 => '河南',
                42 => '湖北',
                43 => '湖南',
                44 => '广东',
                45 => '广西',
                46 => '海南',
                50 => '重庆',
                51 => '四川',
                52 => '贵州',
                53 => '云南',
                54 => '西藏',
                61 => '陕西',
                62 => '甘肃',
                63 => '青海',
                64 => '宁夏',
                65 => '新疆',
                71 => '台湾',
                81 => '香港',
                82 => '澳门',
            );
            return $provinceList[substr($idCard, 0, 2)];
        }
        return '国外';
    }

    /**
     * 根据身份证的前17位算出完整的18位身份证号
     *
     * @param   string  $idCard 身份证号
     *
     * @return  string
     */
    private static function _createIdCard($idCard)
    {
        if (!isset($idCard{16})) {
            self::$_error = '身份证号不合法[错误代码003]';
            return false;
        }
        $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
        $verifyCodeList = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
        $checksum = 0;
        for ($i = 0; $i < 17; $i++){
            $checksum += $idCard{$i} * $factor[$i];
        }
        $mod = $checksum % 11;
        $idCard = substr($idCard, 0, 17) . $verifyCodeList[$mod];
        return strtoupper($idCard);        
    }

    /**
     * 把15位的身份证号转换成18位身份证号
     *
     * @param   string  $idCard 身份证号
     *
     * @return  string
     */
    private static function _transformIdCard15To18($idCard)
    {
        if (strlen($idCard) == 15) {
            $middleCode = in_array(substr($idCard, 12, 3), array('996', '997', '998', '999')) ? 18 : 19;
            $idCard = self::_createIdCard(substr($idCard, 0, 6) . $middleCode . substr($idCard, 6, 9));
        }
        return $idCard;
    }
    
    public static function check_card($card){
    	$card = self::_transformIdCard15To18($card);
        if(!self::isIdCardValid($card)){
        	return 4;
        }     
		$date_time = mktime(0,0,0,intval(substr($card,10,2)),intval(substr($card,12,2)),intval(substr($card,6,4)));
		if($date_time > strtotime('-18 years')){
			return 2;
		}
		return 1;    
    }
    
}
