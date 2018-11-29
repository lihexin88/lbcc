<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/23
 * Time: 15:11
 */

namespace app\api\model;


use think\Db;
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


	public function create_order($user,$number,$bet_dir,$team,$chip)
	{
//		判断押注时间 9:00~21:00
		$now = time();
		$today_start = strtotime(date("Y-m-d",time())) + 9 * 60 * 60;
		$today_end = strtotime(date("Y-m-d",time())) + 21 * 60 * 60;
		if(($now < $today_start) || ($now > $today_end)){
			return rtn(-1,lang('not_game_time'));
		}

//		获取本期是否有投注
		$Recode = self::get(['team'=>$team,'uid'=>$user['id']]);
//		    本期已经投注，只能进行加注
		if($Recode){
//			投注期号锁定
			if($Recode['team']!= $team){
				throw new Exception('wrong_team');
			}
//			加注
			$Recode['number']+=$number;
//			数量范围$chip['min'] ~ $chip['max']
			if(!($Recode['number'] < $chip['max'] && $Recode['number'] > $chip['min']) ){
				throw new Exception('number_error');
			}
//			方向锁定
			if($Recode['dir'] != $bet_dir){
				throw new Exception('wrong_dir');
			}
//          数据更新
			if(!$Recode->save()){
				throw new Exception('os_error');
			}
		}else{
			$GuessRecode['team'] = $team;
			$GuessRecode['uid'] = $user['id'];
			$GuessRecode['dir'] = $bet_dir;
			$GuessRecode['number'] = $number;
			$GuessRecode['create_time'] = time();
			if(!$this->save($GuessRecode)){
				throw new Exception('os_error');
			}
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

	/**
	 * 本期所有投注情况
	 * @param $team 本期期号
	 * @return array 本期所有投注
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	static public function get_this_recode($team)
	{
		$guess_recode = self::where(['team'=>$team['id']])->select()->toArray();
		return $guess_recode;
	}

	/**
	 * 开奖
	 * @param $this_team 当前期号
	 * @param $dir 获奖方向（红蓝）
	 * @throws Exception
	 */
	static public function lottery($this_team,$dir)
	{
		$where_array['team'] = array('eq',$this_team['id']);
		$where_array['dir'] = array('eq',$dir);
		self::where($where_array)->update(['right'=>1,'announce'=>1]);
		$where_array['dir'] = array('neq',$dir);
		self::where($where_array)->update(['right'=>0,'announce'=>1]);
	}

	/**
	 * 获取当天中奖记录的手机号和中奖金额
	 * @return false|\PDOStatement|string|\think\Collection
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	static public function get_daily_recode()
	{
		$page_size = 15;
		$today = strtotime(date("Y-m-d"),time());
		$tomorrow = $today+24*60*60;
		$where['r.update_time'] = array('between',$today.','.$tomorrow);
		$where['r.right'] = 1;
		$Recode = Db::table('sn_guess_recode')->alias('r')->where($where)->join('user u','r.uid = u.id')->field('u.account,r.number')->paginate($page_size);
		return $Recode;
	}

	/**
	 * 后台获取用户竞猜信息
	 * @param $info
	 * @return mixed
	 * @throws \think\exception\DbException
	 */
	static public function get_all($info)
	{
			$where['u.account'] = $info['keywords'];
			if($where['u.account'] == null){
				unset($where['u.account']);
			}
			$where['r.announce'] = $info['status'];
			if(($where['r.announce'] == 3)||($where['r.announce'] == null)){
				unset($where['r.announce']);
			}
			$pagesize = 15;
			$recode['data'] = self::alias('r')->join('user u','r.uid = u.id')->where($where)->paginate($pagesize);
			$recode['page'] = $recode['data']->render();
			$recode['count'] = count($recode['data']);
			return $recode;
	}
}