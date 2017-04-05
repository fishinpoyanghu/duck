<?php
/**
 *首页控制器
 * User:十八
 * QQ：274020083
 * Date: 16/7/27
 * Time: 下午4:12
 */

namespace Home\Controller;

use Think\Controller;

class ReturnController extends Controller
{
    public function getReturnAliPay(){
		$content = file_get_contents('php://input');
		RecordLog("YGM","收到后台通知");

		RecordLog("YGM","报文:".$content);
		
		$requestData = parseRecv($content);
		RecordLog("YGM",json_encode($requestData));
		$sign_type = $requestData["signType"]; 

		$serverSign =$requestData["serverSign"];  

		$serverCert = $requestData["serverCert"]; 

		$orderId = $requestData['orderId'];
        ksort($requestData);

		$source="";

        foreach($requestData as $key => $value)  {
			if($value != '' && $key!="serverCert" && $key!="serverSign"){
				$source = $source.$key.'='.$value.'&';
			}
        }		

		

		$signKey = $GLOBALS['signKey'];

        $verify= false;

        if($sign_type == "RSA"){

		  $source= substr($source,0,strlen($source)-1);
		  RecordLog("YGM","签名串1:".$source);
		  $verify = rsaVerify($source,$serverCert,$serverCert);
		  RecordLog("YGM","rsaVerify");

        }else{

          $source=$source.'key='.$signKey;

          $sign = strtoupper(md5($source));

          if($sign == $serverSign){

            $verify = true;

          }

        }

        //if($verify){
			$log = M('member_cash_log')->where(array('orderid'=>$orderId,'zhuangtai'=>2))->find();
			if($log){
				M('member_cash_log')->where(array('orderid'=>$orderId,'zhuangtai'=>2))->setField('zhuangtai',1);
				M('member')->where(array('account'=>$log['touch_member']))->setInc('money',$log['prize']);
				RecordLog("YGM","验签成功"); 
				echo "success";
				exit();
			}
           

        //}else{
			echo "fail";
          RecordLog("YGM","验签失败");

        //}

	}
	public function getReturnWechat(){
		$content = file_get_contents('php://input');

        $sign_type     = "RSA";
        $signKey      = 123456;

        //RecordLog("YGM","收到后台通知");
        //RecordLog("YGM",$content);
        $recv = parseRecv($content);
		$order = $recv["ppd_mord_no"];
        /*RecordLog("YGM","ord_sts".$recv["ord_sts"]);
        RecordLog("YGM","ppd_mord_no".$recv["ppd_mord_no"]);
        RecordLog("YGM","cert".$recv["cert"]);
        RecordLog("YGM","sign".$recv["sign"]);*/
		$orderId = $recv["ppd_mord_no"];
        $source = "ord_sts=".$recv["ord_sts"]."&ppd_mord_no=".$recv["ppd_mord_no"];
        //RecordLog("YGM","source:".$source);
        $verify= false;
        if($sign_type == "RSA"){
            $verify = rsaVerify($source,$recv["sign"],$recv["cert"]);
        }else{
            $signData=$source.'&key='.$signKey;
            $sign = strtoupper(md5(utf8_encode($signData)));
            if($sign == $recv["sign"]){
                $verify = true;
            }
        }
        if($verify){
			$log = M('member_cash_log')->where(array('orderid'=>$orderId,'zhuangtai'=>2))->find();
			if($log){
				$id = M('member_cash_log')->where(array('orderid'=>$orderId,'zhuangtai'=>2))->setField('zhuangtai',1);
				$ids = M('member')->where(array('account'=>$log['touch_member']))->setInc('money',$log['prize']);
				//RecordLog("YGM","验签成功"); 
				if($id && $ids){
					echo "success";
				}
				
				exit();
			}
            RecordLog("YGM","验签成功");
        }else{
            //RecordLog("YGM","验签失败");
        }
	}
	public function coPayReturn(){
		$content = file_get_contents('php://input');
		RecordLog("YGM","收到后台通知");
		RecordLog("YGM","报文:".$content);
		//$requestData = parseRecv($content);
		$requestData = json_decode($content,true);
		$sign_type = $requestData["signtype"];
		$serverSign =$requestData["serverSign"];
		$serverCert = $requestData["serverCert"];
		$code = $requestData['resCode'];
		$msg= urlencode(iconv('GB2312','UTF-8',$requestData['retMsg']));
		$orderid = $requestData['ord_no'];
		//$requestData = json_decode($requestData,true);
        ksort($requestData);
		$source = "";
        foreach($requestData as $key => $value){
          if($value != '' && $key!="serverCert" && $key!="serverSign"){
              $source = $source.$key.'='.$value.'&';
          }
        }
		
		$verify= false;
		
		RecordLog("YGM","RSA");
		$source= substr($source,0,strlen($source)-1);
		RecordLog("YGM","签名串:".$source);
		$verify = rsaVerify($source,$serverCert,$serverCert);
		
		//if($verify){
		if($code != "000000"){
			$log = M('member_cash_log')->where(array('orderid'=>$orderid,'zhuangtai'=>3))->find();
			if($log){
				$id = M('member_cash_log')->where(array('orderid'=>$orderid,'zhuangtai'=>3))->setField('zhuangtai',1);
				if($id){
					RecordLog("YGM","success");
					echo "success";
					exit;
				}
			}
			
        }else{
		  $id = M('member_cash_log')->where(array('orderid'=>$orderid,'zhuangtai'=>3))->setField('msg',$msg);
		  echo "error";
		  exit;
        }
	}
}