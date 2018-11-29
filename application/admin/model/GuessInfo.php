<?php
/**---------------------------|
 * Created by  '李'
 * User: Administrator
 * Date: 2018/11/29
 * Time: 17:01
 **---------------------------|
 */

namespace app\admin\model;


use think\Model;

class GuessInfo extends Model
{
	/**
	 * 获取游戏公告等信息
	 * @return GuessInfo[]|false
	 * @throws \think\exception\DbException
	 */
	static public function get_info()
	{
		return self::all();
	}
}