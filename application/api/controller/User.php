<?php
namespace app\api\Controller;
use app\api\model\BonusList;
use app\api\model\Config;
use app\api\model\GuessAccount;
use app\api\model\GuessOrder;
use app\api\model\GuessRecode;
use app\api\model\GuessConfig;
use app\api\model\Order;
use app\api\model\Trade;
use app\api\model\UserAuth;
use app\api\model\UserCur;
use app\api\model\User as UserModel;

use app\common\controller\ApiBase;


use think\Validate;
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
	 * @return \think\response\Json
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
		$type = null;
		if(!in_array($direction,['1','-1'])){
			return rtn(-1,lang('os_error'));
		}
		if($direction > 0){
			$type = 1;
		}else{
			$type = 2;
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
//    	获取下注范围
	    $chip = Config::chip_range();

	    if(!in_array($bet_dir,['0','1'])){
			return rtn(-1,lang('os_error'));
	    }
	    if(encrypt($_POST['payment_password'])!=$this->userInfo['payment_password']){
	    	return rtn(-1,lang('not_password'));
	    }
	    Db::startTrans();
	    try{

//			账户操作、扣除竞猜资金
		    $GuessAccount = new GuessAccount();
		    $GuessAccount->bet_fee($this->userInfo,$number);

//		    获取期号
		    $team = GuessConfig::ThisTeam();

//          写入竞猜表
		    $GuessRecode = new GuessRecode();
		    $GuessRecode->create_order($this->userInfo,$number,$bet_dir,$team,$chip);

//		    写入订单表
		    $GuessOrder = new GuessOrder();
		    $GuessOrder->create_order(3,$number,$this->userInfo);

	    	Db::commit();
			return rtn(1,lang('os_success'));
	    }catch (\Exception $e){
			Db::rollback();
			$errors = $e->getMessage();
//			超出最大数量提示剩余数量
			if($errors == 'number_max'){
				$user = GuessRecode::get(['team'=>$team,'uid'=>$this->userInfo['id']]);
				$max_number = Config::chip_range();
				$max_number = $max_number['max'];
				$number_left = $max_number - $user['number'];
				return rtn(-1,lang('number_max').' '.$number_left);
			}else{
				return rtn(-1,lang($errors));
			}
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
		}else if($guess_account['code'] == 2){
            return rtn(-1,lang('guess_account_init'));
        }else{
			return rtn(1,lang('guess_account_success'),$guess_account);
		}
    }



	/**
	 * @param $type 1、身份证正背面 2、微信二维码 3、支付宝二维码
	 * 上传图片 ,将url返回给前台
	 * @return \think\response\Json
	 */
	public function upload_pic(){
			$pic_type = null;
			if($_POST['type'] == 1){
				$pic_type = 'id_pic/';
			}else if($_POST['type'] == 2){
				$pic_type = 'wechat/';
			}else if($_POST['type'] == 3){
				$pic_type = 'alipay/';
			}
			$file = request() -> file('file');
			if($file){
				$info = $file -> move(ROOT_PATH . 'public' . DS . 'upload/user_auth/'.$pic_type ,true,true,2);
				if($info){
					$link = '/upload/user_auth/' .$pic_type. $info -> getSaveName();
					$ret = ['code' => 1,'msg' => '上传成功!','url' => $link];
				}else{
					$ret = ['code' => 0,'msg' => $file -> getError()];
				}
			}else{
				$ret = ['code' => 0,'msg' => '未上传!'];
			}
		return json($ret);
	}

	/**
	 *
	 * @paran $name 用户姓名
	 * @paran $id_number 用户身份证号
	 * @param $id_f_url 身份证正面url
	 * @param $id_b_url 身份证背面url
	 * @paran bank_card 银行卡号
	 * @paran 银行卡开户姓名
	 * @return false|string
	 */
	public function auth_real_info()
	{
//		验证用户信息完整性
		if(!$_POST){
			return rtn(-1,'not_null');
		}
		$data = null;
		$data['name'] = $_POST['name'];
		$data['id_number'] = $_POST['id_number'];
		$data['bank_card'] = $_POST['bank_card'];
		$data['id_f_url'] = $_POST['id_f_url'];
		$data['id_b_url'] = $_POST['id_b_url'];
		$data['take_bank_name'] =$_POST['take_bank_name'];
//		pre($data);
//		exit;
		foreach ($data as $k=>$v){
//			print_r($v);
			if(!$v){
				return rtn(-1,lang('os_error'));
			}
		}
//		修改用户认证信息
		Db::startTrans();
		try{
			$User = new UserAuth();
			$result = $User->user_auth($this->userInfo,$data);
			Db::commit();
			return rtn(1,lang($result));
		}catch (\Exception $e){
			Db::rollback();
			return rtn(-1,lang('error'));
		}
	}

	/**
	 * 查询用户认证信息
	 * @param $token
	 * @return false|string
	 * @throws \think\exception\DbException
	 */
	public function get_auth_info()
	{
		$user = $this->userInfo;
		$AuthInfo = UserAuth::get(['uid'=>$user['id']]);
		if($AuthInfo){
            foreach ($AuthInfo as $k=>$v){
                if($v['status'] == 1){
                    $AuthInfo[$k]['status'] = lang('passed');
                }else if($v['status'] == 2){
                    $AuthInfo[$k]['status'] = lang("auth_rejected");
                }else{
                    $AuthInfo[$k]['status'] = lang("not_auth");
                }
            }
			return rtn(1,lang('success'),$AuthInfo);
		}else{
			return rtn(1,lang('success'),lang('暂无用户认证信息'));
		}
	}

	/**
	 * 修改密码 1、发送验证码
	 * @param $token
	 * @param $secret_key 密钥
	 * @param $type 1、修改登录密码 2、修改交易密码
	 */
	public function send_phone_code()
	{
		$user = $this->userInfo;
//		验证私钥
		if($user['secret_key'] !=$_POST['secret_key']){
			return rtn(-1,lang('wrong_secret'));
		}
//		验证修改类型
		if(!in_array($_POST['type'],['1','2'])){
			return rtn(-1,lang('os_error'));
		}
		$code = generate_code(6);
		Session::set('authcode', ['code' => $code, 'account' => $user['id'],'type'=>$_POST['type'],'auth'=>false]);

		//  短信接口
		/***********
		 *   Ａ    *
		 *   Ｐ    *
		 *   Ｉ    *
		 **********/

		return rtn(1,lang('code_sent'),$_SESSION);
	}

	/**
	 * 修改密码 2、验证验证码
	 * @param $phone_code 验证码
	 * @param $token
	 */
	public function confirm_code()
	{
//		验证传入数据
		if(!$_POST){
			return rtn(-1,lang('os_error'));
		}
//		验证验证码
		if(!($_POST['phone_code'] == $_SESSION['think']['authcode']['code'])){
			return rtn(-1,lang('phone_error'));
		}
        $user = $this->userInfo;
        $typeCode = input('typeCode');
        db('phone_code')->where('phone',$user['account'])->where('type',$typeCode)->delete();
		$_SESSION['think']['authcode']['auth'] = true;
		return rtn(1,lang('os_success'),$_SESSION);
	}

	/**
	 * 修改密码
	 */
	public function change_password()
	{
		if(!$_POST){
			return rtn(-1,lang('os_error'));
		}
		if(!$_SESSION['think']['authcode']['auth']){
			return rtn(-1,lang('phone_error'));
		}
		$password = $_POST['password'];
		$re_password = $_POST['re_password'];
		if($password != $re_password){
			return rtn(-1,lang('pwd_diffent'));
		}
//		验证登录密码
		if($_SESSION['think']['authcode']['type'] == 1){
				//验证规则
				$validate_rule['password'] = 'length:8,20|alphaNum';

				//验证提示
				$validate_msg['password.length'] = lang('pwd_type');
				$validate_msg['password.alphaNum'] = lang('pwd_type');
				$validate = new Validate($validate_rule,$validate_msg);

				//验证数据
				$validate_data['password'] = $password;
				if (!$validate->check($validate_data)) {
					return rtn(-1,$validate->getError());
				}
//		验证支付密码
		}else{
			//验证规则
			$validate_rule['payment_password'] = 'length:6|number';

			//验证提示
			$validate_msg['payment_password.length'] = lang('pay_pwd_type');
			$validate_msg['payment_password.number'] = lang('pay_pwd_type');

			//验证数据
			$validate_data['payment_password'] = $password;
			$validate = new Validate($validate_rule,$validate_msg);
			if (!$validate->check($validate_data)) {
				return rtn(-1,$validate->getError());
			}
		}
		try{
			$User = new UserModel();
			$User->change_passowrd($this->userInfo,$password);
		}catch (\Exception $e){
			return rtn(-1,lang($e->getMessage()));
		}
		return rtn(1,lang('password_changed'));
	}

	/**
	 *
	 * 我的邀请
	 * @param $token
	 */
	public function invitation()
	{
		$web_url = Config::get(['key'=>'WEBSITE']);
		$user = $this->userInfo;
		$Invitation['img'] = $web_url['value'].$user['invitation_img'];
		$Invitation['inv_code']  = $user['invitation_code'];
		return rtn(1,lang('os_success'),$Invitation);
	}

	/**
	 * 邀请返佣
	 * @return false|string
	 */
	public function invitation_reword()
	{
		$BonusList = new BonusList();
		$reword = $BonusList->reword($this->userInfo);
		return rtn(1,lang('os_success'),$reword);
	}

	/**
	 * 我的好友 一
	 * @param $token
	 */
	public function my_friends()
	{
		$User = new UserModel();
		$friends = $User->my_friends($this->userInfo);
		return rtn(1,lang('os_success'),$friends);
	}

	/**
	 * 获取用户交易订单
	 * @return false|string
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	public function get_trade_order()
	{
	    if(!$_POST['status']){
            return rtn(0,lang('not_null'));
        }
		$Trade = new Trade();
		$trade = $Trade->get_trade($this->userInfo,$_POST['status']);
		return rtn(1,lang('os_success'),$trade);
	}


	/**
	 * 取消订单
	 * @return false|string
	 */
	public function cancel_trade()
	{
        if(!$_POST['order']){
            return rtn(0,lang('os_success'));
        }
        $Trade = new Trade();
        if(!$Trade->cancel_trade($_POST['order'],$this->userInfo)){
            return rtn(0,lang('os_error'));
        }
        return rtn(1,lang('os_success'));

	}


	/**
	 * 返回“我的”页面 用户信息
	 * @return false|string
	 * @throws \think\exception\DbException
	 */
	public function user_head_info()
	{
		$user_info['user_info'] = UserCur::get(['uid'=>$this->userInfo['id'],'cur_id'=>1]);
		if(!$user_info){
			return rtn(-1,lang('info_cant_find'));
		}
		$user_info['user_info']['account'] = $this->userInfo['account'];
		$user_info['version'] = Config::get(['key'=>'SYS_VERSION']);
		return rtn(1,lang('os_success'),$user_info);
	}

	/**
	 * 退出登录
	 * @return false|string
	 */
	public function logout()
	{
		$User = new UserModel();
		if($User->logout($this->userInfo)){
			return rtn(1,lang('logout'));
		}else{
			return rtn(-1,lang('os_error'));
		}

	}


}