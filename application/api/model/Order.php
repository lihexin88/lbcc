<?php
namespace app\api\model;

use think\Exception;
use think\Model;

class Order extends Model
{
    // 关闭自动写入时间戳
    protected $autoWriteTimestamp = false;

    /**
     * model 查看某一币种最后一笔成交订单的价格
     */
    public function last_order_price($order_status,$cur_id){
        $order_price_where['order_status'] = $order_status;
        $order_price_where['cur_id'] = $cur_id;
        $price = $this -> where($order_price_where) -> order('id DESC') -> value('price');
        return $price;
    }

    /**
     * model 查看某一交易区最后一笔成交订单的价格
     */
    public function last_order_area_price($order_status,$cur_area_id){
        $order_where['order_status'] = $order_status;
        $order_where['cur_area_id'] = $cur_area_id;
        $price = $this -> where($order_where) -> order('id DESC') -> value('price');
        return $price;
    }


	/**
	 * 撤销订单
	 * @param $order_number 订单号
	 * @param $user 用户信息
	 * @return bool
	 * @throws Exception
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
    public function cancel_trade($order_number,$user)
    {
		$order = self::where('order_number='.$order_number.' AND (seller_id='.$user['id'].' OR trade_id='.$user['id'].')')->find();
		if(!$order){
			throw new Exception('info_cant_find');
		}
		$order->order_status = 4;
		if(!$order->save()){
			throw new Exception('os_error');
		}
		return true;
    }

}
