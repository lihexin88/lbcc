<?php
namespace app\api\controller;
use app\api\model\Currency;
use app\common\controller\ApiBase;
use think\Session;
use think\Request;
use think\Db;
use app\api\model\Trade;
use app\api\model\CurrencyArea;
use app\api\model\Order;

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
     * 本人买入 匹配人是卖出 本人卖出 匹配人是买入
     * @return false|string|\think\response\Json
     */
    public function user_order(){
        if(Request::instance() -> isPost()) {
            $data = input('post.');
            if(!$data['number'] || !$data['price'] || !$data['cur_id'] || !$data['area_id'] || !$data['trade_type'] || !$data['currency_area_id']){
                $r = rtn(-1,lang(not_null));
            }else{
                $Trade = new Trade();
                $return = $Trade->orders($this->userInfo,$data,$data['trade_type']);
                if($return['code'] == -5){
                    //如果状态是1 走挂卖 否则 走挂买
                    $r = json($return['data']['trade_type']==1?$this->Sell($return['data'],$return['user']):$this->Buy($return['data'],$return['user']));
                }else{
                    $r = json($return);
                }
            }
        }
        return $r;
    }

    /**
     * controller 执行挂单买入
     */
    public function Buy($data,$user){
        $Trade = new Trade();
        // 获取行情
        $Lib = new Lib();
        $huobi = $Lib -> get_market_tickers();  // 全部symbol的交易行情
        $huobi_data = $huobi['data'];   // 取出信息数组
        $msg = $Trade -> buy($user, $data,$huobi_data);
        return $msg;
    }

    /**
     * controller 执行挂单卖出
     */
    public function Sell($data,$user){
        $Trade = new Trade();
        $msg = $Trade->sell($user,$data);
        return $msg;
    }

}