<?php
/**
 *
 * User:十八
 * QQ：274020083
 * Date: 16/5/18
 * Time: 上午4:56
 */

namespace Tool\Controller;
use Think\Controller;
class CommonController extends Controller {


    public function _initialize(){

        //加载配置
        $config = M('system_config')->where(array('id'=>1))->find();
        $this->assign('config',$config);

        if(!check_admin_login() && ACTION_NAME != "jihuo"){// 还没登录 跳转到登录页面
            $this->redirect('/Relay/login');
        }

    }

}