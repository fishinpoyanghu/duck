<?php
/**
 *
 * 交易
 * User:十八
 * QQ：274020083
 * Date: 16/7/27
 * Time: 下午11:05
 */

namespace Tool\Controller;

class BuyController extends CommonController{
    /**
     * 饲料记录
     */
    public function buyFeedLog(){
        $log = M('user_finance as finance')->field('member.phone,finance.*')->join('inner join '.C('DB_PREFIX').'member as member on member.id = finance.userid')->where('type = 2')->limit(100)->select();
//        print_r($log);
        $this->assign('log',$log);
        $this->display();
    }
    public function delFeedLog(){
        if(IS_AJAX){
            $id = I('id','','intval');
            if($id){
                $res = M('user_finance')->delete($id);
                if($res){
                    $this->ajaxReturn(array('status'=>1));
                }
            }
        }
    }

    /**
     * 激活码
     */
    public function buyActiveCodeLog(){
        $log = M('user_finance as finance')->field('member.phone,finance.*')->join('inner join '.C('DB_PREFIX').'member as member on member.id = finance.userid')->where('type = 3')->limit(100)->select();
//        print_r($log);
        $this->assign('log',$log);
        $this->display();
    }
    public function delActiveCodeLog(){
        if(IS_AJAX){
            $id = I('id','','intval');
            if($id){
                $res = M('user_finance')->delete($id);
                if($res){
                    $this->ajaxReturn(array('status'=>1));
                }
            }
        }
    }

    /**
     * 购买鸡记录
     */
    public function buyAnimalLog(){
        $log = M('user_finance as finance')->field('member.phone,finance.*')->join('inner join '.C('DB_PREFIX').'member as member on member.id = finance.userid')->where('type = 1')->limit(100)->select();
//        print_r($log);
        $this->assign('log',$log);
        $this->display();
    }
    public function delAnimalLog(){
        if(IS_AJAX){
            $id = I('id','','intval');
            if($id){
                $res = M('user_finance')->delete($id);
                if($res){
                    $this->ajaxReturn(array('status'=>1));
                }
            }
        }
    }
    /**
     * 鸡场
     */
    public function buyAnimal(){
        $log = M('user_animal as animal')->field('member.phone,animal.*')->join('inner join '.C('DB_PREFIX').'member as member on member.id = animal.userid')->limit(100)->select();
        $this->assign('log',$log);
        $this->display();
    }
    public function delAnimal(){
        if(IS_AJAX){
            $id = I('id','','intval');
            if($id){
                $res = M('user_animal')->delete($id);
                if($res){
                    $this->ajaxReturn(array('status'=>1));
                }
            }
        }
    }
    /**
     * 奖金记录
     */
    public function bonusLog(){
        $log = M('bonus_log as bonus_log')->field('bonus_log.*,member.phone,member_to.phone as tophone')
            ->join('inner join '.C('DB_PREFIX').'member as member on member.id = bonus_log.userid')
            ->join('inner join '.C('DB_PREFIX').'member as member_to on member_to.id = bonus_log.touserid')
            ->limit(100)
            ->select();
        $this->assign('log',$log);
        $this->display();
    }
    public function delBonusLog(){
        if(IS_AJAX){
            $id = I('id','','intval');
            if($id){
                $res = M('bonus_log')->delete($id);
                if($res){
                    $this->ajaxReturn(array('status'=>1));
                }
            }
        }
    }

}
