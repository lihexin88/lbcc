<?php
namespace  app\admin\model;
use app\common\model\Base;
use think\Request;
use think\db;
use think\Validate;
class Admin extends Base
{

    protected function setPasswordAttr($value)
    {
        return encrypt(trim($value));
    }

    const PAGE_LIMIT = '10';//用户表分页限制
    const PAGE_SHOW = '10';//显示分页菜单数量

    /**
     * 用户登录
     * @param  array $data 用户登录信息
     * @return array       返回登录结果
     */
    public function userLogin($data)
    {
        if (!$data['username'] || !$data['password'] || !$data['verify']) {
            return array('status'=>0,'info'=>'请补全登录信息');
        }
        if (!captcha_check($data['verify'])) {
            return array('status'=>0,'info'=>'验证码输入错误！');
        }
        if(false == ($info = $this->where(['username'=>$data['username']])->find()) ){
            if(false == ($info = db('user')->where(['username'=>$data['username']])->find())){
                return array('status'=>0,'info'=>'账号不存在！');
            }else{
                session('user_type', $info['user_type']);
            }
        }else{
            session('user_type', $info['user_type']);
        }
        if($info['status'] == 0){
            return array('status'=>0,'info'=>'账号被禁用！');
        }
        if($info['password'] != encrypt(trim($data['password'])) ){
            return array('status'=>0,'info'=>'密码错误！');
        }
        if(!$info['id']){
            return array('status'=>0,'info'=>'不存在此用户！');
        }
        session('aid', $info['id']);
        session('username', $info['username']);
        if($info['id'] != 1){
            session('group_id',db('AuthGroupAccess')->where('uid',$info['id'])->column('group_id'));
        }
        $this->lastLogin($info['id']);
        return array('status'=>1,'info'=>'登录系统成功','url'=>url('/admin/index/index'));
    }

    /**
     * 修改密码
     * @param  array $data 传入数据
     */
    public function editPwd($data)
    {
        if(!$data['oldpassword']){
            return ['status'=>'0','info'=>'请输入当前密码！'];
        }
        if(!$data['password']){
            return ['status'=>'0','info'=>'请输入新密码！'];
        }
        if($data['repassword']  != $data['password']){
            return ['status'=>'0','info'=>'两次输入的新密码不一致！'];
        }
        if(encrypt(trim(I('post.oldpassword'))) != ($pwd = $this->getFieldById(AID,'password')) ){
            return ['status'=>'0','info'=>'原密码不正确！'];
        }
        $info['id'] = AID;
        $info['password'] = encrypt(trim($data['password']));
        if(!$this->save($info)){
            return ['status'=>'0','info'=>'密码修改失败,请重试'];
        }
        return ['status'=>1,'info'=>'密码修改成功,请重新登录','url'=>U('Public/logout')];
    }

    /**
     * 新增/修改用户
     * @param  array $data 传入信息
     */
    public function saveInfo($data)
    {            
        $id = $data['id'];
        if(!empty($id)){
            $where = true;
        }else{
            $where = false;
        }
        $admin = new Admin;
        $result = $admin->allowField(true)->validate(true)->isUpdate($where)->save($data);
        if(false === $result){
            // 验证失败 输出错误信息
            return ['status'=>0,'info'=>$admin->getError()];
        }else{
            $info['uid'] = $admin->id;
            $info['group_id'] = $data['group_id'];
            $rs = model('AuthGroupAccess')->saveinfo($info);
            if($rs['status'] == 0){
                return array('status' => 0, 'info' => '保存失败');
            }else{
                return array('status' => 1, 'info' => '保存成功', 'url' => url('index'));
            }
        }
    }

    /**
     * 获取管理员信息
     * @param  string $id 管理员ID
     */
    public function adminInfo($id)
    {
        $info = $this->where(array('id' => $id))->find();
        $info['group_id'] = db("AuthGroupAccess")->where(array('uid' => $id))->column('group_id');
        return $info;
    }

    /**
     * 我的信息
     * @param  string $id 管理员ID
     */
    public function myInfo($id)
    {
        $info = $this->where(['id'=>$id])->find();
        if($id == 1){
            $info['groupName'] = '超级管理员';
        }else{
            $info['groupName'] = model("AuthGroup")->where(['id'=>model("AuthGroupAccess")->where(['uid'=>$id])->value('group_id')])->value('title');
        }

        $info['statusTxt'] = $info['status'] == 1 ? "启用" : "禁用";
        $info['last_login_time'] =date('Y-m-d h:i:s',$info['last_login_time']);
        $info['last_login_ip'] = long2ip($info['last_login_ip']);
        $info['update_time'] = date('Y-m-d h:i:s',$info['update_time']);
        return $info;
    }

