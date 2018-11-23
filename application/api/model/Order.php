<?php

namespace app\api\model;

use think\Model;

class Order extends Model
{
    // 关闭自动写入时间戳
    protected $autoWriteTimestamp = false;



    /**
     * 我的订单列表页面
     * @param string $id [排序类型]
     * @param array  $map [查询条件]
     * @param string $p [页数]
     */
    public function orderListPage($id,$map,$p,$dealtype)
    {
        $list = $this->where($map)->where('buyer_id|seller_id','=',$id)->page($p, 10)->order('addtime desc')->select()->toArray();
        $userTypeArr = model('User')->showKey();
        $orderStatusArr = model('Dict')->showKey('order_status');
        foreach ($list as $k => $v) {
            $list[$k]['seller_name'] = $userTypeArr[$v['seller_id']];
            //$list[$k]['buyer_name'] = $userTypeArr[$v['buyer_id']];
            if($v['buyer_id'] == $id){
                if($map['order_status'] == 0){
                    $list[$k]['type'] = '点击确认支付';
                }else{
                    $list[$k]['type'] = '购买记录';
                }
            }else{
                if($map['order_status'] == 1){
                    $list[$k]['type'] = '点击确认收款';
                }else{
                    $list[$k]['type'] = '挂卖记录';
                }
            }
            $userinfo = model('User')->infodata(array('id'=>$v['buyer_id']));
            $sellerinfo = model('User')->infodata(array('id'=>$v['seller_id']));
            $sellinfo = model('Sell')->infodata(array('id'=>$v['sell_id']));
            if($dealtype == 1){
                $list[$k]['alipay_accout'] = $sellerinfo['alipay_accout'];
                $list[$k]['seller_name'] = $userTypeArr[$v['seller_id']];
            }else{
                $list[$k]['seller_name'] = $userTypeArr[$v['buyer_id']];
                $list[$k]['alipay_accout'] = $userinfo['alipay_accout'];
            }
            $list[$k]['price'] = $sellinfo['sell_amount'];
            $list[$k]['addtime'] = date('Y-m-d H:i:s',$v['addtime']);
            $list[$k]['orderStatusText'] = $orderStatusArr[$v['order_status']];
        }
        $data['list'] = $list;
        $data['count'] = ceil(($this->where($map)->where('buyer_id|seller_id','=',$id)->count())/10);
        return $data;
    }


    /**
     * 生成抢币订单
     */
    public function grabCoin($id,$uid,$seller_id,$number)
    {
        $map['order_sn'] = generateOrderNumber();
        $map['order_amount'] = $number;
        $map['order_status'] = 0;
        $map['buyer_id'] = $uid;
        $map['seller_id'] = $seller_id;
        $map['addtime'] = time();
        $map['sell_id'] = $id;
        if($this->saveInfo($map)){
            return true;
        }
        return false;
    }

    /**
     * 确认付款
     * @param string $id [用户ID]
     * @param string $order_id [订单ID]
     */
    public function isPay($id,$order_id)
    {
        if(false == ($orderinfo = $this->infodata(array('order_id'=>$order_id)))){
            return ['status'=>0,'info'=>lang("null")];
        }else{
            if($orderinfo['buyer_id'] == $id){
                if($orderinfo['order_status'] !== 0){
                    return ['status'=>0,'info'=>lang('execution')];
                }else{
                    $map['order_id'] = $order_id;
                    $map['order_status'] = 1;
                    $map['pay_time'] = time();
                    $result = $this->isUpdate(true)->save($map);
                    if ($result) {
                        return ['status'=>1,'info'=>lang("success")];
                    } else {
                        return ['status'=>0,'info'=>lang("error")];
                    }  
                }
            }else{
                return ['status'=>0,'info'=>lang('execution')];
            }
        }
    }

    /**
     * 确认收款
     * @param string $id [用户ID]
     * @param string $order_id [订单ID]
     */
    public function isIncome($id,$order_id)
    {
        if(false == ($orderinfo = $this->infodata(array('order_id'=>$order_id)))){
            return ['status'=>0,'info'=>lang('null')];
        }else{
            if($orderinfo['seller_id'] == $id){
                if($orderinfo['order_status'] !== 1){
                    return ['status'=>0,'info'=>lang('execution')];
                }else{
                    $map['order_id'] = $order_id;
                    $map['order_status'] = 2;
                    $map['done_time'] = time();
                    $result = $this->isUpdate(true)->save($map);
                    if ($result) {
                        $where['id'] = $orderinfo['buyer_id'];
                        $userinfo = model('User')->infodata($where);
                        $where['dfs'] = $userinfo['dfs'] + $orderinfo['order_amount'];
                        $user_result = model('User')->saveInfo($where);
                        if($user_result){
                            $record_map['money'] = $orderinfo['order_amount'];
                            $record_map['user_id'] = $orderinfo['buyer_id'];
                            $record_map['trader_id'] = $orderinfo['seller_id'];
                            $record_map['sell_id'] = $orderinfo['sell_id'];
                            $record_map['transaction_type'] = 1;
                            $record_map['create_time'] = time();
                            $record_result = model('Record')->saveInfo($record_map);
                            model('Record')->upGrade($orderinfo['buyer_id']);
                            if($record_result){
                                $record_map2 = $record_map;
                                $record_map2['user_id'] = $orderinfo['seller_id'];
                                $record_map2['trader_id'] = $orderinfo['buyer_id'];
                                $record_map2['transaction_type'] = 2;
                                $record_result2 = model('Record')->saveInfo($record_map2);
                                model('Record')->upGrade($orderinfo['seller_id']);
                            }
                            return ['status'=>1,'info'=>lang('success')];
                        }else{
                            return ['status'=>0,'info'=>lang('error')];
                        }
                    } else {
                        return ['status'=>0,'info'=>lang('error')];
                    }  
                }
            }else{
                return ['status'=>0,'info'=>lang('execution')];
            }
        }
    }


    /**
     * 添加更新数据
     * @param array $data [数据]
     * @return mixed
     */
    public function saveInfo($data)
    {
        $id = isset($data['id']);
        if($id){
            $where = true;
        }else{
            $where = false;
        }
        $result = $this->isUpdate($where)->save($data);
        if ($result) {
            return ['status'=>1,'info'=>lang('success')];
        } else {
            return ['status'=>0,'info'=>lang('error')];
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
