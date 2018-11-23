<?php
namespace app\api\Controller;
use app\api\model\Config;
use app\api\model\GuessAccount;
use app\api\model\GuessOrder;
use app\api\model\GuessRecode;
use app\api\model\UserCur;
use app\common\controller\ApiBase;
use think\Exception;
use think\Session;
use think\Request;
use think\Captcha;
use think\Db;

/**
 * 我的页面
 *
 * @remark 
 */


class User extends ApiBase
{
    public function _initialize() 
    {
        parent::_initialize();
    }

    /**
     * 我的
     * @param string @uid [用户ID]
     */
    public function userPage()
    {
        $id = trim(input('uid'));
        if(!$id) {
            $r = $this->rtn(-1,lang("parameter"));
        }else{
            $map = ['id'] == $id;
            if(false == ($data = model('User')->userPage($id))){
                $r = $this->rtn(-1,lang("null"));
            }else{
                $r = $this->rtn(0,lang("success"),$data);
            }  
        }
        return json($r);
    }

    /**
     * 判断是否填写手机号
     * @param string @uid [用户ID]
     */
    public function isExistTel()
    {
        $id = trim(input('uid'));
        if(!$id) {
            $r = $this->rtn(-1,lang("parameter"));
        }else{
            $map = ['id'] == $id;
            if(false == ($data = model('User')->isExistTel($id))){
                $r = $this->rtn(-1,lang("null"));
            }else{
                $r = $this->rtn(0,lang("success"),$data);
            }  
        }
        return json($r);
    }

    /**
     * 验证老手机号发送验证码
     * @param string @uid [用户ID]
     * @param string @tel [手机号]
     * @param string @code [验证码]
     */
    public function checkOldTel()
    {
        $id = trim(input('uid'));
        $tel = trim(input('tel'));
        $code = input('code');
        if(!$id) {
            $r = $this->rtn(-1,lang("parameter"));
        }else if(!$tel){
            $r = $this->rtn(-1,lang("cont_empty"));
        }else if(!$code){
            $r = $this->rtn(-1,lang("cont_empty"));
        }else{
            $data = model('User')->checkOldTel($id,$tel,$code);
            if($data['status'] == 0){
                $r = $this->rtn(-1,lang("error"),$data['info']);
            }else{
                $r = $this->rtn(0,lang("success"));
            }  
        }
        Session::delete('authcode');
        return json($r);
    }

    /**
     * 修改新手机号码
     * @param string @uid [用户ID]
     * @param string @tel [手机号]
     * @param string @code [验证码]
     */
    public function saveTel()
    {
        $id = trim(input('uid'));
        $tel = trim(input('tel'));
        $code = input('code');
        if(!$id) {
            $r = $this->rtn(-1,lang("parameter"));
        }else if(!$tel){
            $r = $this->rtn(-1,lang("cont_empty"));
        }else if(!$code){
            $r = $this->rtn(-1,lang("cont_empty"));
        }else{
            $data = model('User')->saveTel($id,$tel,$code);
            if($data['status'] == 0){
                $r = $this->rtn(-1,lang("error"),$data['info']);
            }else{
                $r = $this->rtn(0,lang("success"),$data['info']);
            }  
        }
        Session::delete('authcode');
        return json($r);
    }

    /**
     * 修改姓名
     * @param string @uid [用户ID]
     * @param string @username [姓名]
     */
    public function saveUsername()
    {
        $map['id'] = trim(input('uid'));
        $map['username'] = trim(input('username'));
        if(!$map['id']) {
            $r = $this->rtn(-1,lang("parameter"));
        }else if(!$map['username']){
            $r = $this->rtn(-1,lang("cont_empty"));
        }else{
            $data = model('User')->saveInfo($map);
            if($data['status'] == 0){
                $r = $this->rtn(-1,lang("error"),$data['info']);
            }else{
                $r = $this->rtn(0,lang("success"),$data['info']);
            }  
        }
        return json($r);
    }