    /**
     * 记录最后一次登录信息
     */
    public function lastLogin($aid)
    {
        $request = Request::instance();
        $this->where(array('id' => $aid))->setField(array('last_login_time' => time(), 'last_login_ip' => $request->ip()));
        $this->where(array('id' => $aid))->setInc('login_number');
    }

    /**
     * 密码加密
     * @param  string $pwd 密码
     * @return string      加密后的密码
     */
    protected function md5Pwd($pwd)
    {
        //有ID和密码，为修改密码操作
        if(I('post.id') && I('post.password')){
            return encrypt($pwd);
        }else{
            if (!$pwd) {
                $pwd = '123456';
            }
            return encrypt($pwd);
        }
    }

    /**
     * 改变状态
     * @param  array $data 传入数组
     */
    public function changeState($data)
    {
        $data['update_time'] = NOW_TIME;
        if(session('group_id') == 5){
            return array('status' =>0, 'info' => '该访问不在授权范围内!');
        }
        if ($this->save($data)) {
            return array('status' => 1, 'info' => '更改状态成功');
        } else {
            return array('status' => 0, 'info' => '更改状态失败');
        }
    }

    /**
     * 删除
     * @param  string $id ID
     */
    public function deleteInfo($id)
    {
        if($this->delete($id)){
            return ['status'=>1,'info'=>'用户删除成功'];
        }else{
            return ['status'=>0,'info'=>'用户删除失败,请重试'];
        }
    }



    /**
     * model 管理员列表
     */
    public function adminList($p, $map){
        $request= Request::instance();

        $list = $this -> where($map) -> page($p, self::PAGE_LIMIT) -> order('id DESC') -> select() -> toArray();
        $count = $this -> where($map) -> count();
        foreach ($list as $k => $v) {
            // 管理员类型
            if($v['id'] === 1){
                $userType['type'] = 'admin_type';
                $userType['value'] = $v['user_type'];
                $list[$k]['userTypeTxt'] = Db::name('dict') -> where($userType) -> value('key');
            }else{
                $auth_group_where['id'] = $v['user_type'];
                $list[$k]['userTypeTxt'] = Db::name('auth_group') -> where($auth_group_where) -> value('title');
            }

            // 管理员状态
            $userStatus['type'] = 'common_state';
            $userStatus['value'] = $v['status'];
            $list[$k]['statusTxt'] = Db::name('dict') -> where($userStatus) -> value('key');
            switch($v['status']){
                case 1:
                    $list[$k]['status_button'] = 'state_green';
                    break;
                case 2:
                    $list[$k]['status_button'] = 'state_red';
                    break;
            }

        }
        $return['count'] = $count;
        $return['list'] = $list;
        $return['page'] = boot_page($return['count'], self::PAGE_LIMIT, self::PAGE_SHOW, $p,$request->action());
        return $return;
    }

    /**
     * model 改变管理员状态
     * @param  array $data 传入数组
     */
    public function changeStatus($data){
        $mod['update_time'] = time();
        $id = $data['id'];
        if(!$id){
            return array('code' => 0,'msg' => '未获取管理员信息!');
        }
        $status = $data['status'];
        if(!$status){
            return array('code' => 0,'msg' => '未获取管理员状态!');
        }

        if($status == 1){
            $mod['status'] = 2;
        }else{
            $mod['status'] = 1;
        }

        $result = $this -> where('id',$id) -> update($mod);

        if ($result) {
            return array('code' => 1, 'msg' => '更改状态成功');
        } else {
            return array('code' => 0, 'msg' => '更改状态失败');
        }
    }

    /**
     * model 重置管理员密码
     */
    public function resetPwd($id){
        if(!$id){
            return ['code' => 0,'msg' => '未获取管理员信息!'];
        }

        $map['password'] = encrypt('123456');
        $map['update_time'] = time();
        $result = $this -> where(array('id' => $id)) -> update($map);
        if($result){
            return ['code' => 1,'msg' => '更改成功!'];
        }else{
            return ['code' => 0,'msg' => '更改失败!'];
        }
    }

    /**
     * model 删除管理员
     * @param  string $id ID
     */
    public function deleteAdmin($id){
        if(!$id){
            return ['code' => 1,'msg' => '未获取管理员信息!'];
        }

        $condition = 0;
        try{

            $this -> where(array('id' => $id)) -> delete();
            Db::name('auth_group_access') -> where(array('uid' => $id)) -> delete();

            Db::commit();
            $condition = 1;
        }catch(\PDOException $e){
            Db::rollback();
        }

        if($condition == 1){
            return ['code' => 1,'msg' => '用户删除成功'];
        }else{
            return ['code' => 0,'msg' => '用户删除失败,请重试'];
        }
    }

}