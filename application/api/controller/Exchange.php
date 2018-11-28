<?php
namespace app\api\controller;
use app\common\controller\ApiBase;
use think\Session;
use think\Request;
use think\Captcha;
use think\Db;

/**
 * 兑换功能
 *
 * @remark 
 */
class Exchange extends ApiBase
{
    private $UserCur;
    private $Currency;
    private $LockCount;
    public function __construct(\think\Request $request = null) 
    {
        parent::__construct($request);
        $this->UserCur = new \app\api\model\UserCur();
        $this->Currency = new \app\api\model\Currency();
        $this->LockCount = new \app\api\model\LockCount();
    }
    //根据币种ID 判断返回信息
    public function index()
    {
       $res = $this->userInfo;//用户信息
       $cur_id = trim(input('cur_id'));//币种ID
       $currency_id = trim(input('currency_id'));//LBCC需要兑换的币种ID
       if($cur_id == 1){
            $name = $this->Currency->where(['id'=>1])->value('name');
            $list['cur']['id'] = $cur_id;
            $list['cur']['cur_name'] = $name;
            $list['cur']['number'] = $this->UserCur->where(['uid'=>$res['id'],'cur_id'=>$cur_id])->value('number');
            $list['cur']['cur_ratio'] = 2;
            if($currency_id){
                $datas = $this->Currency->where('id',$currency_id)->field('id,name')->select();//如果存在查询币种
                $map['id'] = array('not in',"$cur_id,$currency_id");
            }else{
                $datas = $this->Currency->where('id','neq',$cur_id)->field('id,name')->select();//不存在查询所有
                $map['id'] = array('not in',"1,2");
            }
            $data = $this->Currency->where($map)->field('id,name')->select();//不存在查询所有
            $list['currency']['id'] = $datas[0]['id'];
            $list['currency']['currency_name'] = $datas[0]['name'];
            $list['currency']['number'] = $this->UserCur->where(['uid'=>$res['id'],'cur_id'=>$datas[0]['id']])->value('number');
            $list['currency']['currency_ratio'] = api_currency($datas[0]['name']);
            $list['data'] = $data;
       }else{
            $list['cur']['id'] = $cur_id;
            $list['cur']['cur_name'] = $this->Currency->where(['id'=>$cur_id])->value('name');
            $list['cur']['number'] = $this->UserCur->where(['uid'=>$res['id'],'cur_id'=>$cur_id])->value('number');
            $list['currency']['id'] = 1;
            $list['currency']['currency_name'] = 'LBCC';
            $list['currency']['number'] = $this->UserCur->where(['uid'=>$res['id'],'cur_id'=>1])->value('number');
            $list['currency']['currency_ratio'] = 2;
            $list['cur']['cur_ratio'] = api_currency($datas[0]['name']);
       }
       $list['fee'] = config('EXCHANGE_FEE');
       $list['currency']['currency_ratio'] = $list['currency']['currency_ratio']/$list['cur']['cur_ratio'];
       $list['cur']['cur_ratio'] = 1;
       if($list){
         $r = rtn(1,lang('success'),$list);
       }else{
         $r = rtn(1,lang('null'));
       }
       return $r;
    }
    //执行兑换
    public function exchange()
    {
        $res = $this->userInfo;//用户信息
        $cur_id = trim(input('cur_id'));//币种ID
        $cur_ratio = trim(input('cur_ratio'));//该币种的汇率
        $currency_id = trim(input('currency_id'));//该币种需要兑换的币种ID
        $currency_ratio = trim(input('currency_ratio'));//该币种需要兑换的币种汇率
        $number = trim(input('number'));//数量
        $pwd = trim(input('pwd'));//支付密码
        if(!$cur_id || !$cur_ratio || !$currency_id || !$currency_ratio || !$number || !$pwd){
            $r = rtn(1,lang('not_null'));
        }elseif(encrypt($pwd) !== $res['payment_password']){
            $r = rtn(0,lang('not_password'));
        }else{
            //需要兑换的币种增加数量 币种减少数量
            $fee = config('EXCHANGE_FEE');
            $look_count = $this->LockCount -> where('uid',$res['id']) -> field('count_number,time') -> find();
            $time = time();
            if($look_count['count_number'] >= 5000 && $time > $look_count['time']){
                $service = $fee * config('FIVE_THOUSAND');
            }else if($look_count['count_number'] >= 10000 && $time > $look_count['time']){
                $service = $fee * config('TEM_THOUSAND');
            }else{
                $service = $fee;
            }
            $numbers = $number*$currency_ratio/$cur_ratio;//需要减少的数量 = 该币种需要兑换的币种汇率*数量/该币种的汇率*(1-手续费)
            $list = $this->UserCur->where('uid',$res['id'])->where('cur_id',$cur_id)->setDec('number',$numbers);
            $numberss = $number*(1-$service);
            $data = $this->UserCur->where('uid',$res['id'])->where('cur_id',$currency_id)->setInc('number',$numberss);
            if($list && $data){
                $r = rtn(1,lang('success'));
            }else{
                $r = rtn(1,lang('error'));
            }
        }
        return $r;
    }
}