    /**
     * 实名认证
     * @param string @uid [用户ID]
     * @param string @real_name [真实姓名]
     * @param string @identity [身份证号]
     */
    public function checkIdentity()
    {
        $id = trim(input('uid'));
        $real_name = trim(input('real_name'));
        $identity = trim(input('identity'));
        if(!$id) {
            $r = $this->rtn(-1,lang("parameter"));
        }else if(!$real_name){
            $r = $this->rtn(-1,lang("cont_empty"));
        }else if(!$identity){
            $r = $this->rtn(-1,lang("cont_empty"));
        }else{
            $checkIdentity = validation_filter_id_card($identity);
            if($checkIdentity){
                $map['id'] = $id;
                $map['identity'] = $identity;
                $map['real_name'] = $real_name;
                $result = model('User')->saveInfo($map);
                if($result['status'] == 0){
                    $r = $this->rtn(-1,lang("error"),$result['info']);
                }else{
                    $r = $this->rtn(0,lang("success"));
                } 
            }else{
                $r = $this->rtn(-1,lang('idcard_error'));
            }
        }
        return json($r);
    }


    /**
     * 修改一级密码
     * @param string $uid [用户ID]
     * @param string $old_pwd [原密码]
     * @param string $new_pwd [新密码]
     * @param string $re_pwd [重复新密码]
     * @param string $type [修改密码类型：1修改一级密码，2修改二级密码]
     */
    public function changefirstPwd()
    {
        $uid = trim(input('uid'));
        $old_pwd = trim(input('old_pwd'));
        $new_pwd = trim(input('new_pwd'));
        $re_pwd = trim(input('re_pwd'));
        $type = trim(input('type'));

        if(!$uid) {
            $r = $this->rtn(-1,lang("parameter"));
        }else if(!$old_pwd) {
            $r = $this->rtn(-1,lang("cont_empty"));
        }else if(!$new_pwd) {
            $r = $this->rtn(-1,lang("cont_empty"));
        }else if(!$re_pwd) {
            $r = $this->rtn(-1,lang("cont_empty"));
        }else if($old_pwd == $new_pwd){
            $r = $this->rtn(-1,lang('pwd_same'));
        }else if($new_pwd != $re_pwd){
            $r = $this->rtn(-1,lang('pwd_diffent'));
        }else if(!$type){
            $r = $this->rtn(-1,lang("parameter"));
        }else{
            if(false == model('User')->changefirstPwd($uid,$old_pwd,$new_pwd,$type)){
                $r = $this->rtn(-1,lang("error"));
            }else{
                $r = $this->rtn(0,lang("success"));
            }  
        }
        return json($r);
    }

    /**
     * 修改支付宝
     * @param string $uid [用户ID]
     * @param string $alipay_accout [支付宝账号]
     */
    public function editAlipay()
    {
        $id = trim(input('uid'));
        $alipay_accout = trim(input('alipay_accout'));

        if(!$id) {
            $r = $this->rtn(-1,lang("parameter"));
        }else if(!$alipay_accout) {
            $r = $this->rtn(-1,lang("cont_empty"));
        }else{
            $map['id'] = $id;
            $map['alipay_accout'] = $alipay_accout;
            $result = model('User')->saveInfo($map);
            if($result['status'] == 0){
                $r = $this->rtn(-1,lang("error"));
            }else{
                $r = $this->rtn(0,lang("success"));
            }  
        }
        return json($r);
    }

    /**
     * 绑定银行卡
     * @param string $uid [用户ID]
     * @param string $bank_user [开户名]
     * @param string $bank [开户银行]
     * @param string $branch_bank [支行名称]
     * @param string $bank_number [银行卡号]
     */
    public function editBank()
    {
        $id = trim(input('uid'));
        $bank_user = trim(input('bank_user'));
        $bank = trim(input('bank'));
        $branch_bank = trim(input('branch_bank'));
        $bank_number = trim(input('bank_number'));
        if(!$id) {
            $r = $this->rtn(-1,lang("parameter"));
        }else if(!$bank_user) {
            $r = $this->rtn(-1,lang("cont_empty"));
        }else if(!$bank) {
            $r = $this->rtn(-1,lang("cont_empty"));
        }else if(!$branch_bank) {
            $r = $this->rtn(-1,lang("cont_empty"));
        }else if(!$bank_number) {
            $r = $this->rtn(-1,lang("cont_empty"));
        }else{
            $map['id'] = $id;
            $map['bank_user'] = $bank_user;
            $map['bank'] = $bank;
            $map['branch_bank'] = $branch_bank;
            $map['bank_number'] = $bank_number;
            $result = model('User')->saveInfo($map);
            if($result['status'] == 0){
                $r = $this->rtn(-1,lang("error"));
            }else{
                $r = $this->rtn(0,lang("success"));
            }  
        }
        return json($r);
    }

