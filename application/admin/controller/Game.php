<?php
/**---------------------------|
 * Created by  '李'
 * User: Administrator
 * Date: 2018/11/28
 * Time: 17:40
 **---------------------------|
 */

namespace app\admin\controller;

use app\admin\model\GuessInfo;
use app\api\model\GuessConfig;
use app\api\model\GuessRecode;
use app\common\controller\AdminBase;
use think\Controller;

class Game extends AdminBase
{
	/**
	 * 获取中奖状态
	 * @return mixed
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	public function index()
	{
		$where = null;
		$where['keywords'] = $_GET['keywords']!=null?$_GET['keywords']:null;
		$where['status'] = $_GET['status']!=null?$_GET['status']:null;
		$recode = null;
		$Recode = new GuessRecode();
		$recode = $Recode->get_all($where);
		$this->assign('page',$recode['page']);
		$this->assign('all_recode',$recode['data']);
		$this->assign('count',$recode['count']);
		return $this->fetch();
	}

	/**
	 * 获取系统开奖记录
	 * @return mixed
	 * @throws \think\exception\DbException
	 */
	public function config_recode()
	{
		$where = null;
		if($_GET['keywords']){
			$where['id'] = $_GET['keywords'];
		}
		$recode = GuessConfig::get_recode($where);
		$this->assign('all_recode',$recode['data']);
		$this->assign('page',$recode['page']);
		return $this->fetch();
	}

	public function game_info()
	{
		$guess_info = GuessInfo::get_info();
		$this->assign('list',$guess_info);
		return $this->fetch();
	}

}