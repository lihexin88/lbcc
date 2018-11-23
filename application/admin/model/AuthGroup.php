<?php
namespace  app\admin\model;
use think\Model;
use think\Request;
use think\db;
use think\Validate;
class AuthGroup extends Model
{
	protected $insert = ['module'=>'admin','status' => 1,'type'=>1];
    public function groupList() {
        $list = $this->where( array('module'=>'admin','type'=>'1') )->order('id asc')->select()->toArray();
        foreach ((array)$list as $k => $v) {
            $list[$k]['statusTxt'] = $v['status'] == 1 ? "启用" : "禁用";
			$list[$k]['chStatusTxt'] = $v['status'] == 0 ? "启用" : "禁用";
        }
        return $list;
    }
	
	public function getGroup() {
        $list = $this->where( array('status'=>1) )->field('id,title')->order('id asc')->select()->toArray();
        return $list;
    }
	
	public function getUserGroup() {
		if(session('group_id')){
			$list = $this->where(['status'=>1,'id'=>array('gt',session('group_id'))])->field('id,title')->order('id desc')->select()->toArray();
        	return $list;
		}else{
			return $this->getGroup();
		}
    }

    public function saveInfo($data)
    {            
        $id = $data['id'];
        if(!empty($id)){
            $where = true;
        }else{
            $where = false;
        }
        $AuthGroup = new AuthGroup;
        $result = $AuthGroup->allowField(true)->validate(true)->isUpdate($where)->save($data);
        if(!$where){
        	$map['type'] = 'admin_type';
        	$map['key'] = $data['title'];
        	$map['value'] = $AuthGroup->id;
        	model('Dict')->saveInfo($map);
        }
        if(false === $result){
            return ['status'=>0,'info'=>$AuthGroup->getError()];
        }else{
        	return array('status' => 1, 'info' => '保存成功', 'url' => url('index'));
        }
    }
	
	public function getGroupRules($id){
		$list =$this->field('id,title,rules')->where( array('id'=>$id,'module'=>'admin'))->find();
        return $list;
    }
	
}