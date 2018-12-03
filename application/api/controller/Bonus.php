<?php
namespace app\api\controller;
use think\Controller;
use think\Session;
use think\Request;
use think\Captcha;
use think\Db;
/**
 * 公告功能
 *
 * @remark 
 */


class Bonus extends Controller
{
    private $User;
    private $StoData;
    private $Currency;
    private $UserCur;
    private $Order;
  	private $Config;
    public function __construct(\think\Request $request = null) 
    {
        parent::__construct($request);
        $this->User = new \app\api\model\User();
        $this->StoData = new \app\api\model\StoData();
        $this->Currency = new \app\api\model\Currency();
        $this->UserCur = new \app\api\model\UserCur();
        $this->Order = new \app\api\model\Order();
      	$this->Config = new \app\api\model\Config();
    }
    // EARNINGS_RATIO收益率
    public function index()
    {
        $user = $this->User->select();
        foreach ($user as $k => $v) {
            $sto = $this->StoData->where('uid',$v['id'])->where('status',1)->select();
          	if($sto){
            	foreach ($sto as $kk => $vv) {
                    $name = $this->Currency->where('id',$vv['cur_id'])->value('name');
                    $con = $this->Config->where('key','EARNINGS_RATIO')->value('value');
                    $bonus_num = $vv['number']*$con;//每日分红
                    if($name == 'LBCC'){
                        $bonus = 1*$bonus_num;
                        $this->StoData->where('id',$vv['id'])->setInc('bonus',$bonus);//每日分红自增
                        $this->UserCur->where('uid',$vv['uid'])->where('cur_id',1)->setInc('number',$bonus);//进资产表
                    }else{
                        $lbcc = $this->Order->last_order_area_price(3,5);
                        if(!$lbcc){
                            $lbcc = 10;
                        }
                        $price = api_currency($name)/$lbcc;//其他币种 除以 平台币 等于 比例
                        $bonus = $price*$bonus_num;
                        $this->StoData->where('id',$vv['id'])->setInc('bonus',$bonus);//每日分红自增
                        $this->UserCur->where('uid',$vv['uid'])->where('cur_id',1)->setInc('number',$bonus);//进资产表
                    }
                    $this->bonus($v['parent_id'],$bonus,0);
                }
            }
        }
    }
    //上级返利 $parent_id 父级ID $bonus 分红数 $times 递归次数
    public function bonus($parent_id,$bonus,$times)
    {
      if($parent_id !== null){
      		if($times < 10){
                $user = $this->User->where('id',$parent_id)->find();
                if(empty($user)){
                    return array('msg'=>'没有上级信息不进行返利');
                }else{
                    if($user['level'] == 0){
                        return array('msg'=>'非VIP不会进行返利');
                    }else{
                        if($times == 0){
                            $con = $this->Config->where('key','DIRECT')->value('value');
                            $number = $bonus*$con;//只会给上一级返利
                            $this->UserCur->where('uid',$parent_id)->where('cur_id',1)->setInc('number',$number);
                        }
                        $times++;//递归次数+1
                        if($user['level'] == 1){
                            $count = $this->User->where('parent_id',$user['id'])->count();
                            if($count > 1 && $count <10){
                              	$con = $this->Config->where('key','RANGE')->value('value');
                                $number = $bonus*$con;//2-10人
                            }else{
                                $number = 0;
                            }
                        }elseif($user['level'] == 2){
                            $con = $this->Config->where('key','COPPER')->value('value');
                          	$number = $bonus*$con;
                        }elseif($user['level'] == 3){
                            $con = $this->Config->where('key','SILVER')->value('value');
                          	$number = $bonus*$con;
                        }elseif($user['level'] == 4){
                            $con = $this->Config->where('key','GOLD')->value('value');
                          	$number = $bonus*$con;
                        }else{
                            $con = $this->Config->where('key','EMPEROR')->value('value');
                          	$number = $bonus*$con;
                        }
                        $this->UserCur->where('uid',$parent_id)->where('cur_id',1)->setInc('number',$number);
                        $this->bonus($user['parent_id'],$bonus,$times);
                    }
                }
            }else{
                return array('msg'=>'已经返利10轮');
            }		
      }
    }
  
  //KLine定时 只定时 LBCC交易区
    public function kline()
    {
        $area = db('currency_area')->where('area_id',1)->select();
        $time = time();
        $old_time = time()-60;
        foreach ($area as $k => $v) {
            $open = db('kline')->where('cur_area_id',$v['id'])->order('time desc')->find();
            if($open){
                $data['open_price'] = $open['open_price']==null?0:$open['close_price'];//最后一个收盘价等于最新开盘价
            }else{
            	$data['open_price'] = 0;
            }
            $list = db('order')->where('cur_area_id',$v['id'])->where('create_time','between',[$old_time,$time])->order('create_time desc')->find();
            if($list){
                $data['close_price'] = $list['price'];
            }else{
                $data['close_price'] = $data['open_price']==null?0:$data['open_price'];
            }
            $max = db('order')->where('cur_area_id',$v['id'])->where('create_time','between',[$old_time,$time])->max('price');
            if($max){
                $data['max_price'] = $max;
            }else{
                $data['max_price'] = $open['max_price']==null?0:$open['max_price'];
            }
            $min = db('order')->where('cur_area_id',$v['id'])->where('create_time','between',[$old_time,$time])->min('price');
            if($min){
                $data['min_price'] = $min;
            }else{
                $data['min_price'] = $open['min_price']==null?0:$open['min_price'];
            }
            $data['vol'] = db('order')->where('cur_area_id',$v['id'])->where('create_time','between',[$old_time,$time])->count();
            $data['cur_area_id'] = $v['id'];
            $data['time'] = time();
            db('kline')->insert($data);
        }
    }
}