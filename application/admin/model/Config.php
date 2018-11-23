<?php
namespace  app\admin\model;
use app\common\model\Base;
use think\Request;
use think\db;

class Config extends Base
{
    protected $autoWriteTimestamp = false;
    const PAGE_LIMIT = '10';//用户表分页限制
    const PAGE_SHOW = '10';//显示分页菜单数量

    public function configPage($url)
    {
        $list = $this->where(array('url' => $url))->order('id asc')->select();
        // pre($list);exit;
        return $list;
    }

    public function saveConfig($datas)
    {
        foreach ($datas as $k => $v) {
            $Config = new Config;
            $result = $Config->where('key', $k)->update(['value' => $v]);
        }
        return array('status' => 1, 'info' => '保存成功!');
    }

    /* 列表显示所有config数据 */
    public function infoList($map, $p)
    {   $request= Request::instance();
        $list = $this->where($map)->order('url asc,id asc')->page($p, self::PAGE_LIMIT)->select()->toArray();
        $urlArr = model('Common/Dict')->showKey('config_url');
        $typeArr = model('Common/Dict')->showKey('config_type');
        foreach ((array)$list as $k => $v) {
            $list[$k]['urlTxt'] = $urlArr[$v['url']];
            $list[$k]['typeTxt'] = $typeArr[$v['type']];
        }
        $return['count'] = $this->where($map)->count();
        $return['list'] = $list;
        $return['page'] = boot_page($return['count'], self::PAGE_LIMIT, self::PAGE_SHOW, $p,$request->action());
        return $return;
    }

    public function listInfo($id)
    {
        $info = $this->where(array('id' => $id))->find();
        return $info;
    }

    /* 保存config参数 */
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
        $Config = new Config;
        $result = $Config->allowField(true)->isUpdate($where)->save($data);
        if(false === $result){
            return ['status'=>0,'info'=>$AuthGroup->getError()];
        }else{
            return array('status' => 1, 'info' => '保存成功', 'url' => url('setting'));
        }
    }

}