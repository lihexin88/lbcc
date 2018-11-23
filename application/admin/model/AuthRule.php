<?php
namespace  app\admin\model;
use think\Request;
use think\db;
use think\Validate;
use think\Model;
class AuthRule extends Model
{
     protected $autoWriteTimestamp = false;

    /**
     * 权限列表
     */
    public function ruleList()
    {
        $list = $this->where(['pid'=>0])->order('sort desc,id asc')->select()->toArray();
        foreach ((array)$list as $k => $v) {
            $list[$k]['child'] = $this->where(['pid'=>$v['id']])->order('sort desc,id asc')->select();
        }
        return $list;
    }

    /**
     * 根据pid获取列表
     * @param  string $id PID
     */
    public function pidList($id)
    {
        $list = $this->where(['pid'=>$id])->order('id asc')->select();
        return $list;
    }

    /**
     * 保存修改
     * @param  array $data 传入数据
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
        $AuthRule = new AuthRule;
        $result = $AuthRule->allowField(true)->isUpdate($where)->save($data);
        if(false === $result){
            return ['status'=>0,'info'=>$AuthGroup->getError()];
        }else{
            return array('status' => 1, 'info' => '保存成功', 'url' => url('index'));
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

}