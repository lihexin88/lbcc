<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/27
 * Time: 17:13
 */

namespace app\api\model;


use think\Model;

class Currency extends Model
{
	/**
	 * 根据币种id获取币种名字
	 * @param $id 币种id
	 * @return mixed
	 * @throws \think\exception\DbException
	 */
	static public function get_name_by_id($id)
	{
		$name = self::get(['id'=>$id]);
		return $name['name'];
	}

    /**
     * @param $id
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function get_cur_text($id){
        return self::get_name_by_id($id);
    }
}