    /**
     * 每天收益
     */
    public function dailyEarnings()
    {
        if((date('w') == 6) || (date('w') == 0)){
            $r = $this->rtn(-1,lang('weekend'));
        }else{
            $list = [];
            $holiday = Db::name('holiday')->select();
            foreach ($holiday as $k => $v) {
                $list[] = $v['holiday'];
            }
            $today = date('Y-m-d',time());
            if(in_array($today,$list)){
                $r = $this->rtn(-1,lang('holiday'));
            }else{
                $result = model('User')->dailyEarnings();
                if(!$result){
                    $r = $this->rtn(-1,lang("error"));
                }else{
                    $r = $this->rtn(0,lang("success"));
                } 
            }
        }
        return json($r);
    }

    /**
    *记录每天的收益率
    */
    public function profitrate()
    {
        $data = ['profit_rate' => config('RATE_OF_RETURN'), 'time' => time()];
        db('Profitrate')->insert($data);
        return json($this->rtn(0,lang("success")));
    }

	/**
	 * 获取有奖竞猜风险提示
	 * @return \think\response\Json
	 */
    public function guess_warning()
    {
//    	网站文档
    	$data = [
    		'id'=>2
	    ];
	    $file = db('file')->where($data)->value(config("THINK_VAR").'content');
	    if($file){
		    return rtn(1,lang("success"),$file);
	    }else{
	        return rtn(-1,lang("cant get web file"));
	    }
    }

	/**
	 * 用户竞猜充值提现
	 * @param $password 支付密码
	 * @param $number 交易数量
	 * @param $direction 交易方向 1、竞猜充值 -1 竞猜提现
	 * @param $cookie cookie信息
	 * @return false|string
	 */
    public function guess_exchange()
    {
    	$data = $_POST;
		$password = $data['password'];
		$number = $data['charge_number'];
		$direction = $data['direction'];
		if(!in_array($direction,['1','-1'])){
			return rtn(-1,lang('os_error'));
		}
//		开启事务
		Db::startTrans();
		try{
//			账户操作
			$GuessAccount = new GuessAccount();
		    $GuessAccount->exchange($direction,$password,$number,$this->userInfo);

//			添加操作记录
			$GuessOrder = new GuessOrder();
			$GuessOrder->create_order($direction,$number,$this->userInfo);

//			用户lbcc操作
			$UserCur = new UserCur();
			$UserCur->update_user_cur($direction,1,$number,$this->userInfo);

			Db::commit();
			return rtn(1,lang('os_success'));
		}catch (\Exception $e){
			Db::rollback();
			return rtn(-1,lang($e->getMessage()));
		}

    }

	/**
	 * 竞猜押注
	 * @param $number 押注数量
	 * @param $dir 押注方向 0 红  1蓝
	 * @param cookie
	 * @return false|string
	 */
    public function guess_bet()
    {
    	$data = $_POST;
    	if(!$data){
    		return rtn(-1,lang('os_error'));
	    }
    	$number = intval($_POST['number']);
    	$bet_dir = $_POST['dir'];
    	if(!($number >0 && $number<2000)){
    		return rtn(-1,lang('number_error'));
	    }
	    if(!in_array($bet_dir,['0','1'])){
			return rtn(-1,lang('os_error'));
	    }
	    Db::startTrans();
	    try{
//	    	获取押注手续费
			$fee = Config::get(['key'=>'GUESS_BET_FEE']);
			$fee = 1 + $fee['value'];

//			账户操作、扣除竞猜资金
		    $GuessAccount = new GuessAccount();
		    $GuessAccount->bet_fee($this->userInfo,$number*$fee);

//          写入竞猜表
		    $GuessRecode = new GuessRecode();
		    $GuessRecode->create_order($this->userInfo,$number,$bet_dir);

	    	Db::commit();
			return rtn(1,lang('os_success'));
	    }catch (\Exception $e){
			Db::rollback();
			return rtn(-1,lang($e->getMessage()));
	    }
    }

	/**
	 *
	 * 获取用户押注记录
	 * @return false|string
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
    public function guess_recode()
    {
    	$guess_recode = GuessRecode::get_recode($this->userInfo);
    	return rtn(1,lang('succsee'),$guess_recode);
    }

	/**
	 * 获取用户竞猜账户信息
	 * @return false|string
	 * @throws \think\exception\DbException
	 */
    public function guess_account()
    {
    	$guess_account = GuessAccount::get_guess_account($this->userInfo);
		if($guess_account['code'] == -1){
			return rtn(-1,lang('guess_account_error'));
		}else{
			return rtn(1,lang('guess_account_success'),$guess_account);
		}
    }
   
}