<?php
namespace app\api\controller;
use think\Controller;
class Index extends controller {

    public function index() {
        echo config('WEB_SITE_NAME').'项目API接口目录';
    }
}