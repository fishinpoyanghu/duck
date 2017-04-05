<?php
/**
 * Created by PhpStorm.
 * User: jiaruo
 * Date: 17/2/15
 * Time: 下午1:59
 */

namespace Api\Controller;

use Think\Controller;

class CommonController extends Controller{
    public function _initialize(){
        header("Content type:text/html;charset=utf-8"); 
        if(S('sysclose')){
            $this->echoJson(-10000);
        }

    }
    /**
     * 数组格式化后输出json.
     */
    public function echoJson($data){
        $this->ajaxReturn(echoJson($data));
    }
    /**
     * 分页
     * @return mixed
     */
    public function getSize(){
        $this->checkGet(array('page','pageSize'));
        $page = $this->getParams('page')?$this->getParams('page'):1;
        $pageSize = $this->getParams('pageSize');
        $x = ($page-1) * $pageSize;
        $y =$pageSize;
        $data['x'] = (int)$x;
        $data['y'] = (int)$y;
        return $data;
    }
    /**
     * 检查必传参数是否为空.
     */
    public function checkGet($data){
        if (is_array($data)) {
            foreach ($data as $v) {
                $value = I($v);
                if ($value == null && $value !== 0) {
                    $this->echoJson('缺少字段'.$v);
                }
            }
        }
        if (is_string($data) && I($data) == null && I($data) !== 0) {
            $this->echoJson('缺少字段'.$data);
        };
    }
    /**
     * 把token转成用户id
     *
     * @param  [type] $token [用户token]
     * @return [type]        [description]
     */
    public function tokenToUserId($token){
        if(!$token) $this->echoJson(-9);
        $UID = M('member')->where(array('token'=>$token))->getField('id');
        if(!$UID) $this->echoJson(-9);
        return $UID;
    }
    /**
     * 检测参数是否为False
     */
    public function isFalse($data){
        if(empty($data) || !$data || !isset($data)){
            return true;
        }
        return false;
    }
    /**
     * 发送验证码
     */
    public function getCode($mobile){
        if(!$mobile) return false;
        $code = mt_rand(111111,666666);
        $expirationTime = time() + 30 * 60;
        $content = $code;
		$sms = A('Sms');
		$content = "您好，您的验证码是{$code}打死不要告诉其他人【富贵鸡】";
		$result = $sms->sendAll($mobile,$content);
		//echo $result['stat'];
		if($result['stat'] != 100){
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
    /**
     * 验证验证码
     */
    public function verfiyCode($mobile,$code){
        if(!$mobile) $this->echoJson('-6');
        $result = M('sms_log')->where(array('mobile'=>$mobile))->order('id desc')->find();
//        echo M('sms_log')->_sql();
        if($result && ($result['expiration_time'] > time()) && $result['code'] == $code){
            return true;
        }
        $this->echoJson('-6');
    }
    public function getUser($userId){
        $user = M('member')->find($userId);
        if(!$user){
            $this->echoJson(-9);
        }
        return $user;
    }
	



}