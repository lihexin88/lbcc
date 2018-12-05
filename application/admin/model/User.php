<?php
namespace  app\admin\model;
use app\common\model\Base;
use think\Request;
use think\db;
use think\Validate;
use think\Exception;
class User extends Base
{

    /**
    *添加时自动完成
    */
    protected $insert = array('invitation_code','credit_level'=> 3,'all_profit'=>0);

    /**
    *更新时自动完成
    */
    protected $update = [];

    /**
    *自动加密
    */
    public function setPasswordAttr($value)
    {
        return encrypt($value);
    }
    public function setPaymentPasswordAttr($value)
    {
        return encrypt($value);
    }

    /**
    *自动生成邀请码
    */
    public function setInvitationCodeAttr()
    {
        return generateOrderNumber();
    }
    const PAGE_LIMIT = '10';//用户表分页限制
    const PAGE_SHOW = '10';//显示分页菜单数量


    /**
     * 获取列表
     * @param  array $map 查询条件
     * @param  string $p  页码
     * @return array      返回列表
     */
    public function infoList($map, $p)
    {
        $request= Request::instance();
        $list = $this->where($map)->order('id desc')->page($p, self::PAGE_LIMIT)->select()->toArray();
        foreach ($list as $k => $v) {
            $userinfo = $this->infodata(array('id'=>$v['parent_id']));
            $list[$k]['parent_name'] = $userinfo['username'];
        }
        $return['count'] = $this->where($map)->count();
        $return['list'] = $list;
        
        $return['page'] = boot_page($return['count'], self::PAGE_LIMIT, self::PAGE_SHOW, $p,$request->action());
        return $return;
    }

    /**
     * 新增/修改
     * @param  array $data 传入信息
     */
    public function saveInfo($data)
    {    
        if(array_key_exists('id',$data)){
            $id = $data['id'];
            if(!empty($id)){
                $where = true;
            }else{
                $where = false;
            }
        }else{
            $where = false;
        }     
        $User = new User;
        $result = $User->allowField(true)->isUpdate($where)->save($data);
        if(false === $result){
            // 验证失败 输出错误信息
            return ['status'=>0,'info'=>$User->getError()];
        }else{
            return array('status' => 1, 'info' => '保存成功', 'url' => url('index'));
        }
    }

    /**
     * 改变状态
     * @param  array $data 传入数组
     */
    public function changeState($data)
    {
        if ($this->where(array('id'=>$data['id']))->update(array('status'=>$data['status']))) {
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
        if($this->where(array('id'=>$id))->delete()){
            return ['status'=>1,'info'=>'删除成功'];
        }else{
            return ['status'=>0,'info'=>'删除失败,请重试'];
        }
    }

      /**
     * 重置密码
     * @param  array $data 传入数组
     */
    public function editpwd($id)
    {
        $map['password'] = encrypt('123456');
        $map['payment_password'] = encrypt('123456');
        $map['update_time'] = time();
        if ($this->where(array('id'=>$id))->update($map)) {
            return array('status' => 1, 'info' => '更改成功');
        } else {
            return array('status' => 0, 'info' => '更改失败');
        }
    }


        /**
     * 根据查询条件获取信息
     * @param string $map [查询条件]
     * @return mixed
     */
    public function infodata($map){
        $list = $this->where($map)->find();
        if(!is_null($list)){
            return $list->toArray();
        }
        return false;
    }

    /* 根据键名取键值 用于foreach */
    public function showKey()
    {
        $list = $this->field('id,account')->order('id desc')->select()->toArray();
        foreach ((array)$list as $k => $v) {
            $return[$v['id']] = $v['id'];
          	$return[$v['account']] = $v['account'];
        }
        return $return;
    }

    public function showList()
    {
        $list = $this->field('id,account')->order('id asc')->select()->toArray();
        return $list;
    }

    /**
     * 充值扣费
     * @param $data
     * @return array
     */
    public function rechargegcu($data){
        if(!$data['uid']){
            return ['status' => 0,'info' => '未获取用户信息!'];
        }

        if(!$data['cur_type']){
            return ['status' => 0,'info' => '请选择币种!'];
        }

        if(!$data['number']){
            return ['status' => 0,'info' => '请输入价格!'];
        }

        Db::startTrans();
        try{
            // 执行 充值/扣费
            $user_cur_where['uid'] = $data['uid'];
            $user_cur_where['cur_id'] = $data['cur_type'];
            if($data['status'] == 2){
                Db::name('user_cur')->where($user_cur_where)->setDec('number',$data['number']);
                $msg = '扣费成功!';
            }else{
                Db::name('user_cur')->where($user_cur_where)->setInc('number',$data['number']);
                $msg = '充值成功!';
            }

            // 添加记录
            $in_finance['aid'] = $_SESSION['think']['aid'];
            $in_finance['uid'] = $data['uid'];
            $in_finance['cur_id'] = $data['cur_type'];
            $in_finance['type'] = $data['status'];
	        $in_finance['number'] = $data['number'];
	        $in_finance['remarks'] = $data['remarks'];
            $in_finance['create_time'] = time();
            Db::name('admin_manage_finance') -> insert($in_finance);

            Db::commit();
            return ['status' => 1,'info' => $msg];
        }catch(\Exception $e){
            Db::rollback();
            return ['status' => 0,'info' => $e -> getMessage()];
        }
    }
}