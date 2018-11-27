<?php
namespace app\api\controller;
use app\api\model\Config;
use think\Controller;
class Index extends controller {

    public function index() {
        echo config('WEB_SITE_NAME').'项目API接口目录';
    }


	/**
	 * 关于我们
	 * @return false|string
	 */
    public function about_us()
    {
		$about_us = Config::about_us();
		return rtn(1,lang('os_success'),$about_us);
    }
}