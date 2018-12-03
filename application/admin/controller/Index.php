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

    /**
     * controller 修改当前登陆管理员密码
     */
    public function repwd(){
        if (Request::instance() -> isPost()) {
            if (!input('post.oldpassword')) {
                $this -> error('请输入当前密码!');
            }

            // 验证管理员原密码是否正确
            $pwd = Db::name('admin') -> where('id =' . AID) -> value('password');
            if ($pwd != encrypt(trim(input('post.oldpassword')))) {
                return json(array('code' => 0,'msg' => '操作失败:原密码不符!'));
            }

            // 判断新密码是否为空
            if (!trim(input('post.password'))) {
                return json(array('code' => 0,'msg' => '请输入新密码!'));
            }

            // 判断两次输入的密码是否一致
            if (trim(input('post.password')) != trim(input('post.repassword'))) {
                return json(array('code' => 0,'msg' => '两次输入的新密码不一致!'));
            }

            $data['password'] = encrypt(input('post.password'));
            $id = AID;

            $result = Db::name('admin') -> where('id',$id) -> update($data);
            if($result){
                return json(array('code' => 1,'msg' => '修改密码成功,请重新登录!','url' => url('Publics/logout')));
            }else{
                return json(array('code' => 0,'msg' => '修改密码失败!'));
            }
        } else {
            $this->assign('info', AID);
            return $this->fetch();
        }
    }
}