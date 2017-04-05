<?php
/**
 * 前台方法
 * User:十八
 * QQ：274020083
 * Date: 16/7/27
 * Time: 下午4:03
 */





//用户钱包
function member_cash($mid){

    return M('member')->field('money,currency,action_code,feed')->where(array('id'=>$mid))->find();
}

//初始化钱包

function init_cash($mid){
    $cash = M('member_cash');
    $ret = $cash->where(array('mid'=>$mid))->find();
    if($ret){
        return 0 ;
    }
    //$c = M('system_config')->where(array('id'=>1))->field('init_cash_wallet,init_prize_wallet,init_ticket_wallet')->find();
    $data['mid'] =$mid;
    $data['baoli_wallet']  = 0;
    $data['baotong_wallet'] = 0;
    $data['gongyi_wallet'] = 0;
    $data['gold_wallet'] = 0;
    $data['baofeng_wallet'] = 0;
    $data['financial_wallet'] = 0;
    $data['reward_wallet'] = 0;
    $cash->data($data)->add();
}


/**
 * 给用户头衔
 * @param $lev
 * @return mixed
 */

function make_level($lev){

    $c = M('system_config')->where(array('id'=>1))->find();
    $s = $c['rank_name'];

    $arry =array_filter(explode('|',$s));
    if(empty($arry[$lev]))
    {
        // return '未设置';
        return end($arry);
    }
    return $arry[$lev];
}

//金币明细

function add_log($before_prize,$prize,$after_prize,$touch_member,$recep_member,$status,$content,$wallet){


    $data['before_prize'] =$before_prize;
    $data['prize'] =$prize;
    $data['after_prize'] =$after_prize;
    $data['touch_member'] =$touch_member;
    $data['recep_member'] =$recep_member;
    $data['status'] =$status;
    $data['content'] =$content;
    $data['wallet'] =$wallet;
    $data['date'] =NOW_TIME;

  $id = M('member_cash_log')->data($data)->add();
  // file_put_contents('./ceshi.txt', M('member_cash_log')->getLastSql(),FILE_APPEND);
  return $id;

}
//获取个人的团队路径
function getPath($id){
    $path = M('member')->where(array('id'=>$id))->getField('path');
    $path = $path ? $path : $id;
    return $path;
}
function https_request($url, $data = null)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    if (!empty($data)){
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($curl);
    curl_close($curl);
    return $output;
}
function postCurl($uri,$data){
    $ch = curl_init ();
    // print_r($ch);
    curl_setopt ( $ch, CURLOPT_URL, $uri);
    curl_setopt ( $ch, CURLOPT_POST, 1 );
    curl_setopt ( $ch, CURLOPT_HEADER, 0 );
    curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
    $return = curl_exec ( $ch );
    curl_close ( $ch );
    return $return;
}
function rsa_encrypt($message, $public_key, $modulus, $keylength)   
{   
    $padded = add_PKCS1_padding($message, true, $keylength / 8);   
    $number = binary_to_number($padded);   
    $encrypted = pow_mod($number, $public_key, $modulus);   
    $result = number_to_binary($encrypted, $keylength / 8);   
    return $result;   
  
}   

/**
 * 
 * @param $message     // $message is expected to be binary data 
 * @param $private_key // $modulus should be numbers in (decimal) string format
 * @param $modulus     // $modulus should be numbers in (decimal) string format
 * @param $keylength   // int
 */
function rsa_decrypt($message, $private_key, $modulus, $keylength)   
{   
    $number = binary_to_number($message);   
    $decrypted = pow_mod($number, $private_key, $modulus);   
    $result = number_to_binary($decrypted, $keylength / 8);   
    return remove_PKCS1_padding($result, $keylength / 8);   
}   

/**
 * 
 * @param $message     // $message is expected to be binary data 
 * @param $private_key // $modulus should be numbers in (decimal) string format
 * @param $modulus     // $modulus should be numbers in (decimal) string format
 * @param $keylength   // int
 * @param $hash_func   // name of hash function, which will be used during signing
 * @return $result     // signature String in Base64 format
 */
function rsa_sign($message, $private_key, $modulus, $keylength,$hash_func)   
{   
    //only suport sha1 or md5 digest now
    if (!function_exists($hash_func) && (strcmp($hash_func ,'sha1') == 0 || strcmp($hash_func,'md5') == 0))
        return false;
    $mssage_digest_info_hex = $hash_func($message);
    $mssage_digest_info_bin = hexTobin($mssage_digest_info_hex);
    $padded = add_PKCS1_padding($mssage_digest_info_bin, false, $keylength / 8);
    $number = binary_to_number($padded);   
    $signed = pow_mod($number, $private_key, $modulus);   
    $result = base64_encode($signed); 
    return $result;   
}   

/**
 * 
 * @param $message     // $message is expected to be binary data 
 * @param $private_key // $modulus should be numbers in (decimal) string format
 * @param $modulus     // $modulus should be numbers in (decimal) string format
 * @param $keylength   // int
 * @param $hash_func   // name of hash function, which will be used during signing
 * @return boolean     // true or false
 */
