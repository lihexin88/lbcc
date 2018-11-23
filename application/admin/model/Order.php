<?php
namespace  app\admin\model;
use app\common\model\Base;
use think\Request;
use think\db;

class Order extends Base
{
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
        $userDataArr = model('User')->showKey();
        $userDateArr = model('User')->showKey();
        $usertextArr = model('User')->showKey();
        $orderStatusArr = model('Common/Dict')->showKey('order_status');
        // pre($orderStatusArr);
        foreach ($list as $k => $v) {
            $list[$k]['buyer_name'] = $userDataArr[$v['buyer_id']];
            $list[$k]['seller_name'] = $userDateArr[$v['seller_id']]; 
            $list[$k]['canceler_name'] = isset($v['canceler_id'])?$userDataArr[$v['canceler_id']]:'暂无';
            $list[$k]['addtime'] = date('Y-m-d H:i:s',$v['addtime']);
            $list[$k]['pay_time'] = isset($v['pay_time'])?date('Y-m-d H:i:s',$v['pay_time']):'暂无';
            $list[$k]['done_time'] = isset($v['done_time'])?date('Y-m-d H:i:s',$v['done_time']):'暂无';
            $list[$k]['cancel_reason'] = isset($v['cancel_reason'])?$v['cancel_reason']:'暂无';
            $list[$k]['orderTextArr'] = $orderStatusArr[$v['order_status']];
        }
        // pre($list);exit;
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
     
        $Order = new Order;
        $result = $Order->allowField(true)->isUpdate($where)->save($data);
        if(false === $result){
            return ['status'=>0,'info'=>$AuthGroup->getError()];
        }else{
            return array('status' => 1, 'info' => '保存成功', 'url' => url('index'));
        }
    }

    public function person($id)
    {
        $orderinfo = $this->infodata(array('id'=>$id));
        $buyerinfo = model('User')->infodata(array('id'=>$orderinfo['buyer_id']));
        $buyerinfo['type'] = '买家';
        $sellerinfo = model('User')->infodata(array('id'=>$orderinfo['seller_id']));
        $sellerinfo['type'] = '卖家';
        $list[] = $buyerinfo;
        $list[] = $sellerinfo;
        return $list;
    }



    public function cancel($data)
    {
        $id = $data['id'];
        if(false == ($orderinfo = $this->infodata(array('id'=>$id)))){
            return ['status'=>0,'info'=>'没有该订单'];
        }else{
            if($orderinfo['order_status'] == 2 || $orderinfo['order_status'] == 3){
                return ['status'=>0,'info'=>'该订单当前状态，不能撤单'];
            }else{
                $where['id'] = $orderinfo['seller_id'];
                $userinfo = model('User')->infodata($where);
                $where['dfs'] = $userinfo['dfs'] + $orderinfo['order_amount'];
                $user_result = model('User')->saveInfo($where);
                return ['status'=>1,'info'=>'撤单成功'];
            }
        }
    }

    /**
     * 改变状态
     * @param  array $data 传入数组
     */
    public function changeState($data)
    {
        if ($this->where(array('id'=>$data['id']))->update(array('order_status'=>$data['order_status']))) {
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