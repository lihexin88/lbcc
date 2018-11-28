<?php
namespace app\api\controller;
use app\api\model\Currency;
use app\common\controller\ApiBase;
use think\Session;
use think\Request;
use think\Db;
use app\api\model\Trade;
use app\api\model\CurrencyArea;

/**
 * 交易页面功能
 *
 * @remark 
 */



class Transaction extends ApiBase
{
    /**
     * controller 获取交易区列表
     */
    public function area_list($area_id){
        $CurrencyArea = new CurrencyArea();
        return json($CurrencyArea -> area_list($this -> userInfo,$area_id));
    }

    /**
     * controller 获取交易区下的交易币种信息
     */
    public function area_currency(){
        if(Request::instance() -> isPost()){
            $CurrencyArea = new CurrencyArea();
            $data = input('post.');
            $data['token'] = $this -> userInfo;
            return json($CurrencyArea -> area_currency($data));
        }
    }

    /**
     * controller 点击切换到市场/交易
     */
    public function change_page(){
        if(Request::instance() -> isPost()){
            $Trade = new Trade();
            $data = input('post.');
            $data['token'] = $this -> userInfo;
            return json($Trade -> change_page($data));
        }
    }

    /**
     * controller 点击切换 交易状态(买入/卖出/当前委托/历史委托)
     */
    public function change_block(){
        if(Request::instance() -> isPost()){
            $Trade = new Trade();
            $data = input('post.');
            $data['token'] = $this -> userInfo;
            return json($Trade  -> change_block($data));
        }
    }

    /**
     * controller 输入价格数量获取总价/需要/手续费
     */
    public function get_service(){
        if(Request::instance() -> isPost()){
            $Trade = new Trade();
            $data = input('post.');
            $data['token'] = $this -> userInfo;
            return json($Trade -> get_service($data));
        }
    }

    /**
     * controller 买入
     */
    public function Buy(){
        if(Request::instance() -> isPost()) {
            $Trade = new Trade();
            // 获取行情
            $Lib = new Lib();
            $huobi = $Lib -> get_market_tickers();  // 全部symbol的交易行情
            $huobi_data = $huobi['data'];   // 取出信息数组
            return json($Trade -> buy($this -> userInfo, input('post.'),$huobi_data));
        }
    }



}