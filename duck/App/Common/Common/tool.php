<?php
/**
 *
 * User:十八
 * QQ：274020083
 * Date: 16/7/20
 * Time: 上午3:04
 * 后台的一些方法
 *
 */



//会员诚信积分等级
function member_points($member_id){

   $wallet = M('member_cash')->where("mid=$member_id")->field('points_wallet')->find();
    $points = 0;
   $points_wallet =  $wallet['points_wallet'];

   $points_wallet = intval($points_wallet);

   if($points_wallet >1 && $points_wallet <5){

       $points = 1;

   }

    if($points_wallet >4 && $points_wallet <10){

        $points = 2;

    }

    if($points_wallet >9 && $points_wallet <20){

        $points = 3;

    }
    if($points_wallet >19 && $points_wallet <50){

        $points = 4;

    }

    if($points_wallet >49){

        $points = 5;

    }

        $ret = $points*2;
        return $ret.'0%';

}

//会员诚信积分等级
function member_points_account($account){

    $member_id = member_id($account);

    $wallet = M('member_cash')->where("mid=$member_id")->field('points_wallet')->find();
    $points = 0;
    $points_wallet =  $wallet['points_wallet'];

    $points_wallet = intval($points_wallet);

    if($points_wallet >1 && $points_wallet <5){

        $points = 1;

    }

    if($points_wallet >4 && $points_wallet <10){

        $points = 2;

    }

    if($points_wallet >9 && $points_wallet <20){

        $points = 3;

    }
    if($points_wallet >19 && $points_wallet <50){

        $points = 4;

    }

    if($points_wallet >49){

        $points = 5;

    }

    $ret = $points*2;
    return $ret.'0%';

}



//登陆日志

function admin_log($user,$content){


    $data['user'] = $user;
    $data['content'] = $content;
    $data['date'] = NOW_TIME;
    $data['ip'] = get_client_ip();
    $ret =  M('tool_admin_log')->add($data);
    return $ret;

}



//返回用户的名字

function member_realname($account){

    $member =  M('member')->where(array('account'=>$account))->field('realname')->find();
    return $member['realname'];

}

function arr_to_xml($arr, $dom = 0, $item = 0) {
    if (! $dom) {
        $dom = new DOMDocument ("1.0","UTF-8");
    }
    
    if (! $item) {
        $ccc = array_keys ( $arr );
        if ($ccc [0] == 'envelope') {
            $str_head = 'request';
        } else {
            $str_head = 'envelope';
        }
        $item = $dom->createElement ( $str_head );
        $dom->appendChild ( $item );
    }
    foreach ( $arr as $key => $val ) {      
        $itemx = $dom->createElement ( is_string ( $key ) ? $key : "record" );
        $item->appendChild ( $itemx );
        if (! is_array ( $val )) {
            $text = $dom->createTextNode ( $val );
            $itemx->appendChild ( $text );
        } else {
            arr_to_xml ( $val, $dom, $itemx );
        }
    }
    
    return $dom->saveXML ();
}

function xml_to_array($xml) {
    $reg = "/<(\\w+)[^>]*?>([\\x00-\\xFF]*?)<\\/\\1>/";
    if (preg_match_all ( $reg, $xml, $matches )) {
        $count = count ( $matches [0] );
        $arr = array ();
        for($i = 0; $i < $count; $i ++) {
            
            $key = $matches [1] [$i];   
            
            $val = xml_to_array ( $matches [2] [$i] ); // 递归
            if (array_key_exists ( $key, $arr )) {
                if (is_array ( $arr [$key] )) {
                    if (! array_key_exists ( 0, $arr [$key] )) {
                        $arr [$key] = array (
                                $arr [$key] 
                        );
                    }
                } else {
                    
                    $arr [$key] = array (
                            $arr [$key] 
                    );
                }
                $arr [$key] [] = $val;
            } else {
                
                $arr [$key] = $val;
            }
        }
        return $arr;
    } else {
        return $xml;
    }
}
/**
 * 
 * @param  $str  //签名的原串
 * @param  $privateFile //私钥证书路径
 * @param  $password  //私钥密码
 * @return base编码后的签名串
 */
function private_sign($str,$privateFile,$password){ 
    $str=md5($str);
    $str=pack("H*",$str);
    $pkcs12certdata = file_get_contents ( $privateFile );
    openssl_pkcs12_read( $pkcs12certdata, $certs, $password);
    $priv_key_id=openssl_get_privatekey($certs['pkey']);
    openssl_sign($str, $crypted, $priv_key_id, OPENSSL_ALGO_SHA1);
    $crypted=base64_encode($crypted);
    return $crypted;
}



/**
 * 
 * @param  $str_xml
 * @param  $publicFile
 * @param  $crypted
 * @return true验签成功,false验签不成功
 */
function public_verify($str,$publicFile,$crypted){
    
    $str=md5($str);
    $str=pack("H*",$str);   //转成16进制的字符串
    $binary_signature=base64_decode($crypted); //加密
    $pkcs12certdata = file_get_contents ( $publicFile );//以字符串的形式获取文件中的信息
    $public_key=openssl_get_publickey($pkcs12certdata);//获取公钥
    $ok = openssl_verify($str, $binary_signature, $public_key,OPENSSL_ALGO_SHA1); //1.数据，2.签名，3公钥，4
    return $binary_signature;
}



  

  
  
  
/*  
* Some constants  
*/  
  
  
define("BCCOMP_LARGER", 1);   
  
 