<?php
/**
 *
 * User: 十八
 * QQ  : 274020083
 * Date: 2016/6/13 0013
 * Time: 上午 11:04
 */
namespace Com\SMS;

class SendSMS {


    private $uid;
    private $pass;
    private $mobile;
    private $content;


    public function __construct($uid,$pass,$mobile = '', $content = ''){

        $this->uid = $uid;
        $this->pass = $pass;
        $this->mobile = $mobile;
        $this->content = $content;

    }


    //选择供应商
    public function send($market=1){

        if($market == 1){

           $one =  $this->one($this->uid,$this->pass,$this->mobile,$this->content);
           return $one;
        }

    }




    public function one($uid,$pwd,$mobile,$content,$template=''){

        $apiUrl = 'http://api.sms.cn/sms/utf8';		//短信接口地址
        $data = array(
            'ac' =>		'send',
            'uid'=>		$uid,					//用户账号
            'pwd'=>		md5($pwd.$uid),			//MD5位32密码,密码和用户名拼接字符
            'mobile'=>	$mobile,				//号码
            'content'=>	$content,			//内容
            'template'=>$template,				//变量模板ID 全文模板不用填写
            'format' => 'json',					//接口返回信息格式 json\xml\txt
        );

        $result = $this->postSMS($apiUrl,$data);			//POST方式提交
        $re = $this->json_to_array($result);			    //JSON数据转为数组
        //$re = getSMS($apiUrl,$data);				//GET方式提交

        return $re;
    }


    private function postSMS($url,$data='')
    {
        $row = parse_url($url);
        $host = $row['host'];
        $port = $row['port'] ? $row['port']:80;
        $file = $row['path'];
        while (list($k,$v) = each($data))
        {
            $post .= rawurlencode($k)."=".rawurlencode($v)."&";	//转URL标准码
        }
        $post = substr( $post , 0 , -1 );
        $len = strlen($post);
        $fp = @fsockopen( $host ,$port, $errno, $errstr, 10);
        if (!$fp) {
            return "$errstr ($errno)\n";
        } else {
            $receive = '';
            $out = "POST $file HTTP/1.1\r\n";
            $out .= "Host: $host\r\n";
            $out .= "Content-type: application/x-www-form-urlencoded\r\n";
            $out .= "Connection: Close\r\n";
            $out .= "Content-Length: $len\r\n\r\n";
            $out .= $post;
            fwrite($fp, $out);
            while (!feof($fp)) {
                $receive .= fgets($fp, 128);
            }
            fclose($fp);
            $receive = explode("\r\n\r\n",$receive);
            unset($receive[0]);
            return implode("",$receive);
        }
    }

    //把数组转json字符串
    protected function array_to_json($p)
    {
        return urldecode(json_encode($this->json_urlencode($p)));
    }
    //url转码
    protected function json_urlencode($p)
    {
        if( is_array($p) )
        {
            foreach( $p as $key => $value )$p[$key] = $this->json_urlencode($value);
        }
        else
        {
            $p = urlencode($p);
        }
        return $p;
    }

    //把json字符串转数组
    protected  function json_to_array($p)
    {
        if( mb_detect_encoding($p,array('ASCII','UTF-8','GB2312','GBK')) != 'UTF-8' )
        {
            $p = iconv('GBK','UTF-8',$p);
        }
        return json_decode($p, true);
    }
}