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
    //echo $fundid;
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
        $count = M('member')->where(array('references'=>$userName,'level'=>array('neq',0)))->count();
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
         $userList = M('member')->field('id,account,active')->where(array('references'=>$name,'level'=>array('neq'=>0)))->select();
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

/**
 * @param $userId 用户ID
 * @param $desc   用户描述
 * @param $type   类型:类型:1购买牲畜,2购买饲料,3购买激活码,4开地, 7收取小鸡 8收取蛋蛋 9升级狗 10 孵化机放蛋 11 出蛋 12 孵化机出鸡 //5绿地孵化。6，增养， 取消掉换别的日志
 */
function addLog($userId,$desc,$type,$price){
    $id = M('user_finance')->add(array(
        'userid'        => $userId,
        'desc'          => $desc,
        'create_time'   => time(),
        'type'          =>  $type,
        'number'        =>  $price
    ));
    return $id;
}

/**
 * @param $userId 使用者ID
 * @param $type 1 激活码 2饲料
 * @param $toUserId 给谁 使用的
 * @return mixed
 */
function addResourceLog($userId,$type,$toUserId){
    $id = M('resource_log')->add(array(
        'userid'        => $userId,
        'create_time'   => time(),
        'type'          =>  $type,
        'touserid'      =>  $toUserId
    ));
    return $id;
}

/**
 * 计算相差多少天
 * @param $currentTime
 * @param $oldTime
 * @return float|int
 */
function pastDay($currentTime,$oldTime){
    if($currentTime < $oldTime) return 0;
    $differenceTime = ($currentTime - $oldTime) /86400;
    return ceil($differenceTime);
}

/**
 * 计算应该产生几个蛋
 * @param $day 已经过去了多少天
 * @return int
 */
function outputAmount($day){
    if($day >= 1 && $day <= 30){
        return C('januaryBroodNumber');
    } elseif($day >= 31 && $day <= 60) {
        return C('FebruaryBroodNumber');
    }elseif($day >= 61 && $day <= 90){
        return C('MarchBroodNumber');
    }
    return 0;
}

/**
 * 奖金记录
 * @param $userid
 * @param $touserid
 * @param $number
 * @param $desc
 * @return mixed
 */
function addBonusLog($userid,$touserid,$number,$desc){
    return M('bonus_log')->add(array(
        'userid'    =>  $userid,
        'touserid'  =>  $touserid,
        'number'    =>  $number,
        'desc'      =>  $desc,
        'create_time'   => time(),
    ));
}
function RecordLog($uname, $text) {

   $date_time = date("y-m-d H:i:s");

   $LOG_FILE = "./".date("Ymd").$uname.".txt";

   $text = "$date_time  ".$text;



   if (!file_exists($LOG_FILE)) {

    touch($LOG_FILE);

    //chmod($LOG_FILE,"744");

   }



   $fd = @fopen($LOG_FILE, "a");

   @fwrite($fd, $text."\r\n");

   @fclose($fd);

}
function parseRecv($source)
        {
            $ret = array();
            $temp = explode("&",$source);

            foreach ($temp as $value)
            {
                $index=strpos($value,"=");
                $_key=substr($value,0,$index);
                $_value=substr($value,$index+1);
                $ret[$_key] = $_value;
            }

            return $ret;
        }
function rsaVerify($signData,$merchantSign,$serverCert)
        {
            $cert111 = base64_encode(hex2bin($serverCert));
            $cert111 = "-----BEGIN CERTIFICATE-----\n".preg_replace('/(.{64})/',"$1\n",$cert111)."\n-----END CERTIFICATE-----";
            return openssl_verify($signData, hex2bin($merchantSign), $cert111);
        }
function arrayToXml($requestData){

    $xmlData = "<xml>";

    foreach($requestData as $key1 => $value1)  {

       $xmlData = $xmlData."<".$key1.">"."<![CDATA[".$value1."]]></".$key1.">";                                       

    }  

    $xmlData = $xmlData."</xml>";  

    return $xmlData;    

}
/*

 功能 发送HTTP请求

  URL  请求地址frsaSign

  data 请求数据数组

*/

function POSTDATA1($url, $data)
    {
        $url = parse_url($url);
		
        if (!$url)
        {
            RecordLog("couldn't parse url");
            return "couldn't parse url";
        }
        if (!isset($url['port'])) { $url['port'] = ""; }

        if (!isset($url['query'])) { $url['query'] = ""; }


        $encoded = iconv('GB2312','UTF-8',$data);
        $urlHead = null;
        $urlPort = $url['port'];
        if($url['scheme'] == "https")
        {
            $urlHead = "ssl://".$url['host'];
            if($url['port'] == null || $url['port'] == 0)
            {
                $urlPort = 443;
            }
        }
        else
        {
            $urlHead = $url['host'];
            if($url['port'] == null || $url['port'] == 0)
            {
                $urlPort = 80;
            }
        }
        $fp = fsockopen($urlHead, $urlPort);

        if (!$fp) return "Failed to open socket to $url[host]";

        $tmp="";
        $tmp.=sprintf("POST %s%s%s HTTP/1.0\r\n", $url['path'], $url['query'] ? "?" : "", $url['query']);
        $tmp.="Host: $url[host]\r\n";
        $tmp.="Content-type: application/x-www-form-urlencoded\r\n";
        $tmp.="Content-Length: " . strlen($encoded) . "\r\n";
        $tmp.="Connection: close\r\n\r\n";
        $tmp.="$encoded\r\n";
		
        RecordLog("YGM","发送数据".$tmp);

        fputs($fp,$tmp);

        $line = fgets($fp,1024);

        if (!preg_match("#^HTTP/1\.. 200#i", $line))
        {
            $logstr = "MSG".$line;
            RecordLog("YGM",$logstr);
            return array("FLAG"=>0,"MSG"=>$line);
        }

        $results = ""; $inheader = 1;
        while(!feof($fp))
        {
            $line = fgets($fp,1024);
            if ($inheader && ($line == "\n" || $line == "\r\n"))
            {
                $inheader = 0;
            }
            elseif (!$inheader)
            {
                $results .= $line;
            }
        }

        fclose($fp);
        RecordLog("YGM","接受数据".$results);
        return array("FLAG"=>1,"MSG"=>$results);
    }

