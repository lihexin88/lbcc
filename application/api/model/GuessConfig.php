<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/24
 * Time: 9:32
 */

namespace app\api\model;


use think\Exception;
use think\Model;

class GuessConfig extends Model
{
	/**
	 * 获取当前期号
	 * @return mixed
	 * @throws \think\exception\DbException
	 */
	static public function ThisTeam()
	{
		$team = self::get(['status'=>1]);
		return $team['id'];
	}

	/**
	 * 开奖、生成下一期信息
	 */
	static public function lottery()
	{
//      获取本期期号
		$this_team = self::get(['status'=>1]);

//		统计总投注数
		$this_team_recode = GuessRecode::get_this_recode($this_team);
		$red = 0;
		$blue = 0;
		foreach ($this_team_recode as $k=>$v){
			if($v['dir'] == 0){
				$red+=$v['number'];
			}else{
				$blue+=$v['number'];
			}
		}
//		更新竞猜记录表
		GuessRecode::lottery($this_team,$red>$blue?1:0);

//      中奖的用户增加lbcc
		$this_team_recode = GuessRecode::get_this_recode($this_team);
		$right_account = new GuessAccount();
		foreach ($this_team_recode as $k=>$v){
			if($v['right'] == 1){
				$right_account->right_income($v['uid'],$v['number']);
			}
		}

//		更新当前期号，并生成下一期信息
		if(!self::where('1=1')->update(['status'=>0])){
			throw new Exception('os_erraaaaaor');
		}
		$new_team = new self();
		$new_team['status'] = 1;
		if(!$new_team->save()){
			throw new Exception('os_eraaror');
		}
		return true;
	}
}