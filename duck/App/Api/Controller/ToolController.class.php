<?php
/**
 * Created by PhpStorm.
 * User: jiaruo
 * Date: 17/2/15
 * Time: 下午3:48
 */

namespace Api\Controller;


class ToolController extends CommonController
{
    public function sendCode(){
        if(IS_POST){ 
            $data = I('post.');

            $this->checkGet(array('mobile','yzcode')); 
             
            if(!check_verify($data['yzcode'])){
               $this->echoJson('验证码错误!');
            }
              
            $result = $this->getCode($data['mobile']);
            if(!$this->isFalse($result)){
                $this->echoJson(10000);
            }
            $this->echoJson(-8);
        }
    }
    public function getPrice(){
        $list = M('market_price')->where(array('start'=>array('elt',date('Y-m-d'))))->order('start desc')->limit(7)->select();
        foreach ($list as $k => $v){
            $date[] = date('m-d',strtotime($v['start']));
            $price[] = $v['price'];
        }
        $date = array_reverse($date);
        $price = array_reverse($price);
        $this->ajaxReturn(array('errcode'=>10000,'date'=>$date,'price'=>$price));
    }

    public function getCode($mobile){
        if(!$mobile) return false;
        $code = mt_rand(111111,666666);
        $expirationTime = time() + 30 * 60;
        $content = $code;
         
        $content = "您好，您的验证码是{$code}打死不要告诉其他人【富贵鸡】";
        $url='http://utf8.sms.webchinese.cn/'; 
        $rparams['Uid']='tianchenxue';
        $rparams['Key']='608c3096b11003826580';
        $rparams['smsMob']=$mobile;
        $rparams['smsText']=$content;
        $result=$this->file_get_request($url,$rparams);
      
        if($result != 1){
            $this->echoJson(-21);
        }
        $id = M('sms_log')->add(array(
            'mobile'    =>  $mobile,
            'code'      =>  $code,
            'content'   =>  $content,
            'expiration_time'   =>  $expirationTime,
        ));
        return $id;
    }
    private function file_get_request($url,$postFields)
    {
        $post='';
        while (list($k,$v) = each($postFields)) 
        {
            $post .= rawurlencode($k)."=".rawurlencode($v)."&"; //×ªURL±ê×¼Âë
        }
        return file_get_contents($url.'?'.$post);
    }
}