function rsaSign($certPaths,$certPass,$signData)

{	
		
       $rtValue = array();
		
       $pkcs12 = file_get_contents($certPaths);
	   //var_dump($pkcs12);
       openssl_pkcs12_read($pkcs12,$certs,$certPass);

       $privateKey = $certs['pkey']; 
       $publicKey = $certs['cert'];

       $merchantCert = strtoupper(bin2hex(base64_decode($publicKey)));

       $merchantCert = substr($merchantCert,24,strlen($merchantCert));

       $merchantCert = substr($merchantCert,0,strlen($merchantCert)-20);              

       openssl_sign($signData,$binarySignature,$privateKey,OPENSSL_ALGO_SHA1);

       $merchantSign = strtoupper(bin2hex($binarySignature));

       $rtValue['merchantCert']=$merchantCert;

       $rtValue['merchantSign']=$merchantSign;

       return $rtValue;

}
function xmlToArray($xml){    

        //禁止引用外部xml实体

        libxml_disable_entity_loader(true);

        $values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);        

        return $values;

}
function POSTDATA($url, $data)
{
    $url = parse_url($url);
    if (!$url)
    {
        //RecordLog("couldn't parse url");
        return "couldn't parse url";
    }
    if (!isset($url['port'])) { $url['port'] = ""; }

    if (!isset($url['query'])) { $url['query'] = ""; }


    $encoded = "";

    while (list($k,$v) = each($data))
    {
        $encoded .= ($encoded ? "&" : "");
        $encoded .= rawurlencode($k)."=".rawurlencode($v);
    }
    $urlHead = null;
    $urlPort = $url['port'];
    if($url['scheme'] == "https")
    {
        $urlHead = "ssl://".$url['host'];
        if($url['port'] == null || $url['port'] == 0)
        {
            $urlPort = 443;
        }
    }
    else
    {
        $urlHead = $url['host'];
        if($url['port'] == null || $url['port'] == 0)
        {
            $urlPort = 80;
        }
    }
    $fp = fsockopen($urlHead, $urlPort);

    if (!$fp) return "Failed to open socket to $url[host]";

    $tmp="";
    $tmp.=sprintf("POST %s%s%s HTTP/1.0\r\n", $url['path'], $url['query'] ? "?" : "", $url['query']);
    $tmp.="Host: $url[host]\r\n";
    $tmp.="Content-type: application/x-www-form-urlencoded\r\n";
    $tmp.="Content-Length: " . strlen($encoded) . "\r\n";
    $tmp.="Connection: close\r\n\r\n";
    $tmp.="$encoded\r\n";

    //RecordLog("YGM","��������".$tmp);

    fputs($fp,$tmp);

    $line = fgets($fp,1024);

    if (!preg_match("#^HTTP/1\.. 200#i", $line))
    {
        $logstr = "MSG".$line;
        //RecordLog("YGM",$logstr);
        return array("FLAG"=>0,"MSG"=>$line);
    }

    $results = ""; $inheader = 1;
    while(!feof($fp))
    {
        $line = fgets($fp,1024);
        if ($inheader && ($line == "\n" || $line == "\r\n"))
        {
            $inheader = 0;
        }
        elseif (!$inheader)
        {
            $results .= $line;
        }
    }
    fclose($fp);
    //RecordLog("YGM","��������".$results);
    return array("FLAG"=>1,"MSG"=>$results);
}
//计算团队人数
function countTeamNumber($id){
	$user = M('member')->where(array('id'=>$id))->find();
	$account = M('member')->where(array('references'=>$user['account']))->count();
	$account1 = M('member')->where(array('second_generation'=>$user['account']))->count();
	$account2 = M('member')->where(array('three_generations'=>$user['account']))->count();
	$count = $account + $account1 + $account2;
	return $count;
}
//团队充值总额
function countTeamRecharge($id){
	$count = 0;
	$count1 = 0;
	$count2 = 0;
	
	$user = M('member')->where(array('id'=>$id))->find();
	$account1 = M('member')->where(array('references'=>$user['account']))->getField('account',true);
	$account1s = implode(',',$account1);
	
	$account2 = M('member')->where(array('second_generation'=>$user['account']))->getField('account',true);
	$account2s = implode(',',$account2);
	
	$account3 = M('member')->where(array('three_generations'=>$user['account']))->getField('account',true);
	$account3s = implode(',',$account3);
	
	if(count($account1s) >= 1){
		$count = M('member_cash_log')->where(array('touch_member'=>array('in',$account1s)))->sum('prize');
	}
	
	if(count($account2s) >= 1){
		$count1 = M('member_cash_log')->where(array('touch_member'=>array('in',$account2s)))->sum('prize');
	}
	
	if(count($account3s) >= 1){
		$count2 = M('member_cash_log')->where(array('touch_member'=>array('in',$account3s)))->sum('prize');
	}
	
	return $count + $count1 + $count2;
}
