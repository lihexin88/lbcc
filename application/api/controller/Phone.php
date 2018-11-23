<?php
namespace app\api\controller;
use think\Controller;
use think\Session;
use think\Request;
use think\Db;
use think\Validate;

/**
 * 首页信息登录注册页面
 *
 * @remark 获取验证码（code）、用户注册（userReg）、用户登录页面信息（LoginPage）、用户登录（userLogin）、首页信息（homePage）
 */


class Phone extends controller
{
    public function _initialize() 
    {
        parent::_initialize();
        if(cookie('think_var') == 'zh-cn'){
            config('THINK_VAR','');
        }else{
            config('THINK_VAR','en_');
        }
    }

    /*
    * 切换语言
    * @param  string @lang [语言类型：cn中文，en英文]
    */
    public function lang()
    {
        switch (input('lang')) {
            case 'cn':
                cookie('think_var','zh-cn');
                return rtn(0,'切换成功'); 
                break;
            case 'en':
                cookie('think_var','en-us');
                return rtn(0,'Handover success'); 
                break;
        }
    }

    /*
    * code 获取验证码
    * @param  string @account [账号]
    */
    public function code(){
        $account = input('post.account');
        if(!is_mobile($account)){
            return rtn('-1',lang("is_phone"));
        }else{      
            $code = generate_code(6);
            Session::set('authcode', ['code' => $code, 'account' => $account]);
            return rtn(0,lang("success"),session('authcode.code'));  
        } 
    }

    /**
     * 忘记密码发送验证码
     * @param  string @secret_key [密钥]
     */
    public function forgetPwdCode()
    {
        $secret_key = trim(input('secret_key'));
        if(!$secret_key) {
            return rtn(-1,lang("not_null"));
        }
        $account = db('user')->where('secret_key',$secret_key)->value('account');
        if(!$account){
            return rtn(-1,lang("account_exist"));
        }
        $code = generate_code(6);
        Session::set('authcode', ['code' => $code, 'account' => $account]);
        return rtn(0,lang("success"),session('authcode.code'));  
    }

    /**
     * 忘记密码
     * @param  string @secret_key [密钥]
     * @param  string @code [验证码]
     * @param  string @password [密码]
     * @param  string @repassword [重复密码]
     * @param  string @password_type [密码类型：1登录密码，2支付密码]
     */
    public function forgetPwd()
    {
        $secret_key = trim(input('secret_key'));
        $code = trim(input('code'));
        $password = trim(input('password'));
        $repassword = trim(input('repassword'));
        $password_type = trim(input('password_type'));
        
        if(!$secret_key || !$code || !$password || !$repassword || !$password_type) {
            return rtn(-1,lang("not_null"));
        }
        if($password != $repassword){
            return rtn(-1,lang("pwd_diffent"));
        }
        if($code != session('authcode.code')){
            return rtn(-1,lang("phone_error"));
        }
        if($password_type == 1){
            $data['password'] = encrypt($password);
        }else{
            $data['payment_password'] = encrypt($password);
        }
        $data['update_time'] = time();
        $result = db('User')->where('secret_key',$secret_key)->update($data);
        if($result === false){
            return rtn(-1,lang('error'));
        }else{
            Session::delete('authcode');
            return rtn(0,lang('success'));
        }
    }

    /*
    * 生成助记词
    */
    public function memorizingWords()
    {
        $memorizing_words .= memorizing_words();
        Session::set('memorizingwords', $memorizing_words);
        $arr = str_split($memorizing_words, 5);
        return rtn(0,lang("success"),$arr);  
    }

    /**
     * 用户注册
     * @param  string @account [账号]
     * @param  string @code [验证码]
     * @param  string @invitation_code [邀请码]
     * @param  string @password [密码]
     * @param  string @repassword [重复密码]
     * @param  string @payment_password [支付密码]
     * @param  string @repayment_password [重复支付密码]
     * @param  string @memorizing_words [助记词]
     */
    public function userReg()
    {
        $data= input('post.');
        if(!$data['account'] || !$data['code'] || !$data['invitation_code'] || !$data['password'] || !$data['repassword'] || !$data['payment_password'] || !$data['repayment_password'] || !$data['memorizing_words']){
            return rtn(-1,lang("not_null"));
        }else{
            if(preg_match("/^\d*$/",$data['password']) || preg_match("/^[a-z]*$/i",$data['password'])){
                return rtn(-1,lang('pwd_type'));
            }
            //验证规则
            $validate_rule['password'] = 'length:8,20|alphaNum';
            $validate_rule['payment_password'] = 'length:6|number';
            
            //验证提示
            $validate_msg['password.length'] = lang('pwd_type');
            $validate_msg['password.alphaNum'] = lang('pwd_type');
            $validate_msg['payment_password.length'] = lang('pay_pwd_type');
            $validate_msg['payment_password.number'] = lang('pay_pwd_type');
            $validate = new Validate($validate_rule,$validate_msg);

            //验证数据
            $validate_data['password'] = $data['password'];
            $validate_data['payment_password'] = $data['payment_password'];
            if (!$validate->check($validate_data)) {
                return rtn(-1,$validate->getError());
            }
            if($data['code'] == session('authcode.code')){
                if($data['account'] == session('authcode.account')){
                    if($data['memorizing_words'] == session('memorizingwords')){
                        if($data['password'] == trim(input('repassword'))){
                            if($data['payment_password'] == trim(input('repayment_password'))){
                                $return = model("User")->userReg($data);
                                if($return['status'] === 0) {
                                    return rtn(-1,$return['info']);
                                }else{
                                    Session::delete('authcode');
                                    Session::delete('memorizingwords');
                                    return rtn(0,$return['info']);   
                                }
                            }else{
                                return rtn(-1,lang("pwd_diffent"));
                            }
                        }else{
                            return rtn(-1,lang("pwd_diffent"));
                        }
                    }else{
                        return rtn(-1,lang("memorizing_words_error"));
                    }
                }else{
                    return rtn(-1,lang("phone_diffent"));
                }
            }else{
                return rtn(-1,lang("phone_error"));
            }
        }
    }

    /**
     * 用户登录
     * @param string @account [账号]
     * @param string @password [密码]
     */
    public function userLogin()
    {
        $account = trim(input('account'));
        $password = trim(input('password'));

        if(!$account || !$password) {
            return rtn(-1,lang("not_null"));
        }else{
            $return = model("User")->login($account,$password);
            if($return['status'] === 0) {
                return rtn(-1,$return['info']);
            }else{
                return rtn(0,lang("success"),$return['info']);   
            }
 
        }
    }

    /**
     * 网站文档
     * @param string @file_type [文档类型：1注册协议]
     */
    public function web_flie()
    {
        $data['file_type'] = trim(input('file_type'));

        if(!$data['file_type']) {
            return rtn(-1,lang("not_null"));
        }else{
            $file = db('file')->where($data)->value(config("THINK_VAR").'content');
            return rtn(0,lang("success"),$file);   
        }
    }
}