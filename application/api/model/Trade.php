<?php
namespace app\api\model;


use think\Exception;
use think\Model;
use think\Db;

class Trade extends Model
{
    /**
     * model 点击切换到市场/交易
     */
    public function change_page($data){
        if(!$data['page_type']){
            $data['page_type'] = 1;
        }
        if(!$data['area_id']){
            $data['area_id'] = 1;
        }
        $CurrencyArea = new CurrencyArea();
        if(!$data['currency_area_id']){
            $data['currency_area_id'] = $CurrencyArea -> where('area_id',$data['area_id']) -> order('id ASC') -> value('id');
        }
        // 获取交易区信息
        $area_where['area_id'] = $data['area_id'];
        $area_where['id'] = $data['currency_area_id'];
        $result = $CurrencyArea -> where($area_where) -> find();
        // 获取用户余额
        $UserCur = new UserCur();
        $userCur_where['uid'] = $data['token']['id'];
        $userCur_where['cur_id'] = $result['cur_id'];
        $result['user_cur'] = $UserCur -> where($userCur_where) -> value('number');
        $Currency = new Currency();
        $result['cur_text'] = $Currency -> get_cur_text($result['cur_id']);
        $result['area_text'] = $Currency -> get_cur_text($result['area_id']);
        $result['page_type'] = $data['page_type'];
        unset($result['cur_id']);
        return $result;
    }

    /**
     * model 点击切换 买入/卖出/当前委托/历史委托
     */
    public function change_block($data){
        if(!$data['block_type']){
            $data['block_type'] = 1;
        }
        if(!$data['area_id']){
            $data['area_id'] = 1;
        }
        $CurrencyArea = new CurrencyArea();
        if(!$data['currency_area_id']){
            $data['currency_area_id'] = $CurrencyArea -> where('area_id',$data['area_id']) -> order('id ASC') -> value('id');
        }

        switch($data['block_type']) {
            case 3: // 查询当前委托
                $current_where['uid'] = $data['token']['id'];
                $current_where['trade_status'] = 1;
                $current_where['cur_id'] = $CurrencyArea -> where('id', $data['currency_area_id'])->value('cur_id');
                $list = $this -> where($current_where) -> field('id,number,price,trade_type,cur_id,create_time,cur_area_id') -> select();
                foreach($list as $k => $v){
                    // 交易类型
                    switch($v['trade_type']){
                        case 1:
                            $list[$k]['trade_type_text'] = '卖出';
                            $list[$k]['trade_type_color'] = 'green';
                            break;
                        case 2:
                            $list[$k]['trade_type_text'] = '买入';
                            $list[$k]['trade_type_color'] = 'red';
                            break;
                    }
                    // 币种名称
                    $Currency = new Currency();
                    $list[$k]['cur_text'] = $Currency -> get_cur_text($v['cur_id']);
                }
                break;
            case 4: // 查询历史委托
                $history_where['uid'] = $data['token']['id'];
                $history_where['trade_status'] = 3;
                $history_where['cur_id'] = $CurrencyArea -> where('id',$data['currency_area_id']) -> value('cur_id');
                $list = $this -> where($history_where) -> field('id,number,price,trade_type,cur_id,create_time,cur_area_id') -> select();
                foreach($list as $k => $v){
                    // 交易类型
                    switch($v['trade_type']){
                        case 1:
                            $list[$k]['trade_type_text'] = '卖出';
                            $list[$k]['trade_type_color'] = 'green';
                            break;
                        case 2:
                            $list[$k]['trade_type_text'] = '买入';
                            $list[$k]['trade_type_color'] = 'red';
                            break;
                    }
                    // 币种名称
                    $Currency = new Currency();
                    $list[$k]['cur_text'] = $Currency -> get_cur_text($v['cur_id']);
                }
                break;
        }

        return $list;
    }

    /**
     * model 输入价格数量获取总价/需要/手续费
     */
    public function get_service($data){
        if(!$data['currency_area_id']){
            return ['code' => 0,'msg' => lang('not_cur_area')];
        }
        if(!$data['price']){
            return ['code' => 0,'msg' => lang('input_price')];
        }
        if(!$data['number']){
            return ['code' => 0,'msg' => lang('input_number')];
        }

        $Currency = new Currency();
        $CurrencyArea = new CurrencyArea();
        switch($data['block_type']){
            case 1: // 卖出

                $return['need'] = $data['number'] * (1 + config('SERVICE_SELL'));
                $return['all_price'] = $data['number'];
                // 获取交易币种名称
                $cur = $CurrencyArea -> trade_pairs($data['currency_area_id']);
                $cur_text = $Currency -> get_cur_text($cur['cur_id']);
                $price = api_currency($cur_text) * $data['number'];
                if($price >= 150){
                    $return['service'] = $data['number'] * config('SERVICE_SELL');
                }else{
                    $return['service'] = 150 * config('SERVICE_SELL');
                }

                break;
            case 2: // 买入
                $return['service'] = ($data['price'] * $data['number']) * config('SERVICE_BUY');
                $return['need'] = $data['price'] * $data['number'] * (1 + config('SERVICE_BUY'));
                $return['all_price'] = $data['price'] * $data['number'];
                // 获取交易币种名称
                $cur = $CurrencyArea -> trade_pairs($data['currency_area_id']);
                $cur_text = $Currency -> get_cur_text($cur['cur_id']);
                $price = api_currency($cur_text) * ($data['price'] * $data['number']);
                if($price >= 150){
                    $return['service'] = $data['number'] * config('SERVICE_SELL');
                }else{
                    $return['service'] = 150 * config('SERVICE_SELL');
                }

                break;
        }

    }

