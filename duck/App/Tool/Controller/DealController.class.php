<?php
 
namespace Tool\Controller;

class DealController extends CommonController{



    //金币挂卖
    public function gold(){


        $list = M('gold_list')->where('status=1')->order('id desc')->select();
        $this->assign('list',$list);
        $this->display();
    }

       
    public function gold_pass(){


        if(IS_POST){

            $id = I('post.id');

            $list =  M('gold_list')->where(array('id'=>$id))->find();


            //用户
            $member = $list['account'];
            $member =  M('member')->where(array('account'=>$member))->find();
            //金钱

            $count_wallet = member_cash($member['id']);
            $count_wallet = $count_wallet['count_wallet'];

            if($count_wallet < 2000){

                $before = member_cash($member['id']);
                $before = $before['baoli_wallet'];
                $money = $list['money'];

                M('member_cash')->where(array('mid'=>$member['id']))->setInc('baoli_wallet',$money);

                $after = member_cash($member['id']);
                $after = $after['baoli_wallet'];

                add_log($before,+$money,$after,$member['account'],$member['account'],1,'金币挂卖',0);

            }

            if($count_wallet >= 2000){

                $money = $list['money'];
                $before = member_cash($member['id']);
                $baoli_before = $before['baoli_wallet'];
                $baotong_before = $before['baotong_wallet'];
                $gongyi_before = $before['gongyi_wallet'];

                M('member_cash')->where(array('mid'=>$member['id']))->setInc('baoli_wallet',$money*0.5);
                M('member_cash')->where(array('mid'=>$member['id']))->setInc('baotong_wallet',$money*0.4);
                M('member_cash')->where(array('mid'=>$member['id']))->setInc('gongyi_wallet',$money*0.1);

                $after = member_cash($member['id']);
                $baoli_after = $before['baoli_wallet'];
                $baotong_after = $before['baotong_wallet'];
                $gongyi_after = $before['gongyi_wallet'];

                add_log($baoli_before,+$money*0.5,$baoli_after,$member['account'],$member['account'],1,'金币挂卖',0);
                add_log($baotong_before,+$money*0.4,$baotong_after,$member['account'],$member['account'],1,'金币挂卖',1);
                add_log($gongyi_before,+$money*0.1,$gongyi_after,$member['account'],$member['account'],1,'金币挂卖',2);

            }


            M('gold_list')->where(array('id'=>$id))->setField('status',1);

            //累积挂卖金币总数

            M('member_cash')->where(array('mid'=>$member['id']))->setInc('count_wallet',$list['money']);

            $this->success('交易成功');

        }

    }

    public function gold_no_pass(){


        $id = I('post.id');

        $list =  M('gold_list')->where(array('id'=>$id))->find();


        //用户
        $member = $list['account'];
        $member =  M('member')->where(array('account'=>$member))->find();
        //金钱
        $before = member_cash($member['id']);
        $before = $before['gold_wallet'];
        $money = $list['money'];
        M('member_cash')->where(array('mid'=>$member['id']))->setInc('gold_wallet',$money);
        $after = member_cash($member['id']);
        $after = $after['gold_wallet'];
        add_log($before,+$money,$after,$member['account'],$member['account'],1,'金币挂卖失败',3);

        M('gold_list')->where(array('id'=>$id))->setField('status',2);

        $this->success('操作成功');

    }

    //提现管理

    public function baoli(){

		$list = M('baoli_list as baoli')->field('baoli.*,member.bank_name,member.bank_code,member.bank_address,member.realname')->join('inner join gs_member as member on member.account = baoli.account')->select();

       // $list = M('baoli_list as baoli')->join('inner join gs_member as member on member.account = baoli.account')->select();
       // print_r($list);
        $this->assign('list',$list);
        $this->display();

    }

     function tixian1(){
           $list = M('baoli_list')->where(array('status'=>'0'))->select();
            $this->assign('list',$list);
            $this->display();
        }
	
    function tixian2(){
             $list = M('baoli_list')->where(array('status'=>'1'))->select();
            $this->assign('list',$list);
            $this->display();
      }
	function jihuo(){
		if(IS_POST){
			$data = I();
			M('member')->where(array('account'=>$data['account']))->save(array('active'=>1,'status'=>0));
		}
	}

    
	 

    public function baoli_no_pass(){


        $id = I('post.id');

        $list =  M('baoli_list')->where(array('id'=>$id))->find();
        if($list['type'] == "jiangli"){
            $wallet = "reward_wallet";
        }elseif($list['type'] == "licai"){
            $wallet = "financial_wallet";
        }

        //用户
        $member = $list['account'];
        $member =  M('member')->where(array('account'=>$member))->find();
        //金钱
        $before = member_cash($member['id']);
        $before = $before[$wallet];
        $money = $list['money'];
        M('member_cash')->where(array('mid'=>$member['id']))->setInc($wallet,$money);
        $after = member_cash($member['id']);
        $after = $after[$wallet];
        add_log($before,+$money,$after,$member['account'],$member['account'],1,'拒绝提现',0);

        M('baoli_list')->where(array('id'=>$id))->setField('status',3);
        // echo M('baoli_list')->getLastSql();
        $this->success('操作成功');

    }


    //充值

    public function chongzhi(){


        $list = M('chongzhi')->where('status=0')->select();
        $this->assign('list',$list);
        $this->display();

    }


