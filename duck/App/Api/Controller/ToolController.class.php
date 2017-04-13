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
       // $_POST['token']='cede49a52f40707828c097b59801dbed';
        $this->checkGet(array('token'));
        $list = M('market_price')->where(array('start'=>array('elt',date('Y-m-d'))))->order('start desc')->limit(7)->select();
        $this->userId = $this->tokenToUserId(I('post.token'));
        $membermsg=M('member m ')->join(' left join '.C('DB_PREFIX').'dogmsg c on m.dog_lev=c.lev')->field('m.enclosure_lev,c.rate dog_rate')->where('m.id='.$this->userId)->find();
        $enclosure[4]=0;
        $enclosure[0]=C('enclosure_rate_zero')?C('enclosure_rate_zero'):0.4; //围栏利率 
        $enclosure[1]=C('enclosure_rate_one')?C('enclosure_rate_one'):0.3;
        $enclosure[2]=C('enclosure_rate_two')?C('enclosure_rate_two'):0.2;
        $enclosure[3]=C('enclosure_rate_three')?C('enclosure_rate_three'):0.1;
        $dog_rate=$membermsg['dog_rate'] ;
      
        foreach ($list as $k => $v){
            $date[] = date('m-d',strtotime($v['start']));
            $price[] = $v['price'];
            $bprice=$v['price']-$enclosure[$membermsg['enclosure_lev']]+$dog_rate;
           // echo $bprice,'</br>';
          //  echo number_format($bprice,3, ".", ","),'</br>';
           //  echo sprintf("%.2f",substr(sprintf("%.3f", $bprice), 0, -2))  ,'</br>';
            $baseprice[]=$bprice;
        } 
        $date = array_reverse($date);
        $price = array_reverse($price);
        $baseprice = array_reverse($baseprice);
        $this->ajaxReturn(array('errcode'=>10000,'date'=>$date,'price'=>$price,'baseprice'=>$baseprice));
    }

    public function getCode($mobile){
        if(!$mobile) return false;
        $code = mt_rand(111111,666666);
        $expirationTime = time() + 30 * 60;
        $content = $code;
          
        $content = "您正在重设密码，验证码：{$code}，TEC提醒您注意账号安全【天鹅城】";
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