<?php
namespace app\admin\controller;

use app\admin\model\UserAuth;
use app\common\controller\AdminBase;
use think\Request;
use think\Db;


class User extends Admin
{
    /**
     * 列表
     * @param  integer $p 页码
     */
    public function index($p = 1)
    {
        $map = [];
        $keywords = input('get.keywords') ? input('get.keywords') : null;
        if ($keywords) {
            $map['tel'] = array('like', '%' . trim($keywords) . '%');
        }
        if (is_numeric(input('get.status'))) {
            $map['status'] = input('get.status');
        }
        //$map['user_type'] = 2;
        // pre($map);exit;
        $this->assign("info", model('User')->infoList($map, $p));
        $this->assign("state", model("Common/Dict")->showList('common_state'));//状态
        return $this->fetch();
    }

    /**
     * 新增
     */
    public function add()
    {
        if(Request::instance()->isPost()){
            $data = input('post.');
            $data['user_type'] = 2;
            
            return json(model('User')->saveInfo($data));
        }
        // $info = ['id'=>null,'username'=>null,'accout'=>null,'alipay_accout'=>null,'tel'=>null,'identity'=>null,'dimensity'=>null];
        $this->assign('info',$info);
        $this->assign('pagename','添加用户');
        return $this->fetch();
    }

    /**
     * 充值扣费
     */
    public function rechargegcu()
    {
        if(Request::instance()->isPost()){
            $data = input('post.');
            if($data['cur_type'] == 2){
                $cur_type = 'gcu';
            }else{
                $cur_type = 'usdt';
            }
            if($data['status'] == 2){
                db('user')->where('id',$data['id'])->setDec($cur_type,$data['gcu']);
                $insert_map['transaction_type'] = 11;
            }else{
                db('user')->where('id',$data['id'])->setInc($cur_type,$data['gcu']);
                $insert_map['transaction_type'] = 10;
            }
            $insert_map['money'] = $data['gcu'];
            $insert_map['user_id'] = $data['id'];
            $insert_map['create_time'] = time();
            $insert_map['pay_type'] = $data['cur_type'];
            db('record')->insert($insert_map);
            return json(array('status' => 1, 'info' => '成功', 'url' => url('index')));
        }
        $this->assign('id',input('id'));
        $this->assign('pagename','充值扣费');
        return $this->fetch();
    }
        //二维码上传
    public function upload(){
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('image');
        
        // 移动到框架应用根目录/public/uploads/ 目录下
        if($file){
            $info = $file->move(ROOT_PATH . 'public' . DS . 'upload/user');
            if($info){
                // 成功上传后 获取上传信息
                // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
                $map['link'] = '/upload/user/'.$info->getSaveName();
                $result = model('User')->saveInfo($map);
                if($result['status'] == 0){
                    $this->error('新增失败');
                }else{
                    $this->success('新增成功', 'User/index');
                }
            }else{
                // 上传失败获取错误信息
                echo $file->getError();
            }
        }else{
            $this->error('未上传');
        }
    }
    /**
     * 修改信息
     * @param  string $id ID
     */
    public function edit($id)
    {
        if (Request::instance()->isPost()) {
            return json(model('User')->changeState(input('post.')));
        }
        $this->assign("info", model('User')->infodata(array('id'=>$id)));
        $this->assign('pagename','修改用户');
        return $this->fetch('add');
    }

    /**
     * 增加扣除奖金
     * @param  string $id ID
     */
    public function editbonus($id)
    {
        if (Request::instance()->isPost()) {
            $data = input('post.');
            db('user')->where('id',$data['id'])->setInc('bonus',$data['bonus']);
            $insert_map['money'] = $data['bonus'];
            $insert_map['user_id'] = $data['id'];
            $insert_map['transaction_type'] = 12;
            $insert_map['create_time'] = time();
            db('record')->insert($insert_map);
            return json(['status' => 1, 'info' => '成功']);
        }
    }


    /**
     * 删除信息
     * @param  string $id ID
     */
    public function delete()
    {
        if (Request::instance()->isPost()) {
            return json(model('User')->deleteInfo(input('post.id')));
        }
    }
        /**
     * 重置密码
     * @param  string $id ID
     */
    public function editpwd()
    {
        if (Request::instance()->isPost()) {
            return json(model('User')->editpwd(input('post.id')));
        }
    }
    //实名认证
    public function real()
    {
        if (Request::instance()->isPost()){
            return json(model('User')->real(input('post.id')));
        }
    }

    public function userinfo()
    {
       $id = input('id');
       $user = db('user')->where('id',$id)->field('id,parent_id,tel')->select();
       foreach ($user as $k => $v) {
           $data[$k]['id'] = $v['id'];
           $data[$k]['name'] = $v['tel'];
           if(db('user')->where('parent_id',$v['id'])->find()){
            $data[$k]['icon'] = '/upload/group.png';
           }else{
            $data[$k]['icon'] = '/upload/person.png';
           }
       }
        return json($data);
    }
    public function childinfo()
    {
        $id = trim(input('id'));
       $user = db('user')->where('parent_id',$id)->field('id,parent_id,tel')->select();
        foreach ($user as $k => $v) {
           $data[$k]['id'] = $v['id'];
           $data[$k]['name'] = $v['tel'];
           if(db('user')->where('parent_id',$v['id'])->find()){
            $data[$k]['icon'] = '/upload/group.png';
           }else{
            $data[$k]['icon'] = '/upload/person.png';
           }
       }
        return json($data);
    }

    public function userallmsg()
    {
        $id = trim(input('id'));
        $user = db('user')->where('id',$id)->find();
        if($user['parent_id'] == 0){
            $user['user_invitation_code'] = '暂无';
        }else{
           $user['user_invitation_code'] = db('user')->where('id',$user['parent_id'])->value('invitation_code'); 
        }
        $user['create_time'] = date('Y-m-d H:i:s',$user['create_time']);
        if(!$user['invitation_code']){
            $user['invitation_code'] = '暂无';
        }
        if(!$user['username']){
            $user['username'] = '暂无';
        }
        return $user;
    }

	/**
	 * 获取全部用户认证信息
	 * @return mixed
	 * @throws \think\exception\DbException
	 */
    public function user_auth()
    {
    	$UserAuth = new UserAuth();
    	$userauth = $UserAuth->all();
    	$this->assign('info',$userauth);
    	return $this->fetch();
    }

	/**
	 * 用户认证
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
    public function authenticate()
    {
    	$id = $_POST['uid'];
    	$type = $_POST['type'];
    	$UserAuth = new UserAuth();
    	if(!$UserAuth->authenticate($id,$type))
	    {
	    	return rtn(-1,'1');
	    }
	    return rtn(1,'已审核');

    }
}