<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/22
 * Time: 16:29
 */

namespace app\api\model;


use think\Exception;
use think\Model;

class GuessAccount extends Model
{
	/**
	 * @param $password 支付密码
	 * @param $number 交易数量
	 * @param $user 用户信息
	 * @return array
	 * @throws Exception
	 * @throws \think\exception\DbException
	 */
	public function exchange($direction,$password,$number,$user)
	{
//		验证用户交易密码
		if($user['payment_password'] != encrypt($password)){
			throw new Exception('not_password');
		}
//		获取用户信息
		$guessaccount = $this->get(['uid'=>$user['id']]);
//      充值
		if($direction < 0){
			$guessaccount->blance -= $number;
//		提现
		}else{
			if($guessaccount->blance < $number){
				throw new Exception('low_blance');
			}
			$guessaccount->blance+=$number;

		}
		if(!$guessaccount->save()){
			throw new Exception('os_error');
		}else{
			return ['code'=>1,'msg'=>'os_success'];
		}
	}

	/**
	 * 押注扣取压住费用加手续费
	 * @param $user 用户信息
	 * @param $number 押注数量
	 * @throws Exception
	 * @throws \think\exception\DbException
	 */
	public function bet_fee($user,$number)
	{
		$GuessAccount = $this->get(['uid'=>$user['id']]);
		if($GuessAccount['blance'] < $number){
			throw new Exception('low_blance');
		}
		$GuessAccount['blance'] -=$number;
		if(!$GuessAccount->save()){
			throw new Exception('os_error');
		}
	}

	/**
	 * 获取用户的账户信息
	 * @param $user 用户信息
	 * @return array
	 * @throws \think\exception\DbException
	 */
	static public function get_guess_account($user)
	{
		$guess_account = self::get(['uid'=>$user['id']]);
		if(!$guess_account){
			return ['code'=>-1];
		}else{
			return ['code'=>1,'data'=>$guess_account];
		}
	}

}