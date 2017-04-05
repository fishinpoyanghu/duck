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

class IndexController extends Controller
{
    public function _initialize(){
        // $token = I('token');
        $token = $_SESSION['token'];
        $id =  M('member')->where(array('token'=>$token))->getField('id');
        if(ACTION_NAME != 'index' && !$id){
            $this->redirect('/');
        }
    }
    /**
     * 登录页
     */
    public function index(){
        $this->display();
    }
    public function home(){
        $this->display();
    }
    public function chickensLog(){
        $this->display();
    }
    public function farm(){
        $this->display();
    }
    public function records(){
        $this->display();
    }
    public function user(){
        $token = $_SESSION['token'];
        $data = M('member')->where(array('token'=>$token))->find();
        $this->assign('data',$data);
        $this->display();
    }

}