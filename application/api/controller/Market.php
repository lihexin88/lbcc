<?php
namespace app\api\Controller;
use app\common\controller\ApiBase;
use app\api\controller\Lib;
use think\Session;
use think\Request;
use think\Captcha;
use think\Db;


class Market extends ApiBase
{
	private $Currency;
    public function __construct(\think\Request $request = null) 
    {
        parent::__construct($request);
        $this->Currency = new \app\api\model\Currency();
      	$this->Order = new \app\api\model\Order();
    }

    /**
		行情列表
		id 用户id
    **/
	public function index()
	{
		$list = $this->Currency->field('id,name,icon')->select();
		foreach ($list as $key => $value) {
          	$list[$key]['icon'] = config('WEBSITE').$value['icon'];
			if($value['name'] == "LBCC"){
              	$order = $this->Order->last_order_area_prices(3,5);
              	if($order){
                  	if($order[0]['price'] && $order[1]['price']){
                    	$price = $order[0]['price'];
                        $gain = $order[0]['price']-$order[1]['price'];
                        $prices =$gain/$order[1]['price'];
                        $list[$key]['money'] = array('price'=>$price,'rmb'=>$price*6.9545,'gain'=>$prices);
                    }else{
                    	$list[$key]['money'] = array('price'=>0,'rmb'=>0,'gain'=>0); 
                    }
                }else{
                	$list[$key]['money'] = array('price'=>0,'rmb'=>0,'gain'=>0); 
                }
			}else{
				$list[$key]['money'] = $this->api($value['name']);
			}
          	$list[$key]['money']['gain'] = $zengfu;
		}
		$r = rtn(1,'',$list);
		return $r;
	}
	public function api($cur){
	    $currency = strtolower($cur);//先转小写
	    if($currency == 'usdt'){
	        $price['price'] = 1;
          	$price['rmb'] = 6.9545;
          	$price['gain']  = 0;
	    }else if($currency == 'doge'){
	        $url = "http://api.zb.cn/data/v1/kline?market=doge_usdt&type=1min&size=2";
	        $zb_data =  json_decode(file_get_contents($url), true);
	        $price['price'] = $zb_data['data'][0][4];
          	$price['rmb']	= $zb_data['data'][0][4]*6.9545;
          	$gain = $zb_data['data'][0][4]-$zb_data['data'][0][1];
          	if($gain< 0){
            	$price['gain'] = ($price['price']-$zb_data['data'][0][1])/$zb_data['data'][0][1];
            }else{
            	$price['gain'] = ($price['price']-$zb_data['data'][0][1])/$zb_data['data'][0][1];
            }
	    }else{
	        $Lib = new Lib();
	        $huobi = $Lib->get_market_tickers();//全部symbol的交易行情
	        $huobi_data = $huobi['data'];//取出信息数组
	        foreach ($huobi_data as $k => $v) {//遍历
	            $cur_area = $currency.'usdt';//拼接usdt
	            if($v['symbol'] == $cur_area){
	                $price['price'] = $v['close'];//最新单价 = 最新收盘价
                  	$price['rmb']	= $v['close']*6.9545;
                  	$gain = $price['price']-$v['open'];
                    if($gain< 0){
                        $price['gain'] = ($price['price']-$v['open'])/$v['open'];
                    }else
                        $price['gain'] = ($price['price']-$v['open'])/$v['open'];
                    }
	            }
	        }
	    return $price;
	}
}