    /**
     * model 挂单买入
     */
    public function buy($user,$data,$huobi_data){
        if(!$data['cur_id']){
            return ['code' => 0,'msg' => lang('not_cur')];
        }
        if(!$data['area_id']){
            return ['code' => 0,'msg' => lang('not_area')];
        }
        if(!$data['trade_type']){
            return ['code' => 0,'msg' => lang('not_trade_type')];
        }
        if(!$data['price']){
            return ['code' => 0,'msg' => lang('input_price')];
        }
        if(!$data['number']){
            return ['code' => 0,'msg' => lang('input_number')];
        }

        // 设置插入 交易表 的基本信息
        $in_trade['uid'] = $user['id'];
        $in_trade['number'] = $data['number'];
        $in_trade['price'] = $data['price'];
        $in_trade['trade_status'] = 1;
        $in_trade['cur_id'] = $data['cur_id'];
        $in_trade['create_time'] = time();
        $in_trade['trade_type'] = $data['trade_type'];
        $in_trade['cur_area_id'] = $data['area_id'];

        // 获取交易币种名称
        $currency = new Currency();
        $cur = $currency -> get_cur_text($data['cur_id']);   // 获取币种名称
        $cur_name = strtolower($cur);   // 将币种名称转换为小写字母
        // 判断不同币种转换为 usdt 时的价格
        if($cur_name == 'doge'){    // 判断为 doge 币时
            $url = "http://api.zb.cn/data/v1/kline?market=doge_usdt&type=1min&size=1";
            $zb_data =  json_decode(file_get_contents($url), true);
            $price = $zb_data['data'][0][4];
        }else{  // 判断为其它的币种时
            $order = new Order();
            // 判断交易币种为 usdt ,交易区ID为6
            if($cur_name == 'usdt' && $data['area_id'] == 6){
                $order_status = 3;
                $cur_id = 6;    // 交易币
                $price = $order -> last_order_price($order_status,$cur_id);
            }
            // 判断交易币种为 lbcc ,交易区ID为5
            if($cur_name == 'lbcc' && $data['area_id'] == 5){
                $order_status = 3;
                $cur_id = 1;    // 交易币
                $price = $order -> last_order_price($order_status,$cur_id);
            }
            // 将用户交易币种的价格转为美元
            foreach ($huobi_data as $k => $v) { // 遍历
                $cur_area = $cur_name.'usdt'; // 拼接usdt
                if($v['symbol'] == $cur_area){
                    $price = $v['close'];  // 最新单价 = 最新收盘价
                }
            }
        }
        $all_price = $data['price'] * $data['number'];  // 获取交易总价

        // 判断用户交易金额是否小于 150 美元(手续费最低按照150美元的0.2%收取)
        if($all_price <= 150){
            $service_charge = 150 * 0.02;
        }else{
            $service_charge = $all_price * 0.02;
        }

        // 判断用户交易手续费是否打折
        $lookCount = new lookCount();
        $look_count = $lookCount -> where('uid',$user['id']) -> field('count_number,time') -> find();
        $time = time();
        if($look_count['count_number'] >= 5000 && $time > $look_count['time']){
            $service = $service_charge * config('FIVE_THOUSAND');
        }else if($look_count['count_number'] >= 10000 && $time > $look_count['time']){
            $service = $service_charge * config('TEM_THOUSAND');
        }else{
            $service = $service_charge;
        }

        // 判断用户账户中的相应的币种是否足够
        $price = $data['price'] * $data['number'] + ($service/$price);

        // 在交易表中插入数据
        Db::startTrans();
        try{
            // 在 交易表 中插入数据
            if(!$this -> allowField(true) -> save($in_trade)){
                throw new Exception(lang('os_error'));
            }





            Db::commit();
            return ['code' => 1,'msg' => lang('os_success')];
        }catch(\Exception $e){
            Db::rollback();
            return ['code' => 0,'msg' => $e -> getMessage()];
        }

    }
}