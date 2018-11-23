<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/23
 * Time: 15:11
 */

namespace app\api\model;


use think\Exception;
use think\Model;

class GuessRecode extends Model
{
	/**
	 * 创建押注订单记录
	 * @param $user 用户信息
	 * @param $number 押注数量
	 * @param $bet_dir 押注方向 0 红 1 蓝
	 * @throws Exception
	 */
	public function create_order($user,$number,$bet_dir)
	{
		$GuessRecode['uid'] = $user['id'];
		$GuessRecode['dir'] = $bet_dir;
		$GuessRecode['number'] = $number;
		$GuessRecode['create_time'] = time();
		if(!$this->insert($GuessRecode)){
			throw new Exception('os_error');
		}
	}

	/**
	 * 静态方法，获取当前用户的竞猜记录
	 * @param $user 用户信息
	 * @return false|\PDOStatement|string|\think\Collection
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	static public function get_recode($user)
	{
		$guess_recode = self::where(['uid'=>$user['id']])->select();
		return $guess_recode;
	}
}