function rsa_verify($document, $signature, $public_key, $modulus, $keylength,$hash_func)   
{   
    //only suport sha1 or md5 digest now
    if (!function_exists($hash_func) && (strcmp($hash_func ,'sha1') == 0 || strcmp($hash_func,'md5') == 0))
        return false;
    $document_digest_info = $hash_func($document);
    
    $number    = binary_to_number(base64_decode($signature));   
    $decrypted = pow_mod($number, $public_key, $modulus);   
    $decrypted_bytes    = number_to_binary($decrypted, $keylength / 8);   
    if($hash_func == "sha1" )
    {
        $result = remove_PKCS1_padding_sha1($decrypted_bytes, $keylength / 8);
    }
    else
    {
        $result = remove_PKCS1_padding_md5($decrypted_bytes, $keylength / 8);
    }
    return(hexTobin($document_digest_info) == $result);
}   
  
  
/*  
* Some constants  
*/  
  
  
define("BCCOMP_LARGER", 1);   
  
  
/*  
* The actual implementation.  
* Requires BCMath support in PHP (compile with --enable-bcmath)  
*/  
  
  
//-- 
// Calculate (p ^ q) mod r    
//   
// We need some trickery to [2]:   
// (a) Avoid calculating (p ^ q) before (p ^ q) mod r, because for typical RSA   
// applications, (p ^ q) is going to be _WAY_ too large.   
// (I mean, __WAY__ too large - won't fit in your computer's memory.)   
// (b) Still be reasonably efficient.   
//   
// We assume p, q and r are all positive, and that r is non-zero.   
//   
// Note that the more simple algorithm of multiplying $p by itself $q times, and   
// applying "mod $r" at every step is also valid, but is O($q), whereas this   
// algorithm is O(log $q). Big difference.   
//   
// As far as I can see, the algorithm I use is optimal; there is no redundancy   
// in the calculation of the partial results.    
//--   
  
function pow_mod($p, $q, $r)   
{   
    // Extract powers of 2 from $q   
    $factors = array();   
    $div = $q;   
    $power_of_two = 0;   
    while(bccomp($div, "0") == BCCOMP_LARGER)   
    {   
        $rem = bcmod($div, 2);   
        $div = bcdiv($div, 2);   
  
        if($rem) array_push($factors, $power_of_two);   
        $power_of_two++;   
    }   
  
    // Calculate partial results for each factor, using each partial result as a   
    // starting point for the next. This depends of the factors of two being   
    // generated in increasing order.   
  
    $partial_results = array();   
    $part_res = $p;   
    $idx = 0;   
    
    foreach($factors as $factor)   
    {   
        while($idx < $factor)   
        {   
            $part_res = bcpow($part_res, "2");   
            $part_res = bcmod($part_res, $r);   
            $idx++;   
        }   
        array_push($partial_results, $part_res);   
    }   

    // Calculate final result   
    $result = "1";   
    foreach($partial_results as $part_res)   
    {   
        $result = bcmul($result, $part_res);   
        $result = bcmod($result, $r);   
    }   
    return $result;   
}   
  
//--   
// Function to add padding to a decrypted string   
// We need to know if this is a private or a public key operation [4]   
//--   
  
function add_PKCS1_padding($data, $isPublicKey, $blocksize)   
{   
    $pad_length = $blocksize - 3 - strlen($data);   
    if($isPublicKey)   
    {   
        $block_type = "\x02";   
        $padding = "";   
        for($i = 0; $i < $pad_length; $i++)   
        {   
            $rnd = mt_rand(1, 255);   
            $padding .= chr($rnd);   
        }   
    }   
    else  
    {   
        $block_type = "\x01";   
        $padding = str_repeat("\xFF", $pad_length);   
    }   
  
    return "\x00" . $block_type . $padding . "\x00" . $data;   
}  
 
//--   
// Remove padding from a decrypted string   
// See [4] for more details.   
//--   
  
function remove_PKCS1_padding($data, $blocksize)   
{   
    //assert(strlen($data) == $blocksize);   
    $data = substr($data, 1);   
 
    // We cannot deal with block type 0   
    if($data{0} == '\0')   
        die("Block type 0 not implemented.");   
  
    // Then the block type must be 1 or 2    
    //assert(($data{0} == "\x01") || ($data{0} == "\x02"));   
  
    // Remove the padding   
    $offset = strpos($data, "\0", 1);   
    return substr($data, $offset + 1);   
}   

function remove_PKCS1_padding_sha1($data, $blocksize) {
    $digestinfo = remove_PKCS1_padding($data, $blocksize);
    $digestinfo_length = strlen($digestinfo);
    //sha1 digestinfo length not less than 20
    //assert($digestinfo_length >= 20);

    return substr($digestinfo, $digestinfo_length-20);
}   

function remove_PKCS1_padding_md5($data, $blocksize) {
    $digestinfo = remove_PKCS1_padding($data, $blocksize);
    $digestinfo_length = strlen($digestinfo);
    //md5 digestinfo length not less than 16
    //assert($digestinfo_length >= 16);

    return substr($digestinfo, $digestinfo_length-16);
}
  
//--   
// Convert binary data to a decimal number   
//--   
  
function binary_to_number($data)   
{   
    $base = "256";   
    $radix = "1";   
    $result = "0";   
  
    for($i = strlen($data) - 1; $i >= 0; $i--)   
    {   
        $digit = ord($data{$i});   
        $part_res = bcmul($digit, $radix);   
        $result = bcadd($result, $part_res);   
        $radix = bcmul($radix, $base);   
    }   
    return $result;   
}   
  
//--   
// Convert a number back into binary form   
//--   
function number_to_binary($number, $blocksize)   
{   
    $base = "256";   
    $result = "";   
    $div = $number;   
    while($div > 0)   
    {   
        $mod = bcmod($div, $base);   
        $div = bcdiv($div, $base);   
        $result = chr($mod) . $result;   
    }   
    return str_pad($result, $blocksize, "\x00", STR_PAD_LEFT);   
}   
//
//Convert hexadecimal format data into  binary 
//
function hexTobin($data) {
  $len = strlen($data);
  $newdata='';
  for($i=0;$i<$len;$i+=2) {
      $newdata .= pack("C",hexdec(substr($data,$i,2)));
  }
  return $newdata;
}
 

