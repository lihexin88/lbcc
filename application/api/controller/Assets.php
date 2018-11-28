<?php
namespace app\api\controller;
use app\common\controller\ApiBase;
use think\Session;
use think\Request;
use think\Captcha;
use think\Db;

/**
 * 公告功能
 *
 * @remark 
 */


class Assets extends ApiBase
{
    private $UserCur;
    private $Currency;
    public function __construct(\think\Request $request = null) 
    {
        parent::__construct($request);
        $this->UserCur = new \app\api\model\UserCur();
        $this->Currency = new \app\api\model\Currency();
    }

    /**
     * 资产页面信息
     * @param string @p [页数]
     */
    public function index()
    {
        $res = $this->userInfo;//用户信息
        $list = $this->UserCur->usdt_list($res['id']);//用户对应资产
        $cur = $this->Currency->cur_list();//所有币种新信息
        foreach ($cur as $kk => $vv) {//遍历
            if($vv['name'] == 'LBCC'){
                $data[$vv['id']-1]['price'] = 2;
            }else{
                $data[$vv['id']-1]['price'] = api_currency($vv['name']);//调用公共方法
            }
            $data[$vv['id']-1]['coin'] =  $vv['name'];
        }
        ksort($data);
        foreach($data as $k1=>$v1){
            foreach($list as $k2=>$v2){
                if($v1['coin'] == $v2['name']){
                    $data[$k1]['number'] = $v2['number'];
                    $data[$k1]['cur_id'] = $v2['cur_id'];
                  	$data[$k1]['count'] = $v2['number']*$v1['price'];
                  	$data[6]['usdt'] += $data[$k1]['count'];
                  	$data[6]['rmb'] = $data[6]['usdt']*6.9477;
                }   
            }
        }
         return json($data);
    }
}