    public function chongzhi_pass(){


        if(IS_POST){

            $id = I('post.id');

            $list =  M('chongzhi')->where(array('id'=>$id))->find();


            //用户
            $member = $list['account'];
            $member =  M('member')->where(array('account'=>$member))->find();
            //金钱

            if($list['wallet'] == 'gold_wallet'){

                $before = member_cash($member['id']);
                $before = $before['gold_wallet'];
                $money = $list['money'];
                M('member_cash')->where(array('mid'=>$member['id']))->setInc('gold_wallet',$money);
                $after = member_cash($member['id']);
                $after = $after['gold_wallet'];
                add_log($before,+$money,$after,$member['account'],$member['account'],1,'金币充值',3);
            }

            if($list['wallet'] == 'baoli_wallet'){

                $before = member_cash($member['id']);
                $before = $before['baoli_wallet'];
                $money = $list['money'];
                M('member_cash')->where(array('mid'=>$member['id']))->setInc('baoli_wallet',$money);
                $after = member_cash($member['id']);
                $after = $after['baoli_wallet'];
                add_log($before,+$money,$after,$member['account'],$member['account'],1,'宝利币充值',0);
            }

            M('chongzhi')->where(array('id'=>$id))->setField('status',1);

            $this->success('交易成功');

        }

    }

    public function chongzhi_no_pass(){


        if(IS_POST){

            $id = I('post.id');


            M('chongzhi')->where(array('id'=>$id))->setField('status',2);

            $this->success('操作成功');

        }

    }


    //分红管理

    public function fenhong(){

        //当前总共宝丰币
        $baofeng_sum = M('member_cash')->where()->sum('baofeng_wallet');

        if(IS_POST){

            $money = I('post.money');
            $wallet = I('post.wallet');

            if($wallet == ''){

                die("<script>alert('选择一个钱包！');history.back(-1);</script>");
            }

            $this->member_get($baofeng_sum,$wallet,$money);
            $this->success('分红成功');
        }

        $this->assign('baofeng_sum',$baofeng_sum);
        $this->display();
    }

	public function baoli_pass($id){
		if( $id >= 1){
			$log = M('member_cash_log')->where(array('id'=>$id,'zhuangtai'=>3))->find();
			if(!$log){
				return false;
			}
			$charset = "02";
			$signtype = "RSA";
			$back_url = "http://fgj.xysya.top/index.php/Return/coPayReturn";
			$merc_id = "888021056210001";
			$ord_no = $orderId = mt_rand(1111,9999).time().mt_rand(1111,9999);
			$amount = $log['prize'];
			$bank_name = $log['drawing_name'];
			$bank_no = $log['drawing_code'];
			$pp_type = 0;

			$requestData = array();
			$requestData['charset'] = $charset;
			$requestData['signtype'] = $signtype;
			$requestData['back_url'] = $back_url;
			$requestData['merc_id'] = $merc_id;
			$requestData['amount'] = $amount;
			$requestData['ord_no'] = $ord_no;
			$requestData['bank_name'] = $bank_name;
			$requestData['bank_no'] = $bank_no;
			$requestData['pp_type'] = $pp_type;
			ksort($requestData);
			$signData = "";
			foreach($requestData as $key => $value)  {
				if( $value != '' || $value === 0){
					$signData = $signData.$key.'='.$value.'&';
				}
			}
			

			$merchantSign = "";
			$merchantCert ="";
			$certPath    = "D:/phpStudy/WWW/cert/";
			$certPass    = 749135;
			$signData= substr($signData,0,strlen($signData)-1);
			//echo $signData.'<br>';
			$certPaths = $certPath."888021056210001.p12";
			$rsaSignData =$this->rsaSign($certPaths,$certPass,$signData);
			$merchantCert = $rsaSignData['merchantCert'];
			$merchantSign = $rsaSignData['merchantSign'] ;
			
			reset($requestData);
			$requestData["merchantcert"]      = $merchantCert;
			$requestData["merchantsign"]      = $merchantSign;
				
			//print_r($requestData);die;
			$sTotalString = POSTDATA("https://oic.mfe88.net/mrpos/ljdfjk/2035204.do",$requestData);
			$recv = $sTotalString["MSG"];
			$responseData = json_decode($recv,true);
			$status = $responseData["respType"];
			//var_dump($responseData);
			if($responseData['resCode'] == 'F' ){
				return $responseData['resMsg'];
			}
			M('member_cash_log')->where(array('id'=>$id))->setField('orderid',$orderId);
			echo json_encode(array('msg'=>"成功提交银行处理",'errcode'=>1));die;
		}

    }
	public function rsaSign($certPaths,$certPass,$signData)
    {
        $rtValue = array();
        $pkcs12 = file_get_contents($certPaths);
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
    private function member_get($baofeng_sum,$wallet,$money){


        $member =  M('member_cash')->where()->select(); //所有会员

        if($wallet == 'baoli_wallet'){

            $wallet_type = 0;
        }

        if($wallet == 'baotong_wallet'){

            $wallet_type = 1;
        }

        if($wallet == 'gongyi_wallet'){

            $wallet_type = 2;
        }

        if($wallet == 'gold_wallet'){

            $wallet_type = 3;
        }

        if($wallet == 'baofeng_wallet'){

            $wallet_type = 4;
        }
        foreach($member as $k=>$item){

            $bili = $item['baofeng_wallet']/$baofeng_sum;

            $before = $item[$wallet];
            M('member_cash')->where(array('mid'=>$item['mid']))->setInc($wallet,$money*$bili);

            $member = M('member')->where(array('id'=>$item['mid']))->find();
            add_log($before,$money*$bili,$before+$money*$bili,$member['account'],$member['account'],1,'系统分红',$wallet_type);
        }
	}
    
	
}
