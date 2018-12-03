<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/23
 * Time: 15:32
 */

namespace app\api\model;


use think\Model;

class Config extends Model
{
	/**
	 * 关于我们
	 * @return null
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	static public function about_us()
	{
//		查询about_us 的信息
		$about_us_where['key'] = array('like','ABOUT_US%');
		$about_us = self::where($about_us_where)->select();

		$about_us_key = array('ABOUT_US_TEXT','ABOUT_US_QQ','ABOUT_US_TEL','ABOUT_US_WECHAT');
		$about_us_content = null;
		foreach ($about_us as $k=>$v){
			if(in_array($v['key'],$about_us_key)){
//				循环赋值
				$about_us_content[$v['key']] = $v['value'];
			}
		}
		return $about_us_content;
	}

	/**
	 * 游戏手续费
	 * @return mixed
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	static public function game_fee()
	{
		$fee = self::where(['key'=>'GUESS_BET_FEE'])->field('value')->find();
		return $fee['value'];
	}

	/**
	 *
	 * 下注范围
	 * @return mixed
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	static public function chip_range()
	{
		$min = null;
		$max = null;

		$min = self::where(['key'=>'GAME_MIN_CHIP'])->field('value')->find();
		$max = self::where(['key'=>'GAME_MIN_CHIP'])->field('value')->find();

		$chip['min'] = $min['value'];
		$chip['max'] = $max['value'];
		return $chip;
	}


	/**
	 * 注册协议
	 * @return Config|null
	 * @throws \think\exception\DbException
	 */
	static public function reg_protocol()
	{
		$reg_pro = self::get(['key'=>'REGISTER_PROTOCLO']);
		return $reg_pro;
	}
}