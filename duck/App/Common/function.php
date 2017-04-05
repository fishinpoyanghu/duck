<?php
/**
 *
 * User:十八
 * QQ：274020083
 * Date: 16/7/1
 * Time: 下午4:39
 */


require_once('home.php');
require_once('tool.php');

// 检测输入的验证码是否正确，$code为用户输入的验证码字符串
function check_verify($code, $id = '')
{
    $verify = new \Think\Verify();
    return $verify->check($code, $id);
}
function get_references(){

    return $_SESSION['member']['account'];
}
//会员是否登录
function check_is_login(){

    $member = session('member');
    if (empty($member)) {
        return 0;
    } else {
        return $member['id'];
    }
}
function check_admin_login(){

    $member = session('admin');
    if (empty($member)) {
        return 0;
    } else {
        return $member['id'];
    }
}

//检查密码
function checkPwd($password){

    if($password==''){

        return false;
    }

}

//检查手机
function checkPhone($phone){

    if($phone == '')
    {
        return false;
    }

    if(!preg_match("/^13[0-9]{1}[0-9]{8}$|17[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$/",$phone)){

        return false;
    }
}

//检查支付宝
function checkAlipay($alipay){

    if($alipay != ''){

        $ret = M('member')->where(array('alipay'=>$alipay))->find();

        if($ret){

            return false;
        }
    }
}

//是否手机
function isMobile()
{
    // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
    if (isset ($_SERVER['HTTP_X_WAP_PROFILE']))
    {
        return true;
    }
    // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
    if (isset ($_SERVER['HTTP_VIA']))
    {
        // 找不到为flase,否则为true
        return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
    }
    // 脑残法，判断手机发送的客户端标志,兼容性有待提高
    if (isset ($_SERVER['HTTP_USER_AGENT']))
    {
        $clientkeywords = array ('nokia',
            'sony',
            'ericsson',
            'mot',
            'samsung',
            'htc',
            'sgh',
            'lg',
            'sharp',
            'sie-',
            'philips',
            'panasonic',
            'alcatel',
            'lenovo',
            'iphone',
            'ipod',
            'blackberry',
            'meizu',
            'android',
            'netfront',
            'symbian',
            'ucweb',
            'windowsce',
            'palm',
            'operamini',
            'operamobi',
            'openwave',
            'nexusone',
            'cldc',
            'midp',
            'wap',
            'mobile'
        );
        // 从HTTP_USER_AGENT中查找手机浏览器的关键字
        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT'])))
        {
            return true;
        }
    }
    // 协议法，因为有可能不准确，放到最后判断
    if (isset ($_SERVER['HTTP_ACCEPT']))
    {
        // 如果只支持wml并且不支持html那一定是移动设备
        // 如果支持wml和html但是wml在html之前则是移动设备
        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html'))))
        {
            return true;
        }
    }
    return false;
}
/**
 * 计算已经分红了多少天
 * @param  [type] $uid    [description]
 * @param  [type] $fundid [description]
 * @return [type]         [description]
 */
function countDay($uid,$fundid){
    $uid = $uid?$uid:$_SESSION['member']['id'];
    $count = M('queue_log')->where(array('uid'=>$uid,'fundid'=>$fundid))->count();
    $count = $count ? $count : 0;
    return $count;
}
/**
 * 计算已经分红了多少钱
 * @param  [type] $uid    [description]
 * @param  [type] $fundid [description]
 * @return [type]         [description]
 */
function sumMoney($uid,$fundid){
    echo $fundid;
    $uid = $uid?$uid:$_SESSION['member']['id'];
    $sum = M('queue_log')->where(array('uid'=>$uid,'fundid'=>$fundid))->sum('bonusmoney');
    // echo M('queue_log')->getLastSql();
    $sum = $sum ? $sum : '0.00';
    return $sum;
}
/**
 * 统计下级个数
 * @param  [type] $userName     [会员账户]
 * @param  [type] $activation [是否激活会员]
 * @return [type]             [description]
 */
function countSubordinate($userName,$activation){
    if(!$activation){
        $count = M('member')->where(array('references'=>$userName))->count();
    }else{
        $count = M('member')->where(array('references'=>$userName,'active'=>1))->count();
    }
    return $count;
}
/**
 * 获取团队人数
 * @param  [string] $name 介绍人姓名
 * @param  [int] $rank  获取下级几级
 * @param  [bool] $activation  是否获取激活的会员
 * @return [type]       [description]
 */
 function getTeam($name,$rank=26,$activation=false,$lev=0){
     global $num ;
     $num++;
     if(!$name) return false;
     if($activation){//获取激活的下级会员
         $userList = M('member')->field('id,account,active')->where(array('references'=>$name,'active'=>1))->select();
     }else{
         $userList = M('member')->field('id,account,active')->where(array('references'=>$name))->select();
     }
     if($userList && $num<=$rank){
         foreach ($userList as $key => $value) {
             $userList[$key]['lev'] = $num;
             $userList['list'] = getTeam($value['account'],$rank,$activation);
             $arr[] = $userList;
         }
     }
     return $num;
 }


function ifind($arr,$name){

    static $data=array();
    foreach($arr as $k=>$v){
        if($v['account']==$name){
            $data[]=$v;
            }
        if($v['references']==$name){
            $data[]=$v;
            }
        }
    return $data;
}
