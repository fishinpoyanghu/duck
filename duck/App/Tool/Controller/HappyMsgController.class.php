<?php
/**
 *
 * 幸福信后台管理
 * User: 十八
 * QQ:274020083
 * Date: 2016/6/15 0015
 * Time: 下午 4:40
 */

namespace Tool\Controller;

class HappyMsgController extends CommonController{



    public function index(){


        $list = M('happy_msg')->where(array('status'=>0))->select(); //未处理
        $this->assign('list',$list);
        $this->display();
    }

    public function success(){


        $map['status'] = array(array('eq',1),array('eq',2),'or');
        $list = M('happy_msg')->where($map)->select(); // 已处理的
        $this->assign('list',$list);
        $this->display();
    }

    //审核

    public function edit(){

        if(IS_POST){
            $id = I('post.id');
            $status = I('post.status');
            $ret =   M('happy_msg')->where(array('id'=>$id))->setField('status',$status);
            if($ret){

                die("<script>alert('修改成功！');history.back(-1);</script>");
            }else{

                die("<script>alert('修改失败！');history.back(-1);</script>");
            }
        }
         $get = I('get.');
        $happy_msg =  M('happy_msg')->where(array('id'=>$get['id']))->find();
        $this->assign('msg',$happy_msg);
        $this->display();
    }

    //通过

    public function pass(){


        $id = I('post.id');
        $money = I('post.prize');

        $msg =   M('happy_msg')->where(array('id'=>$id,'status'=>0))->find();
        if(!$msg){

            die("<script>alert('修改失败！');history.back(-1);</script>");
        }

     $ret =    M('happy_msg')->where(array('id'=>$id))->setField('status',1); //通过
        //todo 通过后的奖励

        $log_map['before_prize'] = member_prize_wallet(member_id($msg['account'])); //加钱之前的金额
        add_cash(member_id($msg['account']),$money);
        $log_map['prize'] = $money;
        $log_map['after_prize'] = $log_map['before_prize'] + $log_map['prize'];
        $log_map['date'] = NOW_TIME;
        $log_map['touch_member'] = '系统';
        $log_map['recep_member'] =$msg['account'];
        $log_map['content'] = '幸福信';
        $log_map['wallet'] = 1;  // 0现金钱包 1奖励钱包
      M('member_cash_log')->add($log_map);
        M('happy_msg')->where(array('id'=>$id))->setField('prize',$money);
        if($ret){

            die("<script>alert('审核通过！');history.back(-1);</script>");
        }else{
            die("<script>alert('修改失败！');history.back(-1);</script>");
        }
    }


    public function no_pass(){


        $id = I('post.id');

        $msg =   M('happy_msg')->where(array('id'=>$id,'status'=>0))->find();
        if(!$msg){

            die("<script>alert('修改失败！');history.back(-1);</script>");
        }

        $ret =    M('happy_msg')->where(array('id'=>$id))->setField('status',2); //拒绝

        if($ret){

            die("<script>alert('审核拒绝！');history.back(-1);</script>");
        }else{
            die("<script>alert('修改失败！');history.back(-1);</script>");
        }
    }
}