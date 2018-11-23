<?php
namespace app\api\Controller;
use app\common\controller\ApiBase;
use think\Session;
use think\Request;
use think\Captcha;
use think\Db;


class LockPosition extends ApiBase
{
	private $LockPosition;
    public function __construct(\think\Request $request = null) 
    {
        parent::__construct($request);
        $this->LockPosition = new \app\api\model\LockPosition();
    }
/**
 * 锁仓
 *
 * @remark 
 */
	public function look_position()
	{
		// $number = trim(input('number'));
		// $pwd = trim(input('pwd'));
		// $res = $this->userInfo;//用户信息
		// $map['uid'] = $res['id'];//用户ID
		// $map['cur_id'] = 1;//LBCC ID 1
		// $user_number = db('user_cur')->where($map)->value('number');//用户可用虚拟币数量
		// if(encrypt($pwd) !== $res['payment_password']){
		// 	$r = $this->rtn(1,lang('not_password'));
		// }elseif($number > $user_number){
		// 	$r = $this->rtn(1,lang('not_numebr'));
		// }else{
			$config = model('Common/config')->getConfig();
			dump($config);exit;
		// 	$situation = $this->LockPosition->implement($res['uid'],$number);
		// }
	}
}