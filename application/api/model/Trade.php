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
                            $list[$k]['trade_type_text'] = lang('sell');
                            $list[$k]['trade_type_color'] = 'green';
                            break;
                        case 2:
                            $list[$k]['trade_type_text'] = lang('buy');
                            $list[$k]['trade_type_color'] = 'red';
                            break;
                    }
                    // 币种名称
                    $Currency = new Currency();
                    $list[$k]['cur_text'] = $Currency -> get_cur_text($v['cur_id']);
                    // 交易区币种名称
                    $list[$k]['area_text'] = $Currency -> get_cur_text($data['area_id']);
                    // 成交量
                    $list[$k]['volume'] = $v['number'] * $v['price'];
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
                            $list[$k]['trade_type_text'] = lang('sell');
                            $list[$k]['trade_type_color'] = 'green';
                            break;
                        case 2:
                            $list[$k]['trade_type_text'] = lang('buy');
                            $list[$k]['trade_type_color'] = 'red';
                            break;
                    }
                    // 币种名称
                    $Currency = new Currency();
                    $list[$k]['cur_text'] = $Currency -> get_cur_text($v['cur_id']);
                    // 交易区币种名称
                    $list[$k]['area_text'] = $Currency -> get_cur_text($data['area_id']);
                    // 成交量
                    $list[$k]['volume'] = $v['number'] * $v['price'];
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
        $default['need'] = 0;
        $default['all_price'] = 0;
        $default['service'] = 0;
        if(!$data['price'] || !$data['number']){
            return $default;
        }

        $Currency = new Currency();
        $CurrencyArea = new CurrencyArea();
        switch($data['block_type']){
            case 1: // 卖出
                $return['need'] = $data['number'] * (1 + config('SERVICE_SELL'));   // 需要
                $return['all_price'] = $data['number'];     //  总价
                // 获取交易币种名称
                $cur = $CurrencyArea -> trade_pairs($data['currency_area_id']);
                $cur_text = $Currency -> get_cur_text($cur['cur_id']);
                $price = api_currency($cur_text) * $data['number'];
                if($price >= 150){
                    $service = $data['number'] * config('SERVICE_SELL');
                }else{
                    $service = 150 * config('SERVICE_SELL');
                }
                // 获取 锁仓总数据 表判断用户购买是否大于 5000 或 1000
                $Lock_count = new LockCount();
                $data = $Lock_count -> where('uid',$data['token']['id']) -> value('count_number');
                if($data > 5000){
                    $return['service'] = $service * config('FIVE_THOUSAND');
                }elseif($data>=10000){
                    $return['service'] = $service * config('TEM_THOUSAND');
                }else{
                    $return['service'] = $service;
                }

                break;
            case 2: // 买入
                $return['need'] = $data['price'] * $data['number'] * (1 + config('SERVICE_BUY'));   // 需要
                $return['all_price'] = $data['price'] * $data['number'];    // 总价
                // 获取交易币种名称
                $cur = $CurrencyArea -> trade_pairs($data['currency_area_id']);
                $cur_text = $Currency -> get_cur_text($cur['cur_id']);
                $price = api_currency($cur_text) * ($data['price'] * $data['number']);
                if($price >= 150){
                    $service = $data['price'] * $data['number'] * config('SERVICE_SELL');
                }else{
                    $service = 150 * config('SERVICE_SELL');
                }
                // 获取 锁仓总数据 表判断用户购买是否大于 5000 或 1000
                $Lock_count = new LockCount();
                $data = $Lock_count -> where('uid',$data['token']['id']) -> value('count_number');
                if($data > 5000){
                    $return['service'] = $service * config('FIVE_THOUSAND');
                }elseif($data>=10000){
                    $return['service'] = $service * config('TEM_THOUSAND');
                }else{
                    $return['service'] = $service;
                }

                break;
        }

        return $return;
    }

    /**
     * model 挂单买入
     */
    public function buy($user,$data,$huobi_data){
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
        $all_price = $price * $data['number'];  // 获取交易总价

        // 判断用户交易金额是否小于 150 美元(手续费最低按照150美元的0.2%收取)
        if($all_price <= 150){
            $service_charge = 150 * 0.02;
        }else{
            $service_charge = $all_price * 0.02;
        }

        // 判断用户交易手续费是否打折
        $lockCount = new LockCount();
        $lock_count = $lockCount -> where('uid',$user['id']) -> field('count_number,time') -> find();
        $time = time();
        if($lock_count['count_number'] >= 5000 && $time > $lock_count['time']){
            $service = $service_charge * config('FIVE_THOUSAND');
        }else if($lock_count['count_number'] >= 10000 && $time > $lock_count['time']){
            $service = $service_charge * config('TEM_THOUSAND');
        }else{
            $service = $service_charge;
        }
        $in_trade['service_price'] = $service;  // 交易手续费

        // 判断用户账户中的相应的币种是否足够
        $price = $data['price'] * $data['number'] + ($service/$price);//输入的单价*输入的数量+（手续费美元除以该币种对应的美元单价)=总价+该币种应该扣除的手续费=应扣钱
        $UserCur = new UserCur();//实例化资产表
        $maps['uid'] = $user['id'];
        $maps['cur_id'] = $data['cur_id'];
        $assets = $UserCur->where($maps)->value('number');//数量
        if($price <= $assets){
            // 在交易表中插入数据
            Db::startTrans();
            try{
                // 在 交易表 中插入数据
                $return = $this -> allowField(true) -> save($in_trade);
                if(!$return){
                    throw new Exception(lang('os_error'));
                }else{
                    $UserCur->where($maps)->setDec('number',$data['number']);
                }
                Db::commit();
                return ['code' => 1,'msg' => lang('os_success')];
            }catch(\Exception $e){
                Db::rollback();
                return ['code' => 0,'msg' => $e -> getMessage()];
            }
        }else{
            return ['code' => 0,'msg' => lang('not_numebr')];
        }
    }


    // 挂单卖出
    public function sell($user,$data){

        // 设置插入 交易表 的基本信息
        $in_trade['uid'] = $user['id'];
        $in_trade['number'] = $data['number'];
        $in_trade['price'] = $data['price'];
        $in_trade['trade_status'] = 1;
        $in_trade['cur_id'] = $data['cur_id'];
        $in_trade['create_time'] = time();
        $in_trade['update_time'] = time();
        $in_trade['trade_type'] = $data['trade_type'];
        $in_trade['cur_area_id'] = $data['area_id'];
        $in_trade['service_price'] = config('SERVICE_SELL');  // 交易手续费
        $count_number = $data['number']*(1+config('SERVICE_SELL'));//挂单数量*（1+手续费） = 总共需要的钱
        $UserCur = new UserCur();//实例化资产表
        $maps['uid'] = $user['id'];
        $maps['cur_id'] = $data['cur_id'];
        $assets = $UserCur->where($maps)->value('number');//数量
        if($count_number>$assets){
            return ['code' => 0,'msg' => lang('not_numebr')];
        }else{
            $return = $this -> insert($in_trade);//插入
            if($return){
                $UserCur->where($maps)->setDec('number',$count_number);//自减少总共需要的钱
                return ['code' => 1,'msg' => lang('success')];
            }
        }
    }

    /**
     * 执行交易 $user 用户信息 $data会员输入的数据 $type 本人是1买2卖
     * @param $user
     * @param $data
     * @param $type
     * @return array
     * @throws Exception
     * @throws \think\exception\DbException
     */
    public function orders($user,$data,$type){
        // 获取匹配的数据信息(价格和数量相同,可能是卖,也可能是买)
        $map = $this->maps($data,$type);
        // 判断信息是否存在,如果存在则执行交易,并生成订单

        if($map){
            Db::startTrans();
            try{
                $usercur = new UserCur();
                $cur_area = new CurrencyArea();
                $cur = $cur_area->where('id',$data['currency_area_id'])->value('area_id');      // 获取交易对右边币种ID
                $cur_id = $cur_area->where('id',$data['currency_area_id'])->value('cur_id');    // 获取交易对左边币种ID

                $Currency = new Currency();
                // 通过交易类型判断获取交易币种,并获取交易币种名称
                if($type === 2){
                    $cur_name = $Currency -> get_cur_text($data['cur_id']);
                }else{
                    $cur_name = $Currency -> get_cur_text($data['area_id']);
                }
                $count_number = $this->fees($data['price'],$data['number'],$type,$cur_name,$user);  // 算上手续费

                $order = new Order();
                // 本人全额交易(获得数量 $data['number'] 支付数量$count_number['count'])
                $member['order'] = generateOrderNumber();       // 订单编号
                $member['order_number'] = $data['number'];      // 订单数量
                $member['price'] = $data['price'];              // 订单价格
                $member['order_status'] = 3;                    // 订单状态 1待支付 2已支付 3已确认支付
                $member['create_time'] = time();                // 订单创建时间
                $member['pay_time'] = time();                   // 订单支付时间
                $member['done_time'] = time();                  // 订单结束时间
                $member['trade_type'] = $data['trade_type'];    // 交易类型 1出售 2求购
                $member['buyer_id'] = $data['trade_type']==1?$map['uid']:$user['id'];   // 1卖 2买 如果本人状态是卖 那么买方是匹配人ID 卖方是本人
                $member['seller_id'] = $data['trade_type']==1?$user['id']:$map['uid'];  // 1卖 2买 如果本人状态是卖 那么买方是匹配人ID 卖方是本人
                $member['trade_id'] = $map['id'];               // 匹配的交易挂单ID
                $member['cur_id'] = $map['cur_id'];             // 当前交易的虚拟币币种ID
                $member['cur_area_id'] = $map['cur_area_id'];   // 交易区ID
                // 在订单表插入成交数据
                if(!$order->insert($member)){    // 添加数据
                    throw new Exception(lang('sub_order'));
                }
                if(!$usercur->where('uid',$user['id'])->where('cur_id',$cur)->setDec('number',$count_number['count'])){       // 自减算上手续费的数量
                    throw new Exception(lang('service_failed'));
                }
                if(!$usercur->where('uid',$user['id'])->where('cur_id',$cur_id)->setInc('number',$data['number'])){  // 自增输入的数量
                    throw new Exception(lang('add_num_failed'));
                }

                // 修改匹配到的挂单信息
                $match['update_time'] = time();                     // 修改时间
                $match['trade_status'] = 3;                         // 交易完成
                $usercur->where('uid',$map['uid'])->where('cur_id',$cur)->setInc('number',$data['number']);
                if(!$this->where('id',$map['id'])->update($match)){  // 修改匹配到的交易挂单表信息
                    throw new Exception(lang('mod_trade_failed'));
                }

                // 在交易表中插入当前用户交易信息
                $in_trade['uid'] = $user['id'];
                $in_trade['number'] = $data['number'];
                $in_trade['price'] = $data['price'];
                $in_trade['trade_status'] = 3;
                $in_trade['cur_id'] = $data['cur_id'];
                $in_trade['create_time'] = time();
                $in_trade['update_time'] = time();
                $in_trade['trade_type'] = $data['trade_type'];
                $in_trade['cur_area_id'] = $data['area_id'];
                $in_trade['service_price'] = $count_number['fee'];  // 交易手续费
                if(!$this -> insert($in_trade)){  // 插入当前交易挂单表信息
                    throw new Exception(lang('in_trade_failed'));
                }

                Db::commit();
                return ['code' => 1,'msg' => lang('success')];
            }catch(\Exception $e){
                Db::rollback();
                return ['code' => 1,'msg' => $e -> getMessage()];
            }
        }else{
            return ['code' => -5,'data'=> $data,'user' => $user];
        }
    }

    /**
     * 封装查询条件 并返回 查询数据
     * @param $data     基本数据
     * @param $type     交易类型 1卖 2买
     * @return array|false|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function maps($data,$type){
        $map['price'] = $data['price'];                     // 单价等于
        $map['number'] = $data['number'];                   // 数量等于
        $map['trade_status'] = array('in','1,5');           // 查询挂单 或者 部分成交
        $map['cur_area_id'] = $data['currency_area_id'];    // 查询对应货币交易区ID
        $map['trade_type'] = $type==2?1:2;                  // 三元 如果type==2走真trade_type =1 否则走假trade_type=2
        $trade = $this->where($map)->find();                // 获取交易信息
        return $trade;
    }

    /**
     * 计算手续费
     * @param $price        价格
     * @param $number       数量
     * @param $type         交易类型 1卖 2买
     * @param $cur_name     币种名称
     * @param $user         当前用户信息
     * @return float|int
     */
    public function fees($price,$number,$type,$cur_name,$user){
//        echo $price.'-'.$number.'-'.$type.'-'.$cur_name.'-'.$user;exit;
        // 通过币种名称获取当前币种对 USDT 时的价格
        if($cur_name != 'LBCC'){
            $prices = api_currency($cur_name);//该币种的USDT单价 10:1
        }else{
            $order = new Order();
            $order_status = 3;
            $cur_id = 1;    // 交易币
            $prices = $order -> last_order_price($order_status,$cur_id);
            if(!$prices){
                $prices = 1;
            }
        }
        if($type == 1){
            $usdt = $prices * $number;//该币种数量对应的USDT价钱10*num
            if($usdt >= 150){
                $fee = $number * config('SERVICE_SELL');//卖 数量*(1+手续费)
            }else{
                $num = (150-$usdt)/$prices;//(150-该币种数量对应的USDT价钱)/该币种的USDT单价 = 该币种还需要多少数量
                $fee = ($number+$num)*config('SERVICE_SELL');//卖 150*手续费+数量
            }
        }else{
            $usdt = $prices*$number*$price;//该币种数量对应的USDT价钱10*num
            if($usdt >= 150){
                $fee = $price*$number*config('SERVICE_BUY');//买 数量*单价*(1+手续费);
            }else{
                $num = (150-$usdt)/$prices;//(150-该币种数量对应的USDT价钱)/该币种的USDT单价 = 该币种还需要多少数量
                $fee = $price*($number+$num)*config('SERVICE_SELL');//卖 150*手续费+数量
            }
        }
        $Lock_count = new LockCount();
        $data = $Lock_count -> where('uid',$user['id']) -> value('count_number');
        if($data>5000){
            $fee = $fee * config('FIVE_THOUSAND');
        }elseif($data>=10000){
            $fee = $fee * config('TEM_THOUSAND');
        }else{
            $fee = $fee;
        }
        $return['fee'] = $fee;
        $return['count'] = $type==1?$number+$fee:$number*$price+$fee;
        return $return;
    }

	/**
	 * 获取订单记录
	 * @param $user用户信息
	 * @return false|\PDOStatement|string|\think\Collection
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	public function get_trade($user,$status)
	{
		$trade = $this->where(['uid'=>$user['id'],'trade_status'=>$status])->select();
		$return = null;
		foreach ($trade as $k=>$v){
//		    中英“状态”
		    switch ($v['trade_status']){
                case 1:
		            $return[$k]['trade_status'] = lang("挂卖中");
                    break;
                case 2:
                    $return[$k]['trade_status'] = lang("交易中");
                    break;
                case 3:
                    $return[$k]['trade_status'] = lang("交易完成");
                    break;
                case 4:
                    $return[$k]['trade_status'] = lang("挂卖撤销");
                    break;
                default:
                    $return[$k]['trade_status'] = lang("error");
            }
//            中英“类型”
            switch ($v['trade_type']){
                case 1:
                    $return[$k]['color'] = "red" ;
                    $return[$k]['trade_type'] = lang("卖出").Currency::get_name_by_id($v['cur_id']);
                    break;
                case 2:
                    $return[$k]['color'] = "green" ;
                    $return[$k]['trade_type'] = lang("买入").Currency::get_name_by_id($v['cur_id']);
                    break;
                default:
                    $return[$k]['trade_type'] = lang("error");
            }
//            中英“支付类型”
		    switch ($v['payment_method']){
                case 1:
                    $return[$k]['payment_method'] = lang("银行卡");
                    break;
                case 2:
                    $return[$k]['payment_method'] = lang("微信");
                    break;
                case 3:
                    $return[$k]['payment_method'] = lang("支付宝");
                    break;
                default:
                    $return[$k]['payment_method'] = lang("error");

            }
//            删除支付类型
            unset($return[$k]['payment_method']);
			$return[$k]['end_time'] = date("Y-m-d H:i:s",$v['end_time']);
		    $return[$k]['number'] = $v['number'];
		}
		return $return;
	}


    /**
     * 撤销订单
     * @param $order
     * @param $user
     * @return bool
     */
	public function cancel_trade($order,$user)
    {
        if($this->where(['id'=>$order,'uid'=>$user['id']])->update(['trade_status'=>4])){
            return true;
        }
        return false;
    }

}