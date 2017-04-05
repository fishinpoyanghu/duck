<?php
namespace Tool\Controller;
use Think\Controller;


class RelayController extends Controller
{


    public function _initialize(){

        //加载配置
        $config = M('system_config')->where(array('id'=>1))->find();
        $this->assign('config',$config);


    }

    public  function login($username = null, $password = null, $verify = null)
    {


        if (IS_POST) {
            /* 检测验证码 TODO: */
            if (!check_verify($verify)) {
                die("<script>alert('验证码输入错误!');history.go(-1);</script>");
            }
            $db = M('tool_admin');

            $map['username'] = $username;
            $member = $db->where($map)->find();

            if (!$member) {

               // $this->error('帐号不存');
                die("<script>alert('帐号不存!');history.go(-1);</script>");
            }
            if ($member['password'] != md5($password)) {

                //$this->error('密码错误');
                die("<script>alert('密码错误!');history.go(-1);</script>");

            }
            
            /* 记录登录SESSION和COOKIES */
            $auth = array(
                'id'=>$member['id'],
                'username'=>$member['username'],

            );
            session('admin', $auth);
            admin_log($member['username'],'登陆后台管理');
            $this->success('登录成功！',U('Tool/Index/index'));
        }else{

            if(check_admin_login()){
                $this->redirect('/Index/index');
            }

            $this->display();

        }
    }


    /* 退出登录 */
    public function logout()
    {
        session('admin', null);
        session('[destroy]');
        $this->success('退出成功',U('Tool/Relay/login'));
    }
    public function verify()
    {
        ob_end_clean();
		$config =    array(
		'codeSet' => '123456789',
		'fontSize'    =>    30,    // 验证码字体大小
		'length'      =>    4,     // 验证码位数
		'useNoise'    =>    true, // 关闭验证码杂点
	);
        $verify = new \Think\Verify($config);
		
        $verify->entry();
    }

}
