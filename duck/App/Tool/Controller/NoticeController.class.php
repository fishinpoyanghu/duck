<?php
/**
 * 公告后台管理
 * User:十八
 * QQ：274020083
 * Date: 16/6/11
 * Time: 上午11:22
 */

namespace Tool\Controller;


class NoticeController extends CommonController{



    public function index(){


       $list =  M('notice')->where()->select();

        $this->assign('list',$list);
        $this->display();

    }


    //公告发布

    public function post(){

   if(IS_POST){
       $post = I('post.');

       $post['date'] = NOW_TIME;

       if($post['content'] == ''){


           $this->error('内容不能为空');

       }

       $ret =  M('notice')->create($post);

       if($ret){

           M('notice')->add();

           $this->success('发布成功');
       }else{

           $this->error('发布失败');
       }
   }

        $this->display();
    }

    //更新公告
    public function update(){

        if(IS_POST){

             $post = I('post.');
             $ret =  M('notice')->save($post);

            if($ret){

                $this->success('修改成功');
            }
            else{
                $this->error('修改失败');
            }

         }

         $notice = M('notice')->where(array('id'=>I('get.id')))->find();
         $this->assign('notice',$notice);
         $this->display();
    }


    //删除公告
    public function del(){

        if(IS_POST){

            $r = M('notice')->where(array('id'=>I('post.id')))->delete();

            if($r){

                $this->success('删除成功');
            }else{

                $this->error('删除失败');
            }
        }

    }
}