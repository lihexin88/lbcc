<?php
/**---------------------------|
 * Created by  '李'
 * User: Administrator
 * Date: 2018/11/28
 * Time: 17:40
 **---------------------------|
 */

namespace app\admin\controller;

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
		$recode = null;
		$pagesize = 10;
		$type = $_POST==1?1:0;
		$Recode = new GuessRecode();
		$recode = $Recode->get_all($type);
		$this->assign('page',$recode['page']);
		$this->assign('all_recode',$recode['data']);
		return $this->fetch();
	}
}