<?php
namespace app\admin\controller;

use app\common\controller\AdminBase;
use think\Request;
use think\Db;

class Trade extends Admin
{
    /**
     * 列表
     * @param  integer $p 页码
     */
    public function index($p = 1)
    {
        $map = [];
         if (is_numeric(input('get.uid'))) {
            $map['uid'] = input('get.uid');
        }
         if (is_numeric(input('get.trade_type'))){
            $map['trade_type'] = input('get.trade_type');
        }
        if (is_numeric(input('get.trade_status'))){
            $map['trade_status'] = input('get.trade_status');
        }
        if (is_numeric(input('get.money_type'))){
            $map['money_type'] = input('get.money_type');
        }
        $this->assign("info", model('Trade')->infoList($map, $p));
        $this->assign("trade_type", model("Common/Dict")->showList('trade_type'));//状态
        $this->assign("trade_status", model("Common/Dict")->showList('trade_status'));
        $this->assign("money_type", model("Common/Dict")->showList('money_type'));
        $this->assign("userlist", model("User")->showList());

        return $this->fetch();
    }

    /**
     * 修改信息
     * @param  string $id ID
     */
    public function edit($id)
    {
        if (Request::instance()->isPost()) {
            return json(model('Trade')->changeState(input('post.')));
        }
    }
}