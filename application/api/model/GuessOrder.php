<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/23
 * Time: 10:47
 */

namespace app\api\model;


use think\Exception;
use think\Model;

class GuessOrder extends Model
{
	/**
	 * @param $direction 方向 1、存入 -1、取出
	 * @param $number 数量
	 * @param $user 用户信息
	 * @return array
	 * @throws Exception
	 */
	public function create_order($direction,$number,$user)
	{
		$GuessOrder['uid'] = $user['id'];
		$GuessOrder['direction'] = $direction;
		$GuessOrder['number'] = $number;
		if(!$this->save($GuessOrder))
		{
			throw new Exception('recharge_failed');
		}
		return ['code'=>1,'msg'=>'gus_recharged'];
	}
}