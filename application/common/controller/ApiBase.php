<?php
/**
 * api接口基类
 */
namespace app\common\controller;
use app\common\controller\Base;
use think\Db;
class ApiBase extends Base
{
    public $userInfo;
    public function _initialize()
    {
        parent::_initialize();
	    if(cookie('think_var') == 'zh-cn'){
		    config('THINK_VAR','');
	    }else{
		    config('THINK_VAR','en_');
	    }
         $map['token'] = input('token');
         $map['time_out'] = ['egt',time()];
         $token_user = db('user')->where($map)->find();
         if(!$token_user){
             echo rtn(1,lang('login_state')); exit;
         }else{
             $data['time_out'] = strtotime("+2 days");
             db('user')->where('token',input('token'))->update($data);
             $this->userInfo = db('user')->where('id',$token_user['id'])->find();
         }
    }
}

