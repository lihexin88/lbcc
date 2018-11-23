<?php
namespace app\admin\controller;

use app\common\controller\AdminBase;
use think\Request;
use think\Db;

class UserMills extends Admin
{
    /**
     * 列表
     * @param  integer $p 页码
     */
    public function index($p = 1)
    {
        $map = [];
        if (is_numeric(input('get.uid'))) {
            $map['uid'] = input('get.uid');
        }
        if (is_numeric(input('get.mills_state'))) {
            $map['mills_state'] = input('get.mills_state');
        }
        $this->assign("info", model('UserMills')->infoList($map, $p));
        $this->assign("userlist", model("User")->showList());
        $this->assign("mills_state", model("Common/Dict")->showList('mills_state'));
        return $this->fetch();
    }

    /**
     * 新增
     */
    public function add()
    {
        if(Request::instance()->isPost()){
            $data = input('post.');
            $user = db('user')->where('id',input('uid'))->find();
            if($user['identity']){
                $data['mills_state'] = 1;
                $data['begin_time'] = time();
                $data['end_time'] = time()+config('BATTERY_CYCLE')*24*3600;
                
            }
            return json(model('UserMills')->saveInfo($data));
        }
        $info = ['id'=>null,'username'=>null,'mills_id'=>null,'alipay_accout'=>null,'parent_id'=>null,'dfs'=>null,'usd'=>null,'tel'=>null,'identity'=>null];
        $this->assign('info',$info);
        $this->assign("userList", model("User")->showList());
        $this->assign("millsList", model("Mills")->showList());
        $this->assign('pagename','赠送用户');
        return $this->fetch();
    }

    /**
     * 修改信息
     * @param  string $id ID
     */
    public function edit($id)
    {
        if (Request::instance()->isPost()) {
            return json(model('UserMills')->changeState(input('post.')));
        }
        $this->assign("info", model('UserMills')->infodata(array('id'=>$id)));
        $this->assign('pagename','修改用户');
        return $this->fetch('add');
    }

    /**
     * 删除信息
     * @param  string $id ID
     */
    public function delete()
    {
        if (Request::instance()->isPost()) {
            return json(model('UserMills')->deleteInfo(input('post.id')));
        }
    }
}