<?php
namespace app\admin\controller;

use app\common\controller\AdminBase;
use think\Request;
use think\Db;
use app\admin\model\Trade as TradeModel;

class Trade extends Admin
{
    /**
     * 列表
     * @param  integer $p 页码
     */
    public function index(/*$p = 1*/)
    {
        $map = [];
         if (is_numeric(input('get.uid'))) {
            $map['uid'] = input('get.uid');
        }
         if (is_numeric(input('get.trade_type'))){
            $map['transaction_type'] = input('get.trade_type');
        }
        if (is_numeric(input('get.trade_status'))){
//            $map['trade_status'] = input('get.trade_status');
        }
        if (is_numeric(input('get.money_type'))){
            $map['cur_id'] = input('get.money_type');
        }


		$Trade = new TradeModel();
		$trade = $Trade->infoList($map);
        $this->assign("info", $trade);
//        print_r($trade);
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