<?php
namespace app\admin\controller;

use app\common\controller\AdminBase;
use think\Request;
use think\Db;
class Index extends AdminBase
{

    public function index()
    {
        return $this->fetch();
    }

    public function profile()
    {
        $this->assign('info', model("Admin")->myInfo(AID));
        return $this->fetch();
    }

    public function repwd()
    {
        if (Request::instance()->isPost()) {
            if (!input('post.oldpassword')) {
                $this->error('请输入当前密码！');
            }
            $pwd = db('Admin')->where('id=' . AID)->value('password');
            if ($pwd != encrypt(trim(input('post.oldpassword')))) {//去除前后空格
                $this->error('操作失败:原密码不符！');
            }
            if (!input('post.password')) {
                $this->error('请输入新密码！');
            }
            if (input('post.password') != input('post.password')) {
                $this->error('两次输入的新密码不一致！');
            }
            $data['password'] = encrypt(trim(input('post.password')));

            $data['id'] = AID;
            model('Admin')->saveInfo($data);
            $this->success('操作成功,请重新登录!', url('Publics/logout'));
        } else {
            $this->assign('info', AID);
            return $this->fetch();
        }
    }
}