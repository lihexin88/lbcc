<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/26
 * Time: 9:50
 */

namespace app\api\controller;


use app\api\model\GuessRecode;
use think\Controller;
use app\api\model\GuessConfig;
use think\Db;


class Game extends Controller
{
	/**
	 * 开奖、生成下一期信息
	 */
	public function lottery()
	{
		Db::startTrans();
		try{
			GuessConfig::lottery();
			Db::commit();
			return rtn(1,lang('os_success'));
		}catch (\Exception $e){
			Db::rollback();
			return rtn(-1,$e->getMessage());
		}
	}

	/**
	 * 获取当天所有用户获奖信息
	 */
	public function get_history()
	{
		$GuessRecode = GuessRecode::get_daily_recode();
		return rtn(1,lang('os_success'),$GuessRecode);
	}

}